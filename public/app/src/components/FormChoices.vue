<script>
import { ref } from 'vue'

export default {
  name: 'FormChoices',
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: null
    }
  },
  setup() {
    const selection = ref(0)

    return { selection }
  }
}
</script>

<template>
  <div class="c-input msf-input msf-input--choices">
    <div class="c-input__label msf-input__label msf-input__label--choices">
      {{ data.label }}
      <span
        v-if="data.is_required"
        class="c-txt c-txt--highlight"
      >*</span>
    </div>
    <button
      v-for="(choice, choiceIndex) in data.buttons"
      :key="`Choice-${index}-button-${choiceIndex}`"
      class="c-btn c-btn--pill"
      :class="{ 'is-active' : choiceIndex === selection }"
      @click="selection = choiceIndex"
    >
      {{ choice.text }}
    </button>
  </div>
</template>

<style scoped lang="scss" src=""></style>