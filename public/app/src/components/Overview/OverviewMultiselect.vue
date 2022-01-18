<script>
export default {
  name: 'OverviewMultiselect',
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
  <div class="msf-overview-element__value">
    <span
      v-for="(choice, choiceIndex) in data.value.selection"
      :key="`overview-${data-id}-${choiceIndex}`"
    >
      {{ choice.choice }}
      <span v-if="choiceIndex < data.value.selection.length - 1">
        &comma;
        <br>
      </span>
    </span>
  </div>
</template>