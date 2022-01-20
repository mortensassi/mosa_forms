<script>
import {useVuelidate} from '@vuelidate/core'
import {required, helpers} from '@vuelidate/validators'
import store from '@/store'
import {inject, onMounted, computed} from 'vue'

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
    },
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
  <div class="c-input c-input--checkbox msf-input msf-input--checkbox">
    <label
      :for="`msf-input-${index}`"
      class="c-input__label msf-input__label msf-input__label--checkbox"
    >{{ data.label }}</label>
    <input
      :id="`msf-input-${index}`"
      type="checkbox"
      class="c-input__control msf-input__control msf-input__control--checkbox"
      :checked="data.checked"
      @change="$emit('change', data.label)"
    >
  </div>
</template>

<style lang="scss" src="@styles/components/_input.scss"/>