<script>
import {useVuelidate} from '@vuelidate/core'
import {helpers, minLength, required} from '@vuelidate/validators'
import store from '@/store'
import _throttle from 'lodash.throttle'
import {ref, computed, onMounted, watch, inject, nextTick} from 'vue'
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

      let storeData = {
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
      }

      if (storeEntry.value && storeEntry.value.listHeight) {
        storeData.listHeight = storeEntry.value.listHeight
      } else {
        storeData.listHeight = maxHeight.value
      }

      setFormEntry(storeData)
    }

    const makeSelection = () => {
      let collection = null
      if (storeEntry.value) {
        collection = storeEntry.value.value.selection
      } else if (preselectedCheckboxes.value.length > 0) {
        collection = preselectedCheckboxes.value
      }

      if (collection) {
        collection.forEach(checkbox => {
          const checkboxIndex = props.data.groups[checkbox.group.id].checkboxes.findIndex(el => el.fieldname === checkbox.fieldname)
          const { value, fieldname } = checkbox
          const val = { value, fieldname }

          updateSelection(checkboxIndex, val, checkbox.group.id)
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
    const listIsCollapsed = ref(true)

    const maxHeight = computed(() => {
      if (!collapseList.value) return 0

      if (storeEntry.value && storeEntry.value.listHeight) return storeEntry.value.listHeight

      const elBox =  checkboxesEl.value.getBoundingClientRect()

      if (collapseList.value && listIsCollapsed.value) {
        const ninthEl = checkboxesEl.value.querySelectorAll('.msf-input--checkbox')[10]
        const ninthElBox = ninthEl.getBoundingClientRect()

        return elBox.height - (elBox.bottom - ninthElBox.bottom) + ninthElBox.height / 2
      }

      return elBox.height
    })

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

          if (val) {
            val.disabled = true
          }

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

    const currentGroupSelection = computed(() => {
      return selection.value.filter(item => checkboxes.value.some(check => item.fieldname === check.fieldname) )
    })

    const toggleAll = () => {
      if (currentGroupSelection.value.length < checkboxes.value.length ) {
        props.data.groups[currentGroup.value].checkboxes.forEach((item, index) => {
          const { checkbox, fieldname } = item
          const val = { fieldname, value: checkbox }

          if (!selection.value.some((selectedItem) => selectedItem.fieldname === fieldname)) {
            updateSelection(index, val, currentGroup.value)
          }
        })
      } else {
        props.data.groups[currentGroup.value].checkboxes.forEach((item, index) => {
          const { checkbox, fieldname } = item
          const val = { fieldname, value: checkbox }

          if (selection.value.some((selectedItem) => fieldname === selectedItem.fieldname && selectedItem.disabled !== true)) {
            updateSelection(index, val, currentGroup.value)
          }
        })

      }
    }

    onMounted(() => {
      makeSelection()
    })

    return {
      checkboxesEl,
      currentGroup,
      checkboxes,
      selection,
      collapseList,
      listIsCollapsed,
      formEntries,
      maxHeight,
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
      currentGroupSelection,
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

      <div
        v-if="data.groups[currentGroup].checkboxes.length > 1"
        class="msf-input msf-input--select-all msf-input--checkbox c-input c-input--checkbox"
      >
        <label
          :for="`GroupedCheckboxes-g-${currentGroup}-select-all`"
          class="c-input__label msf-input__label msf-input__label--checkbox"
        >Alle auswählen</label>
        <div class="msf-input__wrapper">
          <svg
            v-if="currentGroupSelection.length > 0"
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
            :checked="currentGroupSelection.length === data.groups[currentGroup].checkboxes.length"
            type="checkbox"
            class="c-input__control msf-input__control msf-input__control--checkbox msf-input__control--select-all"
            @change="toggleAll"
          >
        </div>
      </div>
      <div
        ref="checkboxesEl"
        class="msf-input__checkboxes"
        :class="listIsCollapsed ? 'msf-input__checkboxes--collapsed' : 'msf-input__checkboxes--is-expanded'"
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
        @click="listIsCollapsed = !listIsCollapsed"
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

<style>
  .msf-input__checkboxes--collapsed {
    --list-max-height: v-bind(maxHeight);
    max-height: calc(var(--list-max-height) * 1px)
  }
</style>