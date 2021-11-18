<script>
export default {
  name: 'FormSelect',
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
}
</script>

<template>
  <div class="msf-input msf-input--select">
    <label
      :for="`msf-select-${index}`"
      class="msf-input__label ms-input__label--select"
    />
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