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
    },
  },

  setup(props) {
    const inputValue = reactive({ collection: [{val: 0}, {val: 0}
  ]
  })
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntryHelper = inject('setFormEntry')
    const setFormEntry = () => {
      setFormEntryHelper({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: 'CounterGroup',
        value: {
          fieldname: props.data.fieldname,
          userInput: inputValue.collection.map((val, valIndex) => {
            return {
              value: val.val,
              name: props.data.inputs[valIndex].label,
            }
          }),
        },
        type: props.data.acf_fc_layout,
      })
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
            }
          }
        })  
      }

      return rules
    })

    const v = useVuelidate()

    const updateInputValue = (type, index) => {
      checkValue()
      if (inputValue.collection[index].val >= 0 && inputValue.collection[index].val < Number(props.data.inputs[index].max_val) ) {
        if (type === 'increment') {
          inputValue.collection[index].val += 1
        } else {
          inputValue.collection[index].val -= 1
        }
      } else {
        inputValue.collection[index].val = 0
      }

      setFormEntry()
    }

    watch(inputValue.collection, (arr) => {
      // Control max value handling
      arr.forEach((val, valI) => {
        if (val.val > props.data.inputs[valI].max_val) {
          arr[valI].val = Number(props.data.inputs[valI].max_val)
        } else if (val.val < 0) {
          arr[valI].val = 0
        }
      })
    }, { deep: true})

    onMounted(() => {
      if (storeEntry.value) {
        inputValue.collection = storeEntry.value['value'].userInput.map(input => ({ val: Number(input.value) }))
      } else {
        setFormEntry()
      }
    })

    const checkValue = () => {
      v.value.$validate()
    }

    return {
      value: inputValue,
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
      v-for="(counter, counterIndex) in value.collection"
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
  </div>
</template>
