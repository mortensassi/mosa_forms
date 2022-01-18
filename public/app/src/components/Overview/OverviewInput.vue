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
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">{{ data.value.userInput }}</div>
</template>