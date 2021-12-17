<script>
export default {
  name: 'FormDuplicate',
  props: {
    data: {
      type: Object,
      default: null
    }
  },
  emits: ['duplicate']
}
</script>

<template>
  <div class="msf-form__duplicator">
    <button @click="$emit('duplicate')" class="c-btn c-btn--is-icon msf-form__duplicator-btn">
      <span class="c-btn__label is-sr-only">{{ data.label }}</span>
      <svg class="c-btn__icon"><use xlink:href="#icon-plus"></use></svg>
    </button>
    <div class="msf-form__duplicator-label">{{ data.label }}</div>
  </div>
</template>
