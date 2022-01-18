<script>
import {computed, inject, onMounted, ref} from 'vue'
import store from '@/store'

export default {
  name: 'FormButtongroup',
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
    const selection = ref([])
    const showTooltip = inject('showTooltip')
    const hideTooltip = inject('hideTooltip')
    const buttonGroup = ref(null)
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const toggleSelection = (buttonIndex, label) => {
      if (selection.value.find(button => button.index === buttonIndex)) {
        const pos = selection.value.findIndex(button => button.index === buttonIndex)
        selection.value.splice(pos, 1)
      } else {
        selection.value.push({ index: buttonIndex, label })
      }

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        value: selection.value,
        type: props.data.acf_fc_layout,
      })
    }

    onMounted(() => {
      if (storeEntry.value) {
        selection.value = storeEntry.value['value']
      }

      if (buttonGroup.value) {
        const buttons = buttonGroup.value.querySelectorAll('.c-btn--pill')
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

    return { buttonGroup, selection, toggleSelection }
  }
}
</script>

<template>
  <div class="msf-input msf-input--btn-group">
    <span class="msf-input__label msf-input__label--button-group">
      {{ data.label }}
      <span
        v-if="data.is_required"
        class="c-txt c-txt--highlight"
      >*</span>
    </span>
    <div
      ref="buttonGroup"
      class="msf-form__button-group"
    >
      <button
        v-for="(button, buttonIndex) in data.buttons"
        :key="`Buttongroup-${index}-button-${buttonIndex}`"
        class="c-btn c-btn--pill msf-form__btn"
        :class="{ 'is-active' : selection.find(button => button.index === buttonIndex) }"
        @click="toggleSelection(buttonIndex, button.label)"
      >
        <span class="c-btn__label">{{ button.label }}</span>
        <span
          v-if="button.has_info"
          class="c-btn--tooltip u-tooltip"
          :aria-labelledby="`Buttongroup-${index}-button-${buttonIndex}-tooltip`"
        >
          <span class="u-tooltip__icon-wrap">
            <svg class="u-tooltip__icon"><use xlink:href="#icon-info" /></svg>
          </span>
          <span
            :id="`Buttongroup-${index}-button-${buttonIndex}-tooltip`"
            role="tooltip"
            class="u-tooltip__content"
          >
            {{ button.info }}
            <span class="u-tooltip__arrow" />
          </span>
        </span>
      </button>
    </div>
  </div>
</template>

<style lang="scss">
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
</style>
