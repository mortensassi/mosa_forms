<script>
export default {
  name: 'OverviewButtongroup',

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
  <div class="msf-overview-element__value">{{ data.value.map(el => el.label).join(', ') }}</div>
</template>

<style scoped lang="scss" src=""></style>