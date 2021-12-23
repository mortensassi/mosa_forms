<script>
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
    const selection = ref(0)
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const makeChoice = (choice) => {
      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        value: {
          name: props.data.buttons[choice].text,
          id: choice
        }
      })
    }

    watch(storeEntry, (v) => {
      selection.value = v.value.id
    })

    onMounted(() => {
      if (storeEntry.value) {
        selection.value = storeEntry.value['value'].id
      } else {
        makeChoice(0)
      }
    })

    return { selection, storeEntry, makeChoice }
  }
}
</script>

<template>
  <div class="c-input msf-input msf-input--choices">
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
      {{ choice.text }}
    </button>
  </div>
</template>

<style scoped lang="scss" src=""></style>
