<script>
export default {
  name: 'FormTextarea',
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
  <div class="msf-input msf-input--textarea">
    <label
      :for="`msf-textarea-${index}`"
      class="msf-input__label msf-input__label--textarea"
    >{{ data.label }}</label><textarea
      :id="`msf-textarea-${index}`"
      rows="4"
    />
  </div>
</template>