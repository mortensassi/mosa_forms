<script>
import Multiselect from 'vue-multiselect'
import {computed, inject, onMounted, ref, watch} from 'vue'
import store from '@/store'
import {useVuelidate} from "@vuelidate/core";
import {helpers, minLength, required} from "@vuelidate/validators";

export default {
  name: 'FormMultiselect',
  components: {Multiselect},
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
    const choices = computed(() => {
      const data = props.data.choices

      data.forEach((group, groupIndex) => {
        group.choices.forEach((choice, choiceIndex) => {
          choice.index = `${groupIndex}-${choiceIndex}`
        })
      })

      return data
    })
    const selection = ref([])
    const currentStep = ref(store.state.form.step)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const setFormEntry = inject('setFormEntry')

    const selectedChoices = computed(() => {
      return choices.value.map(group => {
        return group.choices.filter(choice => choice.selected)
      }).flat()
    })

    watch(selection, (n) => {
      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        link: props.data.link,
        name: props.data.label,
        type: props.data.acf_fc_layout,
        value: {
          fieldname: props.data.fieldname,
          selection: n
        }
      })
    })

    onMounted(() => {
      if (storeEntry.value && storeEntry.value['value'].selection) {
        selection.value = storeEntry.value['value'].selection
      } else if (selectedChoices.value) {
        selection.value = selectedChoices.value
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

    const v$ = useVuelidate(validationRules, selection);

    return { choices, selection, storeEntry, selectedChoices, v$ }

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
    <multiselect
      :id="`msf-select-${index}`"
      v-model="selection"
      :options="choices"
      :multiple="true"
      group-values="choices"
      group-label="group"
      label="choice"
      track-by="index"
      placeholder="Wählen Sie Ihr Wohnprojekt aus"
      :show-labels="false"
      :group-select="true"
      :close-on-select="false"
      :clear-on-select="false"
    >
      <template #caret>
        <svg class="multiselect__select">
          <use xlink:href="#icon-chevron-down" />
        </svg>
      </template>
      <template #option="props">
        <div class="option__checkmark">
          <svg
            v-if="props.option.$isLabel && selection.length > 0 "
            class="option__icon"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 12 4"
          ><rect
            x=".167"
            y=".75"
            width="11.667"
            height="2.5"
            rx="1"
          /></svg>
        </div>
        <div
          v-if="props.option.$isLabel"
          class="option__desc"
        >
          {{ props.option.$groupLabel }}
        </div>
        <div
          v-else
          class="option__desc"
        >
          {{ props.option.choice }}
        </div>
      </template>
      <template #selection="{ values, search, isOpen }">
        <span
          v-if="values.length &amp;&amp; !isOpen"
          class="multiselect__placeholder"
        >{{ `${values.length} ${values.length < 2 ? 'Option' : 'Optionen'} ausgewählt` }}</span>
      </template>
    </multiselect>
    <span
      v-if="v$.$errors && v$.$errors[0]"
      class="'c-input__validation', c-input__validation--error msf-input__validation"
    >
    {{ v$.$errors[0].$message }}
  </span>
  </div>
</template>

<style src="@styles/vendor/_multiselect.scss"></style>
