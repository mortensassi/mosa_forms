<script>
export default {
  name: 'OverviewPricerangesingle',
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
  <div class="msf-overview-element__value">&euro; {{ data.value.userInput }}</div>
</template>