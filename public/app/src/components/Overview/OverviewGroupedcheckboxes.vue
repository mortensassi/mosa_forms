<script>
export default {
  name: 'OverviewGroupedCheckboxes',
  props: {
    data: {
      type: Object,
      default: null
    }
  },
}
</script>
<template>
  <div class="msf-overview-element__title">
    {{ data.name }}
  </div>
  <div class="msf-overview-element__value">
    {{ data.value.selection.map(el => el.value).join(', ') }}
  </div>
</template>