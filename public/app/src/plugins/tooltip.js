import {computePosition, flip, shift, offset, arrow} from '@floating-ui/dom'

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

export default {
  install: (app, options) => {

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

    app.provide('tooltip', options)
    app.provide('showTooltip', showTooltip)
    app.provide('hideTooltip', hideTooltip)
  }
}
