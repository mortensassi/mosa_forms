<script>
export default {
  name: 'OverviewPricerange',
  props: {
    data: {
      type: Object,
      default: null
    }
  }
}
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">&euro; {{ data.value.inputData[1] }}</div>
</template>