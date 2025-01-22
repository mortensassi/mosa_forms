<script>
import {useVuelidate} from '@vuelidate/core'
import {helpers, required} from '@vuelidate/validators'
import store from '@/store'
import {computed, inject, onMounted, ref, watch} from 'vue'

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
    const selection = ref()
    const showTooltip = inject('showTooltip')
    const hideTooltip = inject('hideTooltip')
    const choices = ref(null)
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const selectedChoices = computed(() => {
      return props.data.buttons.filter(button => button.selected)
    })

    function getFormFieldNumberValue(formEntries, fieldKey) {
      for (let step of formEntries.steps) {
        for (let group of step.groups) {
          for (let field of group.fields) {

            if(field.type === 'price_range') {
              if (field.value && field.value.fieldname.includes(fieldKey)) {

                // Check if inputData.collection exists and is an array
                if (Array.isArray(field.value.inputData?.collection)) {
                  // Extract the "val" from each object in the collection
                  const mergedValue = field.value.inputData.collection.reduce((sum, item) => {
                    return sum + (item.val || 0); // Default to 0 if val is missing
                  }, 0);
                  return mergedValue;
                } else {
                  return 0; // Default value if collection is not valid
                }
              }
            } else {
              if (field.value && field.value.fieldname === fieldKey) {
                if (Array.isArray(field.value.userInput)) {
                  // Sum up the "value" from each object in the array
                  const mergedValue = field.value.userInput.reduce((sum, item) => {
                    return sum + (item.value || 0); // Default to 0 if value is missing
                  }, 0);
                  return mergedValue;
                } else if (field.value.userInput && typeof field.value.userInput === 'object') {
                  // Return "value" directly if userInput is an object
                  return field.value.userInput.value || 0;
                }
              }
            }
          }
        }
      }
      return 0; // Return default value if not found
    }

    const validateKDU = () => {
      if (!props.data.is_kdu_check ||
        !selection.value ||
        props.data.buttons[selection.value]?.text !== "Ja") {
        console.log('Validation passed: No KDU check required');
        return true;
      }

      const anzahlPersonen = getFormFieldNumberValue(store.state.form.entries, 'anzahlPersonen');
      const maxPrice = getFormFieldNumberValue(store.state.form.entries, 'maxPrice');
      console.log('adultCount:', anzahlPersonen, 'maxPrice:', maxPrice);

      const numberOfPeople = anzahlPersonen || 0;
      const maxRent = maxPrice || 0;
      console.log('numberOfPeople:', numberOfPeople, 'maxRent:', maxRent);

      const kduRates = props.data.kdu_rates;

      let allowedRent = 0;
      if (numberOfPeople <= 1) allowedRent = parseInt(kduRates.persons_one);
      else if (numberOfPeople === 2) allowedRent = parseInt(kduRates.persons_two);
      else if (numberOfPeople === 3) allowedRent = parseInt(kduRates.persons_three);
      else if (numberOfPeople === 4) allowedRent = parseInt(kduRates.persons_four);
      else if (numberOfPeople === 5) allowedRent = parseInt(kduRates.persons_five);
      else {
        const additionalPeople = numberOfPeople - 5;
        allowedRent = parseInt(kduRates.persons_five) + (additionalPeople * parseInt(kduRates.persons_additional));
      }

      console.log('allowedRent:', allowedRent);
      return maxRent <= allowedRent;
    };

    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = helpers.withMessage(props.data.error_message || 'Fehler', required)
      }

      if (props.data.is_kdu_check) {
        rules.kdu = helpers.withMessage(props.data.kdu_rates.error_message,
          validateKDU
        )
      }

      return rules
    })

    const v$ = useVuelidate(validationRules, selection)

    const makeChoice = async (choice) => {
      await v$.value.$validate()

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        value: {
          fieldname: props.data.fieldname,
          value: {
            name: props.data.buttons[choice].text,
            fieldname: props.data.buttons[choice].fieldname
          },
          id: choice
        },
        type: props.data.acf_fc_layout,
        subgroup: props.data.duplicate || props.data.subgroup,
      })
    }

    watch(storeEntry, (v) => {
      if (v) {
        selection.value = v.value.id
      }
    })

    watch(
      [() => store.state.form.entries, selection],
      () => {
        v$.value.$validate() // Trigger revalidation whenever dependencies change
      },
      { deep: true }
    )


    onMounted(() => {
      if (storeEntry.value) {
        if (storeEntry.value['value']) {

          selection.value = storeEntry.value['value'].id
        }
      } else if (selectedChoices.value.length > 0) {
        selectedChoices.value.forEach(selectedButton => {
          const index = props.data.buttons.findIndex(button => button.text === selectedButton.text)

          makeChoice(index)
        })
      }

      if (choices.value) {
        const buttons = choices.value.querySelectorAll('.c-btn--pill')
        buttons.forEach(button => {
          const trigger = button.querySelector('.u-tooltip__icon-wrap')
          const tooltipEl = button.querySelector('.u-tooltip__content')

          if (tooltipEl) {
            const showEvents = ['mouseover', 'focus'];
            const hideEvents = ['mouseleave', 'blur'];

            showEvents.forEach(e => {
              trigger.addEventListener(e, () => showTooltip(button, trigger, tooltipEl))
            })
            hideEvents.forEach(e => {
              trigger.addEventListener(e, () => hideTooltip(tooltipEl))
            })
          }
        })
      }
    })

    const errorMessage = computed(() => {
      if (v$.value.$errors.length > 0) {
        return v$.value.$errors[0].$message
      }
      return null
    })

    return { choices, selection, storeEntry, makeChoice, selectedChoices, v$, errorMessage }
  }
}
</script>

