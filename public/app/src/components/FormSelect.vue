<script>
import { ref, computed } from 'vue'
import Treeselect from 'vue3-treeselect'
import 'vue3-treeselect/dist/vue3-treeselect.css'

export default {
  name: 'FormSelect',
  components: { Treeselect },
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    const value = ref(null)
    const options = computed(() => {
      const data = props.data

      return data.choices.map((item, index) => {
        let choiceOptions =  {
          id: `choice-${index}`,
          label: item.choice
        }

        if (item.is_grouped) {
          const childrenOptions = {
            children: item.choices.map((nestedItem, nestedIndex) => {
              return {
                id: `choice-${index}-child-${nestedIndex}`,
                label: nestedItem.choice,
              }
            })
          }

          choiceOptions = { ...choiceOptions, ...childrenOptions}
        }

        return choiceOptions
      })
    })

    return { value, options }
  }
}
</script>

<template>
  <div class="msf-input msf-input--select">
    <label
      :for="`msf-select-${index}`"
      class="msf-input__label ms-input__label--select"
    >{{ data.label }} <span
      v-if="data.is_required"
      class="c-txt c-txt--highlight"
    >*</span> </label>
    <treeselect
      :id="`msf-select-${index}`"
      v-model="value"
      :multiple="true"
      :options="options"
      :placeholder="data.title"
      :default-expand-level="1"
    />
  </div>
</template>
