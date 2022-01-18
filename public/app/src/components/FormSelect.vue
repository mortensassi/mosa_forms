<script>
import {ref, computed, onMounted, watch, inject} from 'vue'
import store from '@/store'
import vSelect from 'vue-select'

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

    onMounted(() => {
      if (storeEntry.value && storeEntry.value['value'].userInput) {
        selection.value = storeEntry.value['value'].userInput
      }
    })

    const makeSelection = (value) => {
      selection.value = value

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        type: props.data.acf_fc_layout,
        subgroup: props.data.subgroup,
        value: {
          userInput: selection,
          fieldname: props.data.fieldname
        }
      })
    }

    return {storeEntry, selection, rootEl, currentStep, makeSelection }
  }
}
</script>

<template>
  <div
    ref="rootEl"
    class="msf-input msf-input--select"
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
      @option:selected="makeSelection"
      :modelValue="selection"
      label="choice"
      :options="data.choices"
    >
      <template #open-indicator="{ attributes }">
        <svg v-bind="attributes"><use xlink:href="#icon-chevron-down"></use></svg>
      </template>
    </v-select>
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
