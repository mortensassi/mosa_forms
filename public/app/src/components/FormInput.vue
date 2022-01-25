<script>
import {useVuelidate} from '@vuelidate/core'
import {required, email, helpers, numeric, minLength} from '@vuelidate/validators'
import store from '@/store'
import {inject, onMounted, ref, watch, reactive} from 'vue'
import {h, computed} from 'vue'

export default {
  name: 'FormInput',
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: ''
    },
    inputIndex: {
      type: Number,
      default: null
    },
    groupIndex: {
      type: Number,
      default: null
    },
    selection: {
      type: Object,
      default: null
    },
    group: {
      type: Object,
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
    subgroupIndex: {
      type: Number,
      default: null
    },
  },

  emits: {
    change: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    const value = ref(null)
    const root = ref(null)
    const currentStep = ref(store.state.form.step)
    const setFormEntry = inject('setFormEntry')
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = helpers.withMessage(props.data.error_message, required)
      }

      if (props.data.type === 'email') {
        rules.email = helpers.withMessage(props.data.error_message, email)
      }

      if (props.data.type === 'numeric') {
        rules.numeric = helpers.withMessage(props.data.error_message, numeric)
      }

      if (props.data.type === 'date') {
        rules.dateFormat = helpers.withMessage(props.data.error_message,  (val) => {
          const pattern = new RegExp(/\d{2}\.\d{2}\.\d{4}/)
          return pattern.test(val) && val.length === 10
        })
      }

      return rules
    })

    const v$ = useVuelidate(validationRules, value)

    onMounted(() => {
      if (storeEntry.value) {
        value.value = storeEntry.value['value'].userInput
        root.value.querySelector('.c-input__control')['value'] = storeEntry.value['value'].userInput
      }
    })

    return {root, currentStep, setFormEntry, value, v$}
  },

  render() {
    const field = this.data

    let inputProps = {
      id: `msf-input-${this.index}`,
      class: ['c-input__control', 'msf-input__control', `msf-input__control--${field.type}`],
      type: field.type === 'date' ? 'text' : field.type,
      required: field.is_required,

      onInput: (v) => {
        this.value = v.target.value

        this.setFormEntry({
          step: this.currentStep,
          group: this.stepGroupIndex,
          subgroup: field.duplicate || field.subgroup,
          realIndex: this.realIndex,
          id: this.fieldKey,
          name: this.data.label,
          value: {
            userInput: v.target.value,
            fieldname: this.data.fieldname,
          },
          type: this.data.acf_fc_layout,
        })
      },

      onBlur: async () => {
        await this.v$.$validate()
      }
    }

    if (field.is_required) {
      inputProps = {
        ...inputProps, ...{ required }
      }
    }

    let childElements = [
      h('label', {
        class: ['c-input__label', 'msf-input__label', `msf-input__label--${field.type}`],
        innerHTML: field.label,
        for: `msf-input-${this.index}`
      }),
      h('input', inputProps),
    ]

   if (this.v$ && this.v$.$errors.length) {
      childElements = [...childElements, ...[
        h('span', {
          innerHTML: this.v$.$errors[0].$message,
          class: ['c-input__validation', 'c-input__validation--error', 'msf-input__validation']
        })
      ]]
    }

    const requiredElement = h('span', {
      class: 'msf-input__required-star c-txt c-txt--highlight',
      innerHTML: '*'
    })

    if (field.is_required) {
      childElements.splice(1, 0, requiredElement)
    }

    return h('div',
        {
          class: ['c-input', `c-input--${field.type}`, 'msf-input', `msf-input--${field.type}`, `msf-input--size-${field.size}`],
          ref: 'root'
        }, childElements)
  }
}
</script>

<style lang="scss" src="@styles/components/_input.scss"/>
