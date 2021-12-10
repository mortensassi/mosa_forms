<script>
import { ref } from 'vue'
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

  setup() {
    const value = ref(null)
    const options = ref([ {
      id: 'a',
      label: 'a',
      children: [ {
        id: 'aa',
        label: 'aa',
      }, {
        id: 'ab',
        label: 'ab',
      } ],
    }, {
      id: 'b',
      label: 'b',
    }, {
      id: 'c',
      label: 'c',
    } ])

    return { value, options }
  }
}
</script>

<template>
  <div class="msf-input msf-input--select">
    <treeselect v-model="value" :multiple="true" :options="options" :placeholder="data.title" />
    <label
      :for="`msf-select-${index}`"
      class="msf-input__label ms-input__label--select"
    >{{ data.label }}</label>
    <select
      :id="`msf-select-${index}`"
      class="msf-input__control"
    >
      <option
        v-for="(item, itemIndex) in data.choices"
        :key="`msf-select-${index}-choice-${itemIndex}`"
      >
        {{ item.choice }}
      </option>
    </select>
  </div>
</template>
