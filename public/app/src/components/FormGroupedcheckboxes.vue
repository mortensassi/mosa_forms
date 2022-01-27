<script>
import {useVuelidate} from '@vuelidate/core'
import {helpers, minLength, required} from '@vuelidate/validators'
import store from '@/store'
import _throttle from 'lodash.throttle'
import {ref, computed, onMounted, watch, inject, reactive,} from 'vue'
import FormCheckbox from '@/components/FormCheckbox.vue'

export default {
  name: 'FormGroupedCheckboxes',
  components: {FormCheckbox},
  props: {
    fieldKey: {
      type: String,
      default: null,
    },
    step: {
      type: Object,
      default: null
    },
    group: {
      type: Object,
      default: null
    },
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: null
    },
    realIndex: {
      type: Number,
      default: null
    },
    stepGroupIndex: {
      type: Number,
      default: null
    }
  },

  setup(props) {
    const setFormEntry = inject('setFormEntry')
    const formEntries = ref(store.state.form.entries)
    const checkboxesEl = ref(null)
    const currentStep = ref(store.state.form.step)
    const selection = ref([])
    const storedStepFields = computed(() => store.state.form.entries.steps[currentStep.value].groups.flatMap(group => group.fields))
    const linkedMultiSelect = computed(() => {
      let stepFields = null
      if (storedStepFields.value.length > 0) {
        stepFields = storedStepFields.value
      } else {
        stepFields = props.step.groups.flatMap(group => group.fields)
      }
      return stepFields.find(field => field.link === props.data.fieldname)
    })
    const multiselectEntries = computed(() => {
      if (!linkedMultiSelect.value) return
      let entries = null
      if (storedStepFields.value.length > 0) {
        entries = linkedMultiSelect.value['value'].selection
      } else {
        entries = linkedMultiSelect.value.choices.flatMap(group => group.choices)
      }
      return entries
    })
    const updateSelection = (id, val, group) => {
      const foundItem = selection.value.find(item =>
          item.id === id && item.group.id === group
      )

      if (foundItem) {
        selection.value.splice(selection.value.indexOf(foundItem), 1)
      } else {
        selection.value.push({ id, fieldname: val.fieldname, value: val.value, disabled: val.disabled, group: { id: group, name: props.data.groups[group].name }  })
      }

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.group.title,
        type: props.data.acf_fc_layout,
        value: {
          selection: selection.value,
          fieldname: props.data.fieldname,
        }
      })
    }

    const makeSelection = () => {
      let collection = null
      if (storeEntry.value) {
        collection = storeEntry.value.value.selection
      } else if (preselectedCheckboxes.value.length > 0) {
        collection = preselectedCheckboxes.value
      } /*else if (multiselectEntries.value && multiselectEntries.value.length > 0) {
        multiselectEntries.value.forEach(entry => {
          const checkbox = checkboxes.value.find(checkbox => checkbox.fieldname === entry.region && entry.selected)
          if (checkbox) {
            if (collection) {
              collection = [...collection, ...[checkbox]]
            } else {
              collection = [...[checkbox]]
            }
          }
        })
      }*/

      if (collection) {
        collection.forEach(checkbox => {
          const checkboxIndex = checkboxes.value.findIndex(el => el.fieldname === checkbox.fieldname)
          const { value, fieldname } = checkbox
          const val = { value, fieldname }

          updateSelection(checkboxIndex, val, currentGroup.value)
        })
      }
    }

    const checkboxStateUpdate = (input, inputIndex) => {
      updateSelection(inputIndex, input, currentGroup.value)
    }

    const validationRules = computed(() => {
      const rules = {}

      if (props.data.is_required) {
        rules.required = helpers.withMessage(props.data.error_message, required)
        rules.minLength = helpers.withMessage(props.data.error_message, minLength(1))
      }

      return rules
    })

    const v$ = useVuelidate(validationRules, selection.value)

    const currentGroup = ref(0)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const checkboxes = computed(() => {
      return props.data.groups[currentGroup.value].checkboxes.map(checkbox => {
        return { value: checkbox.checkbox, checked: checkbox.checked, fieldname: checkbox.fieldname }
      })
    })

    const getCheckboxData = (input) => {
      if (!input) return null
      let isDisabled = false
      if (multiselectEntries.value) {
        const match = multiselectEntries.value.find(selection => selection.region === input.fieldname)
        if (match) {
          isDisabled = true
        }
      }
      return {
        type: 'checkbox',
        label: input.value,
        checked: selection.value.some(checkbox => checkbox.fieldname === input.fieldname),
        disabled: isDisabled
      }
    }
    const collapseList = computed(() => checkboxes.value.length > 9)
    const maxHeight = ref(0)
    const listIsCollapsed = ref(true)
    const setMaxHeight = (reset, once) => {
      if (!collapseList.value) return
      const elBox = checkboxesEl.value.getBoundingClientRect()
      if (once) {
        maxHeight.value = elBox.height
      }
      const ninthEl = checkboxesEl.value.querySelectorAll('.msf-input--checkbox')[8]
      const ninthElBox = ninthEl.getBoundingClientRect()
      if (reset) {
        return maxHeight.value
      }
      return elBox.height - (elBox.bottom - ninthElBox.bottom) + ninthElBox.height / 2
    }

    const setMaxHeightVariable = (reset) => {
      checkboxesEl.value.style.setProperty('--list-max-height', `${setMaxHeight(reset)}px`)
      if (reset) {
        checkboxesEl.value.classList.add('msf-input__checkboxes--is-expanded')
        listIsCollapsed.value = false
      }
    }

    const setCurrentGroup = (index) => {
      if (storeEntry.value) {
        storeEntry.value.value.location = props.data.groups[index].name
        storeEntry.value.value.group = index
      }

      currentGroup.value = index
    }

    const preselectedCheckboxes = computed(() => {
      let entries = props.data.groups.map(group => {
        return group.checkboxes.filter(checkbox => checkbox.checked)
      }).flat()
      return entries
    })

    watch(multiselectEntries, (newArr, oldArr) => {
      if (newArr) {
        newArr.forEach(checkbox => {
          checkbox.selected = true
          const checkboxIndex = checkboxes.value.findIndex(el => el.fieldname === checkbox.region)
          const val = checkboxes.value.find(el => el.fieldname === checkbox.region)

          val.disabled = true

          if (val && !selection.value.some(el => el.fieldname === checkbox.region)) {
            updateSelection(checkboxIndex, val, currentGroup.value)
          }
        })

        const oldEntriesNotinNew = oldArr.filter(oldEntry => {
          return !newArr.includes(oldEntry)
              && checkboxes.value.some(checkbox => checkbox.fieldname === oldEntry.region)
              && oldEntry.selected
        } )

        if (oldEntriesNotinNew.length > 0) {
          oldEntriesNotinNew.forEach(checkbox => {
            const checkboxIndex = checkboxes.value.findIndex(el => el.fieldname === checkbox.region)
            const val = checkboxes.value.find(el => el.fieldname === checkbox.region)

            val.disabled = false

            if (val) {
              updateSelection(checkboxIndex, val, currentGroup.value)
              checkbox.selected = false
            }
          })
        }
      }
    })

    const toggleAll = () => {
      const checkboxes = props.data.groups.flatMap(group => group.checkboxes)

      if (selection.value.length < checkboxes.length ) {
        props.data.groups.forEach((group, groupIndex) => {
          group.checkboxes.forEach((item, index) => {
            const { checkbox, fieldname } = item
            const val = { fieldname, value: checkbox }

            if (!selection.value.some((selectedItem) => selectedItem.fieldname === fieldname)) {
              updateSelection(index, val, groupIndex)
            }
          })
        })
      } else {
        props.data.groups.forEach((group, groupIndex) => {
          group.checkboxes.forEach((item, index) => {
            const { checkbox, fieldname } = item
            const val = { fieldname, value: checkbox }

            if (selection.value.some((selectedItem) => fieldname === selectedItem.fieldname && selectedItem.disabled !== true)) {
              updateSelection(index, val, groupIndex)
            }
          })
        })
      }
    }

    onMounted(() => {
      makeSelection()

      setMaxHeight(true, true)
      if (collapseList.value && listIsCollapsed.value) {
        setMaxHeightVariable()
        window.addEventListener('resize', () => _throttle(() => setMaxHeightVariable, 200))
      } else {
        setMaxHeightVariable(true)
      }
    })

    return {
      checkboxesEl,
      currentGroup,
      checkboxes,
      selection,
      collapseList,
      listIsCollapsed,
      formEntries,
      setMaxHeight,
      setMaxHeightVariable,
      updateSelection,
      setCurrentGroup,
      currentStep,
      storeEntry,
      preselectedCheckboxes,
      linkedMultiSelect,
      multiselectEntries,
      getCheckboxData,
      storedStepFields,
      checkboxStateUpdate,
      makeSelection,
      toggleAll,
      v$
    }
  }
}
</script>

