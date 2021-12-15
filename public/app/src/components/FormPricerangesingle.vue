<script>
import {ref} from 'vue'
import VueSlider from 'vue-slider-component'

export default {
  name: 'FormPricerangesingle',
  components: { VueSlider },
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
    const priceRange = ref(null)
    const inputData = ref(0)

    const updateInputData = () => {
      const current = inputData.value
      priceRange.value.setValue(current)
    }

    return { value: inputData, priceRange, updateInputData }
  }
}
</script>

<template>
  <span class="msf-input__label msf-input__label--button-group">
    {{ data.label }}
    <span
      v-if="data.is_required"
      class="c-txt c-txt--highlight"
    >*</span>
  </span>
  <vue-slider
    ref="priceRange"
    v-model="value"
    :enable-cross="false"
    :max="data.max_val"
    :tooltip="'none'"
    class="msf-range-slider"
  >
    <template #dot>
      <svg class="msf-range-slider__dot"><use xlink:href="#icon-range-slider-dot" /></svg>
    </template>

    <template #process="{ style }">
      <div
        class="msf-range-slider__process"
        :style="[style, { top: '50%', height: '3px' }]"
      >
        <!-- Can add custom elements here -->
      </div>
    </template>
  </vue-slider>
  <div class="msf-range-slider__inputs">
    <div class="c-input msf-range-slider__input">
      <label
        :for="`Pricerange-${index}-single-input`"
        class="c-input__label"
      >{{ data.label }}</label>
      <div class="c-input__currency-wrap">
        <input
          :id="`Pricerange-${index}-single-input`"
          v-model="value"
          type="number"
          class="c-input__control c-input__control--prepend-with-currency"
          
          :placeholder="`€ ${value}`"
          @change="updateInputData"
        >
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_range-slider.scss"></style>
