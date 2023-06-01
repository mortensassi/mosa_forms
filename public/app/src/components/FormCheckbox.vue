<script>
import {useVuelidate} from '@vuelidate/core'
import {required, helpers} from '@vuelidate/validators'
import store from '@/store'
import {inject, onMounted, computed, ref} from 'vue'

export default {
  name: 'FormCheckbox',
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: ''
    },
    inputIndex: {
      type: Number,
      default: null
    },
    groupIndex: {
      type: Number,
      default: null
    },
    selection: {
      type: Object,
      default: null
    },
    group: {
      type: Object,
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
    subgroupIndex: {
      type: Number,
      default: null
    }
  },
  emits: {
    change: {
      type: String,
      default: ''
    }
  },
}
</script>

<template>
  <div class="c-input c-input--checkbox msf-input msf-input--checkbox" :class="{ 'is-disabled' : data.disabled }">
    <label
      :for="`msf-input-${index}`"
      class="c-input__label msf-input__label msf-input__label--checkbox"
    >{{ data.label }}</label>
    <input
      :id="`msf-input-${index}`"
      type="checkbox"
      class="c-input__control msf-input__control msf-input__control--checkbox"
      :checked="data.checked"
      :disabled="data.disabled"
      @change="$emit('change', data.label)"
    >
    <span
      v-if="data.tooltip"
      class="msf-form-checkbox-tooltip"
      :aria-labelledby="`msf-input-${index}-tooltip`"
    >
      <span class="u-tooltip__icon-wrap">
        <svg class="u-tooltip__icon"><use xlink:href="#icon-info" /></svg>
      </span>
      <span
        :id="`msf-input-${index}-tooltip`"
        role="tooltip"
        class="u-tooltip__content"
      >

        <table class="u-tooltip__table">
          <caption>Wohnungen im Stadtteil</caption>
          <thead>
            <tr>
                <th v-for="header in data.tooltip.header">
                  {{header.c}}
                </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in data.tooltip.body">
              <td v-for="cell in row">{{cell.c}}</td>
            </tr>
          </tbody>
        </table>

        <span class="u-tooltip__arrow" />
      </span>
    </span>
  </div>
</template>

<style lang="scss" src="@styles/components/_input.scss"/>

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
