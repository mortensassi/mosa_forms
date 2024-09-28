<script>
import {useVuelidate} from '@vuelidate/core'
import { ValidateEach } from '@vuelidate/components'
import {required, numeric, helpers, minValue} from '@vuelidate/validators'
import {computed, inject, onMounted, ref, reactive, watch} from 'vue'
import store from '@/store'

export default {
  name: 'FormCounter',
  components: { ValidateEach },
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: null
    },
    fieldKey: {
      type: String,
      default: ''
    },
    realIndex: {
      type: Number,
      default: null
    },
    stepGroupIndex: {
      type: Number,
      default: null
    }
  },

  setup(props) {
    const inputValue = reactive([{val: ref(0)}, {val: ref(0)}])
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntryHelper = inject('setFormEntry')
    const errorMessage = ref('')
    const isExceedingLimit = ref(false)

    const apartmentSizeConditions = {
      '1-2': { maxValue: 3.5, strict: true },
      '2': { maxValue: 3.5, strict: false },
      '2-3': { maxValue: 4, strict: true },
      '3': { maxValue: 4.5, strict: false },
      '3-4': { maxValue: 5.5, strict: true },
      '4': { maxValue: 6, strict: false },
      '4-5': { maxValue: 6.5, strict: true },
      '>5': { maxValue: Infinity, strict: true }
    }


    const effectiveMaxValue = computed(() => {
      const minRooms = getMinRoomsValue(store.state.form.entries, 'minRooms')
      const condition = apartmentSizeConditions[minRooms] || { maxValue: Infinity, strict: false }
      return condition.maxValue
    })

    const isStrictComparison = computed(() => {
      const minRooms = getMinRoomsValue(store.state.form.entries, 'minRooms')
      const condition = apartmentSizeConditions[minRooms] || { maxValue: Infinity, strict: false }
      return condition.strict
    })

    function getMinRoomsValue(formEntries, fieldKey) {
      for (let step of formEntries.steps) {
        for (let group of step.groups) {
          for (let field of group.fields) {
            if (field.value && field.value.fieldname === fieldKey) {
              return field.value.userInput.choice;
            }
          }
        }
      }
      return null;
    }

    const setFormEntry = () => {
      setFormEntryHelper({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: 'CounterGroup',
        value: {
          fieldname: props.data.fieldname,
          userInput: inputValue.map((val, valIndex) => {
            return {
              value: val.val,
              name: props.data.inputs[valIndex].label,
            }
          }),
        },
        type: props.data.acf_fc_layout,
      })
    }

    const calculateTotalValue = () => {
      return inputValue[0].val + (inputValue[1].val * 0.75)
    }

    const validateValues = () => {
      const totalValue = calculateTotalValue()
      if (isStrictComparison.value ? totalValue >= effectiveMaxValue.value : totalValue > effectiveMaxValue.value) {
        errorMessage.value = `Die angegebene Personenanzahl ist zu groß für die ausgewählte Wohnungsgröße. Bitte wählen Sie für diese Personenanzahl eine größere Wohnung.`
        isExceedingLimit.value = true
        return false
      } else {
        errorMessage.value = ''
        isExceedingLimit.value = false
        return true
      }
    }

    const validationRules = computed(() => {
      const rules = []

      if (props.data.is_required) {
        props.data.inputs.forEach((input, i) => {
          rules[i] = {
            val: {
              required: helpers.withMessage(props.data.error_message || 'Fehler', required),
              numeric: helpers.withMessage(props.data.error_message || 'Fehler', numeric),
              minValue: helpers.withMessage(props.data.error_message || 'Fehler', minValue(props.data.inputs[i].min_val)),
              notExceedingLimit: helpers.withMessage(
                ``,
                () => !isExceedingLimit.value
              ),
            }
          }
        })
      }

      return rules
    })

    const v = useVuelidate(validationRules, inputValue)

    const updateInputValue = (type, index) => {
      const newValue = type === 'increment' ? inputValue[index].val + 1 : inputValue[index].val - 1
      if (newValue >= 0) {
        inputValue[index].val = newValue
      }

      validateValues()
      v.value.$validate()
      setFormEntry()
    }

    watch(inputValue, (arr) => {
      // Control max value handling
      arr.forEach((val, valI) => {
        if (val.val < 0) {
          arr[valI].val = 0
        }
      })
      validateValues()
      v.value.$validate()
      setFormEntry()
    }, { deep: true})

    onMounted(() => {
      if (storeEntry.value) {
        storeEntry.value['value'].userInput.forEach((item, itemIndex) => {
          inputValue[itemIndex].val = item.value
        })
      } else {
        setFormEntry()
      }
      validateValues()
      v.value.$validate()
    })

    const checkValue = () => {
      validateValues()
      v.value.$validate()
      setFormEntry()
    }

    return {
      inputValue,
      errorMessage,
      validateValues,
      updateInputValue,
      storeEntry,
      validationRules,
      v,
      checkValue,
      effectiveMaxValue,
      calculateTotalValue,
      isStrictComparison,
      isExceedingLimit
    }
  }
}
</script>


<template>
  <div class="msf-input-wrap">
    <ValidateEach
      v-for="(counter, counterIndex) in inputValue"
      :key="`Counter-${index}-input-${counterIndex}`"
      :state="counter"
      :rules="validationRules[counterIndex]"
    >
      <template #default="{ v }">
        <div
          class="msf-input msf-input--counter"
          :class="{'c-input--error' : v.val.$errors.length}"
        >
          <label
            :for="`Counter-${index}-input-${counterIndex}`"
            class="msf-input__label msf-input__label--counter c-input__label"
          >{{ data.inputs[counterIndex].label }}
            <span
              v-if="data.is_required"
              class="c-txt c-txt--highlight"
            >*</span></label>
          <div class="msf-input-wrap">
            <button
              class="c-btn c-btn--is-icon c-btn--has-icon msf-input__btn"
              @click="updateInputValue('decrement', counterIndex)"
            >
              <span class="c-btn__label is-sr-only">Eine Person weniger</span>
              <svg class="c-btn__icon"><use xlink:href="#icon-minus" /></svg>
            </button>
            <input
              :id="`Counter-${index}-input-${counterIndex}`"
              v-model.number="v.val.$model"
              type="number"
              :min="data.inputs[counterIndex].min_val"
              :max="data.inputs[counterIndex].max_val"
              class="c-input__control msf-input__control msf-input__control--count"
              :required="data.inputs[counterIndex].is_required"
              @change="checkValue"
            >
            <button
              class="c-btn c-btn--is-icon c-btn--has-icon msf-input__btn"
              @click="updateInputValue('increment', counterIndex)"
            >
              <span class="c-btn__label is-sr-only">Eine Person mehr</span>
              <svg class="c-btn__icon"><use xlink:href="#icon-plus" /></svg>
            </button>
          </div>
          <div v-if="v.val.$errors">
            <span
              v-for="(error, errorIndex) in v.val.$errors"
              :key="errorIndex"
              class="'c-input__validation', c-input__validation--error msf-input__validation"
            >
              {{ error.$message }}
            </span>
          </div>
        </div>
      </template>
    </ValidateEach>
    <div
      v-if="errorMessage"
      class="msf-input__validation msf-input__validation--count msf-input__validation--error"
    >
      {{ errorMessage }}
    </div>
  </div>
</template>
