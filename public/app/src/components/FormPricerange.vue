<script>
import {ref, computed, onMounted} from 'vue'
import VueSlider from 'vue-slider-component'

export default {
  name: 'FormPricerange',
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
  setup(props) {
    const priceRange = ref(null)
    const inputData = ref([Number(0), Number(props.data.max_val)])

    const updateInputData = () => {
      console.log('CHANGESSSS')
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

    <template #process="{ start, end, style, index }">
      <div
        class="msf-range-slider__process"
        :style="[style, { top: '50%', height: '3px' }]"
      >
        <!-- Can add custom elements here -->
      </div>
    </template>
  </vue-slider>
  <div class="msf-range-slider__inputs">
    <div
      v-for="(input, inputIndex) in 2"
      :key="`Pricerange-${index}-input-${inputIndex}`"
      class="c-input msf-range-slider__input"
    >
      <label
        :for="`Pricerange-${index}-${inputIndex === 0 ? 'min' : 'max'}-input`"
        class="c-input__label"
      >{{ inputIndex === 0 ? 'Von' : 'Bis' }}</label>
      <div class="c-input__currency-wrap">
        <input
          :id="`Pricerange-${index}-${inputIndex === 0 ? 'min' : 'max'}-input`"
          v-model="value[inputIndex]"
          type="number"
          @change="updateInputData"
          class="c-input__control c-input__control--prepend-with-currency"
          :placeholder="`€ ${value[inputIndex]}`"
        >
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_range-slider.scss"></style>
