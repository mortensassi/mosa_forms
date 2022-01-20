<script>
import { computed } from 'vue'

export default {
  name: 'OverviewPricerange',
  props: {
    data: {
      type: Object,
      default: null
    }
  },

  setup(props) {
    const value = computed(() => {
      const collection = props.data.value.inputData.collection

      return collection.length > 1 ? collection[1].val : collection[0].val
    })

    return {
      value
    }
  }
}
</script>
<template>
  <div class="msf-overview-element__title">{{ data.name }}</div>
  <div class="msf-overview-element__value">&euro; {{ value }}</div>
</template>