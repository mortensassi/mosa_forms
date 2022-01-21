<script>
export default {
  name: 'OverviewChoices',
  props: {
    data: {
      type: Object,
      default: null
    }
  },
}
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">{{ data.value.value.name }}</div>
</template>