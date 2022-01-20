<script>
import {useVuelidate} from '@vuelidate/core'
import {required, helpers, numeric} from '@vuelidate/validators'
import store from '@/store'
import {computed, inject, onMounted, onBeforeMount, ref, reactive, watch} from 'vue'
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
    },
    fieldKey: {
      type: String,
      default: ''
    },
    realIndex: {
      type: Number,
      default: null
    },
    stepGroupIndex: {
      type: Number,
      default: null
    },
  },
  setup(props) {
    const showTooltip = inject('showTooltip')
    const hideTooltip = inject('hideTooltip')
    const priceRange = ref(null)
    const sliderVal = ref([Number(props.data.max.val)])
    const inputData = reactive({
      collection: [{val: Number(props.data.max.val)}]
    })
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    onBeforeMount(() => {
      if (props.data.toggle_min) {
        inputData.collection.splice(0,0, {val: Number(props.data.min.val)},)
        sliderVal.value.splice(0,0, Number(props.data.min.val))
      }
    })

    const updateInputData = () => {
      const current = inputData.collection.map(e => e.val)
      priceRange.value.setValue(current)
    }

    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.collection = {
          $each: helpers.forEach({
            val: {
              required: helpers.withMessage(props.data.error_message || 'Fehler', required),
              numeric: helpers.withMessage(props.data.error_message || 'Fehler', numeric)
            }
          })
        }
      }

      return rules
    })

    const v$ = useVuelidate(validationRules, inputData)

    watch(sliderVal, newVal => {
      inputData.collection.map((e, eIndex) => e.val = Array.isArray(newVal) ? newVal[eIndex] : newVal)
    })

    watch(inputData,(n) => {
      v$.value.$validate()
      if (n) {
        setFormEntry({
          step: currentStep.value,
          group: props.stepGroupIndex,
          realIndex: props.realIndex,
          id: props.fieldKey,
          name: props.data.label,
          type: props.data.acf_fc_layout,
          subgroup: props.data.duplicate || props.data.subgroup,
          value: {
            inputData: n,
            fieldname: [props.data.min.fieldname, props.data.max.fieldname]
          }
        })
      }
    }, { deep: true })

    onMounted(() => {
      if (storeEntry.value) {
        inputData.collection = storeEntry.value['value'].inputData.collection
      }

      if (props.data.info) {
        const tooltip = document.getElementById(`Pricerange-${props.index}-tooltip`)
        if (tooltip) {
          const trigger = tooltip.querySelector('.u-tooltip__icon-wrap')
          const tooltipEl = tooltip.querySelector('.u-tooltip__content')

          if (tooltipEl) {
            const showEvents = ['mouseover', 'focus'];
            const hideEvents = ['mouseleave', 'blur'];

            showEvents.forEach(e => {
              trigger.addEventListener(e, () => showTooltip(tooltip, trigger, tooltipEl))
            })
            hideEvents.forEach(e => {
              trigger.addEventListener(e, () => hideTooltip(tooltipEl))
            })
          }

        }
      }
    })

    return { inputData, sliderVal, priceRange, updateInputData, storeEntry, v$, validationRules }
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
      v-model="sliderVal"
      :data="'inputData'"
      :data-id="'val'"
      :enable-cross="false"
      :max="data.max.val"
      :tooltip="'none'"
      class="msf-range-slider"
    >
      <template #dot>
        <svg class="msf-range-slider__dot">
          <use xlink:href="#icon-range-slider-dot" />
        </svg>
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
      <div
        v-for="(input, inputIndex) in inputData.collection"
        :key="`Pricerange-${index}-input-${inputIndex}`"
        class="c-input msf-range-slider__input"
        :class="{'c-input--error' : v$.collection && v$.collection.$each.$response.$errors[inputIndex].val.length}"
      >
        <label
          v-if="inputData.collection.length < 2"
          :for="`Pricerange-${inputIndex}`"
          class="c-input__label"
        >
          {{ data.label }}
        </label>
        <label
          v-else
          :for="`Pricerange-${inputIndex}`"
          class="c-input__label"
        >{{ inputIndex === 0 ? 'Von' : 'Bis' }}</label>
        <div class="c-input__currency-wrap">
          <input
            :id="`Pricerange-${inputIndex}`"
            v-model="input.val"
            type="number"
            class="c-input__control c-input__control--prepend-with-currency"
            @blur="updateInputData"
          >
        </div>
        <div v-if="v$ && v$.collection">
          <span
            v-for="error in v$.collection.$each.$response.$errors[inputIndex].val"
            :key="error"
            class="'c-input__validation', c-input__validation--error msf-input__validation"
          >
            {{ error.$message }}
          </span>
        </div>
      </div>
    </div>
    <div
      v-if="data.info"
      class="msf-form-info"
    >
      <div
        :id="`Pricerange-${index}-tooltip`"
        class="u-tooltip msf-form-info-tooltip"
      >
        <div class="u-tooltip__label msf-form-info-tooltip__label">
          {{ data.info.label }}
        </div>
        <button
          :id="`Pricerange-${index}-tooltip-trigger`"
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
</template>

<style lang="scss" src="@styles/components/_range-slider.scss"></style>
