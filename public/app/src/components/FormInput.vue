<script>
import {useVuelidate} from '@vuelidate/core'
import {required, email, helpers, numeric} from '@vuelidate/validators'
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
    let storedFields = null
    let storeEntry = null
    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = required
      }

      if (props.data.type === 'email') {
        rules.email = email
      }

      if (props.data.type === 'number') {
        rules.number = numeric
      }

      return rules
    })
    const validation = reactive({
      result: null,
      type: '',
      message: ''
    })

    if (!props.selection) {
      storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
      storeEntry = computed(() => storedFields[props.realIndex])
    }

    const isChecked = computed(() => {
      if (props.selection) {
        return props.selection.find(item => item.id === props.inputIndex && item.group.id === props.groupIndex)
      }
      return false
    })

    let v$ = null

    v$ = useVuelidate(validationRules, value)

    watch(validation, (n) => {
      if (n.result) {
        validation.type = 'success'
        validation.message = 'Diese Eingabe war ein Erfolg, ja!'
      } else {
        validation.type = 'error'
        validation.message = 'Hier scheint etwas nicht zu passen!'
      }
    }, { deep: true })

    onMounted(() => {
      if (storeEntry && storeEntry.value) {
        value.value = storeEntry.value['value'].userInput
        root.value.querySelector('.c-input__control')['value'] = storeEntry.value['value'].userInput
      }
    })

    return {root, isChecked, currentStep, setFormEntry, value, storeEntry, v$, validation}
  },

  render() {
    const field = this.data

    let inputProps = {
      id: `msf-input-${this.index}`,
      class: ['c-input__control', 'msf-input__control', `msf-input__control--${field.type}`],
      type: field.type,
      required: field.is_required,
      onChange: () => {
        if (this.selection) { //checkbox handler
          this.$emit('change', field.label)
        }
      },
      onInput: (v) => {
        this.value = v.target.value

        if (!this.selection) {
          this.setFormEntry({
            step: this.currentStep,
            group: this.stepGroupIndex,
            subgroup: field.subgroup,
            realIndex: this.realIndex,
            id: this.fieldKey,
            name: this.data.label,
            value: {
              userInput: v.target.value,
              fieldname: this.data.fieldname,
            },
            type: this.data.acf_fc_layout,
          })
        }
      },
      onBlur: async () => {
        this.validation.result = await this.v$.$validate()
      }
    }

    if (this.selection) {
      inputProps = {
        ...inputProps, ...{
          checked: this.isChecked
        }
      }
    }

    const childElements = [
      h('label', {
        class: ['c-input__label', 'msf-input__label', `msf-input__label--${field.type}`],
        innerHTML: field.label,
        for: `msf-input-${this.index}`
      }),
      h('input', inputProps),
      h('span', {
        innerHTML: this.validation.message,
        class: ['c-input__validation', `c-input__validation--${this.validation.type}`]
      })
    ]

    const requiredElement = h('span', {
      class: 'msf-input__required-star c-txt c-txt--highlight',
      innerHTML: '*'
    })

    if (field.is_required) childElements.splice(1, 0, requiredElement)

    return h('div',
        {
          class: ['c-input', `c-input--${field.type}`, this.validation ? `c-input--${this.validation.type}` : '', 'msf-input', `msf-input--${field.type}`, `msf-input--size-${field.size}`],
          ref: 'root'
        }, childElements)
  }
}
</script>

<style lang="scss" src="@styles/components/_input.scss"/>