<template>
  <div class="msf-input msf-input--grouped-checkboxes">
    <div
      v-if="data.groups"
      class="msf-input__group"
    >
      <button
        v-for="(button, groupIndex) in data.groups"
        :key="`FormGroupedcheckboxes-button-${groupIndex}`"
        class="c-btn c-btn--pill msf-form__btn"
        :class="{ 'is-hovered' : groupIndex === currentGroup }"
        @click="setCurrentGroup(groupIndex)"
      >
        <span class="c-btn__label">{{ `${button.name}` }} <span>({{ selection.filter(item => item.group.id === groupIndex).length }})</span> </span>
      </button>

      <div class="msf-input msf-input--select-all msf-input--checkbox c-input c-input--checkbox">
        <label
          :for="`GroupedCheckboxes-g-${currentGroup}-select-all`"
          class="c-input__label msf-input__label msf-input__label--checkbox"
        >Alle Auswählen</label>
        <div class="msf-input__wrapper">
          <svg
              v-if="selection.length > 0"
              class="msf-input__icon"
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
          <input
              :id="`GroupedCheckboxes-g-${currentGroup}-select-all`"
              @change="toggleAll"
              :checked="selection.length === data.groups.flatMap(group => group.checkboxes).length"
              type="checkbox"
              class="c-input__control msf-input__control msf-input__control--checkbox msf-input__control--select-all"
          >
        </div>
      </div>
      <div
        ref="checkboxesEl"
        class="msf-input__checkboxes"
        :class="{ 'msf-input__checkboxes--collapsed' : collapseList }"
      >
        <FormCheckbox
          v-for="(input, inputIndex) in checkboxes"
          :key="`GroupedCheckboxes-g-${currentGroup}-c-${inputIndex}`"
          :data="getCheckboxData(input)"
          :selection="selection"
          :index="`GroupedCheckboxes-g-${currentGroup}-c-${inputIndex}`"
          :input-index="inputIndex"
          :group-index="currentGroup"
          @change="checkboxStateUpdate(input, inputIndex)"
        />
      </div>
      <button
        v-if="collapseList && listIsCollapsed"
        class="c-link msf-form__collapse-trigger"
        @click="setMaxHeightVariable(true)"
      >
        Alle Gebiete ansehen ({{ checkboxes.length }})
      </button>
      <span
        v-if="v$.$errors && v$.$errors[0]"
        class="'c-input__validation', c-input__validation--error msf-input__validation"
      >
        {{ v$.$errors[0].$message }}
      </span>
    </div>
  </div>
</template>
