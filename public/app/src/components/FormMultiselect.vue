<script>
import VueMultiselect from 'vue-multiselect'
import {computed, inject, onMounted, ref, watch} from 'vue'
import store from '@/store'

export default {
  name: 'FormMultiselect',
  components: {VueMultiselect},
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
    const selection = ref([])
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    watch(selection, (n) => {
      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.data.label,
        value: n
      })
    })

    onMounted(() => {
      if (storeEntry.value && storeEntry.value['value']) {
        selection.value = storeEntry.value['value']
      }
    })

    return { selection, storeEntry }

  }
}
</script>

<template>
  <VueMultiselect
      :id="`msf-select-${index}`"
      :multiple="true"
      v-model="selection"
      :options="data.choices"
      group-values="choices"
      label="choice"
      group-label="group"
      :group-select="true"
      track-by="choice"
  />
</template>

<style scoped>

</style>
