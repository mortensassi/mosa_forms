<script>
import {onMounted, ref} from 'vue'
import { computePosition, flip, shift, offset, arrow } from '@floating-ui/dom'
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
    }
  },

  setup() {
    const buttonGroup = ref(null)
    const largeBreakpoint = window.matchMedia('(min-width: 1024px)')
    const updatePosition = (button, trigger, tooltipEl) => {
      const arrowEl = button.querySelector('.u-tooltip__arrow')
      const offsetVal = largeBreakpoint.matches ? 24 : 16

      computePosition(trigger, tooltipEl, {
        placement: 'top',
        middleware: [
            flip(),
          shift(),
          offset(offsetVal),
            arrow({element: arrowEl})
        ],
      })
          .then(({x, y, placement, middlewareData}) => {
            Object.assign(tooltipEl.style, {
              left: `${x}px`,
              top: `${y}px`,
            })

            const {x: arrowX, y: arrowY} = middlewareData.arrow

            const staticSide = {
              top: 'bottom',
              right: 'left',
              bottom: 'top',
              left: 'right',
            }[placement.split('-')[0]];

            Object.assign(arrowEl.style, {
              left: arrowX != null ? `${arrowX}px` : '',
              top: arrowY != null ? `${arrowY}px` : '',
              right: '',
              bottom: '',
              [staticSide]: '-4px',
            })
          })

    }
    const showTooltip = (button, trigger, tooltip) => {

      tooltip.style.visibility = 'visible'
      tooltip.style.opacity = '1'
      tooltip.style.transform = 'translateY(0)'

      updatePosition(button, trigger, tooltip)
    }

    const hideTooltip = (tooltip) => {
      tooltip.style.visibility = 'hidden'
      tooltip.style.opacity = '0'
       tooltip.style.transform = 'translateY(1rem)'
    }

    const showEvents = ['mouseover', 'focus'];
    const hideEvents = ['mouseleave', 'blur'];

    onMounted(() => {
      if (buttonGroup.value) {
        const buttons = buttonGroup.value.querySelectorAll('.c-btn--pill')
        buttons.forEach(button => {
          const trigger = button.querySelector('.u-tooltip__icon-wrap')
          const tooltipEl = button.querySelector('.u-tooltip__content')
          if (tooltipEl) {
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

    return { buttonGroup }
  }
}
</script>

<template>
  <div>
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
        class="c-btn c-btn--pill"
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

  display: block;
  visibility: hidden;
  text-align: left;
  opacity: 0;
  transform: translateY(1rem);
  position: absolute;
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
