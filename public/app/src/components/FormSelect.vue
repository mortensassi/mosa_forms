<script>

import {ref, computed, onMounted, watch, inject} from 'vue'
import store from '@/store'
import vSelect from 'vue-select'
import {useVuelidate} from '@vuelidate/core'
import {required, helpers, minLength} from '@vuelidate/validators'

export default {
  name: 'FormSelect',
  components: {vSelect},
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: ''
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
    const rootEl = ref(null)
    const selection = ref([])
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const preSelection = computed(() => {
      return props.data.choices.filter(choice => choice.selected)
    })

    onMounted(() => {
      if (storeEntry.value && storeEntry.value['value'].userInput) {
        selection.value = storeEntry.value['value'].userInput
      } else if(preSelection.value.length > 0) {
        selection.value = preSelection.value[0]
        makeSelection(preSelection.value[0])
      }
    })

    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = helpers.withMessage(props.data.error_message, required)
        rules.minLength = helpers.withMessage(props.data.error_message, minLength(1))
      }

      return rules
    })

    const v$ = useVuelidate(validationRules, selection)

    const makeSelection = async (value) => {
      selection.value = value
      await v$.value.$validate()

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        type: props.data.acf_fc_layout,
        subgroup: props.data.duplicate || props.data.subgroup,
        value: {
          userInput: selection,
          fieldname: props.data.fieldname
        }
      })
    }

    return {storeEntry, selection, rootEl, currentStep, makeSelection, preSelection, v$ }
  }
}
</script>

<template>
  <div
    ref="rootEl"
    class="msf-input msf-input--select"
    :class="{'c-input--error' : v$.$errors && v$.$errors[0]}"
  >
    <label
      :for="`msf-select-${index}`"
      class="msf-input__label ms-input__label--select"
    >{{ data.label }} <span
      v-if="data.is_required"
      class="c-txt c-txt--highlight"
    >*</span> </label>
    <v-select
      :id="`msf-select-${index}`"
      :model-value="selection"
      label="choice"
      :options="data.choices"
      @option:selected="makeSelection"
    >
      <template #open-indicator="{ attributes }">
        <svg v-bind="attributes"><use xlink:href="#icon-chevron-down" /></svg>
      </template>
    </v-select>
    <span
        v-if="v$.$errors && v$.$errors[0]"
        class="'c-input__validation', c-input__validation--error msf-input__validation"
    >
    {{ v$.$errors[0].$message }}
  </span>
  </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
  .is-child {
    margin-left: 1rem;
  }
  .is-selected {
    background-color: hsl(100deg, 20%, 40%);
  }
</style>