<template>
  <div
    ref="choices"
    class="c-input msf-input msf-input--choices"
    :class="v$.$errors.length ? 'c-input--error' : 'c-input--success'"
  >
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
      class="c-btn c-btn--pill msf-form__btn"
      :class="{ 'is-active' : choiceIndex === selection }"
      @click="makeChoice(choiceIndex)"
    >
      <span class="c-btn__label">{{ choice.text }}</span>
      <span
        v-if="choice.has_info"
        class="c-btn--tooltip"
        :aria-labelledby="`Choice-${index}-button-${choiceIndex}-tooltip`"
      >
          <span class="u-tooltip__icon-wrap">
            <svg class="u-tooltip__icon"><use xlink:href="#icon-info" /></svg>
          </span>
          <span
            :id="`Choice-${index}-button-${choiceIndex}-tooltip`"
            role="tooltip"
            class="u-tooltip__content"
          >
            {{ choice.info }}
            <span class="u-tooltip__arrow" />
          </span>
        </span>
    </button>
    <div
      v-if="errorMessage"
      class="msf-input__validation msf-input__validation--choice msf-input__validation--error"
      v-html="errorMessage"
    ></div>
  </div>
</template>

<style scoped lang="scss" src="">
.u-tooltip__content {
  @include anim($dur: 0.15s);
  @include text-token('sans', 'copy', 'md');

  transition-property: opacity, transform;
  position: absolute;
  display: block;
  left: 0;
  text-align: left;
  visibility: hidden;
  opacity: 0;
  transform: translateY(1rem);
  background: map-get($brand-colors, 'green', '200');
  color: map-get($brand-colors, 'green', '900');
  min-width: 10rem;
  z-index: 4;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;

  @include bp($large-bp) {
    min-width: 20rem;
    padding: 0.625rem 1rem;
  }
}

.u-tooltip__arrow {
  position: absolute;
  z-index: -1;
  background: map-get($brand-colors, 'green', '200');
  width: 1rem;
  height: 1rem;
  transform: rotate(45deg);

  @include bp($large-bp) {
    width: 1.6875rem;
    height: 1.6875rem;
  }
}

.msf-input__validation--choice {
  position: relative;
  top: 0;
  @include bp($large-bp) {
    position: absolute;
    margin-top: -1.5em;
    top: calc(100% + 0.6rem);
  }
}
</style>
