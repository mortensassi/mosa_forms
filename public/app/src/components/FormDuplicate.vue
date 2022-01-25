<script>
import { computed } from 'vue'

export default {
  name: 'FormDuplicate',
  props: {
    data: {
      type: Object,
      default: null
    },

    duplicateCount: {
      type: Number,
      default: 0
    }
  },
  emits: ['duplicate', 'removeDuplicate'],

  setup(props) {
    const maxLimit = computed(() => props.duplicateCount === props.data.max_count)

    return { maxLimit }
  }
}
</script>

<template>
  <div class="msf-form__duplicator">
    <button
      class="c-btn c-btn--is-icon msf-form__duplicator-btn"
      @click="$emit(maxLimit ? 'removeDuplicate' : 'duplicate')"
    >
      <span class="c-btn__label is-sr-only">{{ data.label }}</span>
      <svg class="c-btn__icon">
        <use
          v-if="maxLimit"
          xlink:href="#icon-minus"
        />
        <use
          v-else
          xlink:href="#icon-plus"
        />
      </svg>
    </button>
    <div class="msf-form__duplicator-label">
      {{ maxLimit ? data.label_remove : data.label }}
    </div>
  </div>
</template>
