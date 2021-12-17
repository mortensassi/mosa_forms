<script>
import {inject, onMounted, ref} from 'vue'
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
  setup(props) {
    const showTooltip = inject('showTooltip')
    const hideTooltip = inject('hideTooltip')
    const priceRange = ref(null)
    const inputData = ref(0)

    const updateInputData = () => {
      const current = inputData.value
      priceRange.value.setValue(current)
    }

    const showEvents = ['mouseover', 'focus'];
    const hideEvents = ['mouseleave', 'blur'];

    onMounted(() => {
      if (props.data.info) {
        const tooltip = document.getElementById(`Pricerangesingle-${props.index}-tooltip`)
        const trigger = tooltip.querySelector('.u-tooltip__icon-wrap')
        const tooltipEl = tooltip.querySelector('.u-tooltip__content')

        if (tooltipEl) {
          showEvents.forEach(e => {
            trigger.addEventListener(e, () => showTooltip(tooltip, trigger, tooltipEl))
          })
          hideEvents.forEach(e => {
            trigger.addEventListener(e, () => hideTooltip(tooltipEl))
          })
        }
      }
    })

    return { value: inputData, priceRange, updateInputData, showTooltip, hideTooltip }
  }
}
</script>

<template>
  <div class="msf-range-slider-wrap msf-range-slider-wrap--pricerange">
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
        />
      </template>
    </vue-slider>
  </div>
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
      <div
        v-if="data.info"
        class="msf-form-info"
      >
        <div
          :id="`Pricerangesingle-${index}-tooltip`"
          class="u-tooltip msf-form-info-tooltip"
        >
          <div class="u-tooltip__label msf-form-info-tooltip__label">
            {{ data.info.label }}
          </div>
          <button
            :id="`Pricerangesingle-${index}-tooltip-trigger`"
            class="u-tooltip__trigger msf-form-info-tooltip__trigger"
          >
            <span class="u-tooltip__icon-wrap">
              <svg class="u-tooltip__icon"><use xlink:href="#icon-info" /></svg>
            </span>
          </button>
          <span
            role="tooltip"
            class="u-tooltip__content"
          >
            {{ data.info.description }}
            <span class="u-tooltip__arrow" />
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_range-slider.scss"></style>
