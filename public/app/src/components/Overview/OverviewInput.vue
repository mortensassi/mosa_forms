<script>
export default {
  name: 'OverviewInput',
  props: {
    data: {
      type: Object,
      default: null
    }
  },

  setup() {

  }
}
// # TODO: check input field type for date format - remove hotfix by field name
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">{{ data.value.userInput }}</div>
</template>
