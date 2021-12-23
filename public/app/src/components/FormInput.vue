<script>
import store from '@/store'
import {inject, onMounted, ref} from 'vue'
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

    onMounted(() => {
      if (storeEntry && storeEntry.value) {
        value.value = storeEntry.value['value']
        root.value.querySelector('.c-input__control')['value'] = storeEntry.value['value']
      }
    })

    return { root, isChecked, currentStep, setFormEntry, value, storeEntry }
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
            realIndex: this.realIndex,
            id: this.fieldKey,
            name: this.data.label,
            value: v.target.value
          })
        }
      }
    }

    if (this.selection) {
      inputProps = { ...inputProps, ...{
          checked: this.isChecked
        } }
    }

    const childElements = [
      h('label', {
        class: ['c-input__label', 'msf-input__label', `msf-input__label--${field.type}`],
        innerHTML: field.label,
        for: `msf-input-${this.index}`
      }),
      h('input', inputProps),
    ]

    const requiredElement = h('span', {
      class: 'msf-input__required-star c-txt c-txt--highlight',
      innerHTML: '*'
    })

    if (field.is_required) childElements.splice(1, 0, requiredElement)

    return h('div',
        {
          class: ['c-input', 'msf-input', `msf-input--${field.type}`, `msf-input--size-${field.size}`],
          ref: 'root'
        }, childElements)
  }
}
</script>

<style lang="scss" src="@styles/components/_input.scss"/>
