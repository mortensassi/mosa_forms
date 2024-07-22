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


    const effectiveMaxValue = computed(() => {
      return props.maxValue || getCalculatedMaxValue()
    })

    function getCalculatedMaxValue() {
      let maxValue = getMinRoomsValue(store.state.form.entries, 'minRooms')
      if (maxValue) {
        switch (maxValue) {
          case '1-2': return 4;
          case '2': return 4;
          case '2-3': return 4;
          case '3': return 5;
          case '3-4': return 6;
          case '4': return 6;
          case '4-5': return 7;
          case '>5': return 99;
          default: return 99;
        }
      }
      return 99; // Default max value if nothing else is specified
    }

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

    const validateValues = () => {
      const totalValue = inputValue.reduce((sum, input) => sum + input.val, 0)
      if (totalValue > effectiveMaxValue.value) {
        errorMessage.value = `Die angegebene Personenanzahl ist zu groß für die ausgewählte Wohnungsgröße. Bitte wählen Sie für diese Personenanzahl eine größere Wohnung.`
        return false
      } else {
        errorMessage.value = ''
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
              maxValue: helpers.withMessage(``, (value) => {
                const totalValue = inputValue.reduce((sum, input, index) => sum + (index === i ? value : input.val), 0)
                return totalValue <= effectiveMaxValue.value
              })
            }
          }
        })
      }

      return rules
    })

    const v = useVuelidate(validationRules, inputValue)

    const updateInputValue = (type, index) => {
      checkValue()
      if (inputValue[index].val >= 0 && inputValue[index].val < Number(props.data.inputs[index].max_val) ) {
        if (type === 'increment') {
          inputValue[index].val += 1
        } else {
          inputValue[index].val -= 1
        }
      } else {
        inputValue[index].val = 0
      }

      setFormEntry()
    }

    watch(inputValue, (arr) => {
      // Control max value handling
      arr.forEach((val, valI) => {
        if (val.val > props.data.inputs[valI].max_val) {
          arr[valI].val = Number(props.data.inputs[valI].max_val)
        } else if (val.val < 0) {
          arr[valI].val = 0
        }
      })
      validateValues()
    }, { deep: true})

    onMounted(() => {
      if (storeEntry.value) {
        storeEntry.value['value'].userInput.forEach((item, itemIndex) => {
          inputValue[itemIndex].val = item.value
        })
      } else {
        setFormEntry()
      }
      v.value.$validate()
    })

    const checkValue = () => {
      v.value.$validate()
    }

    return {
      inputValue,
      errorMessage,
      validateValues,
      updateInputValue,
      storeEntry,
      validationRules,
      v,
      checkValue
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
