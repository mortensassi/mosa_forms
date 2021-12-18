<script>
import { ref } from 'vue'
import {h, onMounted} from 'vue'

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
    storeEntry: {
      type: Object,
      default: null
    }
  },

  emits: {
    change: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    const root = ref(null)

    onMounted(() => {
      if (props.storeEntry) {
        if (props.storeEntry.type === 'checkbox') {

          const { selection } = props.storeEntry

          if (selection.find(item => item.id === props.inputIndex)) {
            root.value.querySelector('.c-input__control').checked = true
          }
        }
      }
    })

    return { root }
  },

  render() {
    const field = this.data

    const childElements = [
      h('label', {
        class: ['c-input__label', 'msf-input__label', `msf-input__label--${field.type}`],
        innerHTML: field.label,
        for: `msf-input-${this.index}`
      }),
      h('input', {
        id: `msf-input-${this.index}`,
        class: ['c-input__control', 'msf-input__control', `msf-input__control--${field.type}`],
        type: field.type,
        required: field.is_required,
        onChange: () => this.$emit('change', field.label)
      }),
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
