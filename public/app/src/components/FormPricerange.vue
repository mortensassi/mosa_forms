<script>
import {useVuelidate} from '@vuelidate/core'
import {minLength, required, helpers, numeric} from '@vuelidate/validators'
import store from '@/store'
import {computed, inject, onMounted, ref, watch} from 'vue'
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
    const priceRange = ref(null)
    const inputData = ref([Number(0), Number(props.data.max_val)])
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const updateInputData = () => {
      const current = inputData.value
      priceRange.value.setValue(current)
    }

    const validationRules = {
      inputData: {
        $each: {
          minLength: minLength(3),
          required,
          numeric
        }
      }
    }

    const v$ = useVuelidate(validationRules, inputData)

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
          value: {
            inputData: n,
            fieldname: props.data.fieldname
          }
        })
      }
    }, { deep: true })

    onMounted(() => {
      if (storeEntry.value) {
        inputData.value = storeEntry.value['value'].inputData
      }
    })

    return { inputData, priceRange, updateInputData, storeEntry, v$ }
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
      v-model="inputData"
      :enable-cross="false"
      :max="data.max_val"
      :tooltip="'none'"
      class="msf-range-slider"
    >
      <template #dot>
        <svg class="msf-range-slider__dot">
          <use xlink:href="#icon-range-slider-dot" />
        </svg>
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
        v-for="(input, inputIndex) in inputData"
        :key="`Pricerange-${index}-input-${inputIndex}`"
        class="c-input msf-range-slider__input"
        :class="v$.$errors.length ? 'c-input--error' : 'c-input--success'"
      >
        <label
          :for="`Pricerange-${index}-${inputIndex === 0 ? 'min' : 'max'}-input`"
          class="c-input__label"
        >{{ inputIndex === 0 ? 'Von' : 'Bis' }}</label>
        <div class="c-input__currency-wrap">
          <input
            :id="`Pricerange-${index}-${inputIndex === 0 ? 'min' : 'max'}-input`"
            v-model="inputData[inputIndex]"
            type="number"
            class="c-input__control c-input__control--prepend-with-currency"
            @change="updateInputData"
          >
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_range-slider.scss"></style>
