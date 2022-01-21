<script>
export default {
  name: 'OverviewCountries',
  props: {
    data: {
      type: Object,
      default: null
    }
  },

  setup() {

  }
}
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">{{ data.value.userInput.name }}</div>
</template>