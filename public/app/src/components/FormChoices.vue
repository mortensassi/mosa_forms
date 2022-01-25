<script>
import {useVuelidate} from '@vuelidate/core'
import {required} from '@vuelidate/validators'
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
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const selectedChoices = computed(() => {
      return props.data.buttons.filter(button => button.selected)
    })

    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = required
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
    })

    return { selection, storeEntry, makeChoice, selectedChoices, v$ }
  }
}
</script>

<template>
  <div
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
      {{ choice.text }}
    </button>
  </div>
</template>

<style scoped lang="scss" src=""></style>
