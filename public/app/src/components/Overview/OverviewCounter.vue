<script>
export default {
  name: 'OverviewCounter',
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
  <div class="msf-overview-element__group">
    <div
      v-for="(item, itemIndex) in data.value.userInput"
      :key="`overview-${data.id}-item-${itemIndex}`"
      class="msf-overview-element__group-column"
    >
      <div class="msf-overview-element__title">
        {{ item.name }}
      </div>
      <div class="msf-overview-element__value">
        {{ item.value }}
      </div>
    </div>
  </div>
</template>