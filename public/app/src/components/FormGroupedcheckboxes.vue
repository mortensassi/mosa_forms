<script>
import store from '@/store'
import _throttle from 'lodash.throttle'
import {ref, computed, onMounted, inject, watch} from 'vue'
import FormInput from '@/components/FormInput.vue'

export default {
  name: 'FormGroupedCheckboxes',
  components: {FormInput},
  props: {
    fieldKey: {
      type: String,
      default: null,
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
    const updateSelection = (id, val, group) => {
      const foundItem = selection.value.find(item =>
        item.id === id && item.group.id === group
      )
      if (foundItem) {
        selection.value.splice(selection.value.indexOf(foundItem), 1)
      } else {
        selection.value.push({ id, value: val.checkbox, group: { id: currentGroup.value, name: props.data.groups[currentGroup.value].name }  })
      }

      setFormEntry({
        step: currentStep.value,
        group: props.stepGroupIndex,
        realIndex: props.realIndex,
        id: props.fieldKey,
        name: props.group.title,
        type: props.data.acf_fc_layout,
        test: 'test',
        value: {
          selection: selection.value
        }
      })
    }

    const currentGroup = ref(0)
    const storedFields = store.state.form.entries.steps[currentStep.value].groups[props.stepGroupIndex].fields
    const storeEntry = computed(() => storedFields[props.realIndex])
    const checkboxes = computed(() => props.data.groups[currentGroup.value].checkboxes)
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

    onMounted(() => {
      if (storeEntry.value) {
        selection.value = storeEntry.value.value.selection
      }

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
        ref="checkboxesEl"
        class="msf-input__checkboxes"
        :class="{ 'msf-input__checkboxes--collapsed' : collapseList }"
      >
        <FormInput
          v-for="(input, inputIndex) in checkboxes"
          :key="`GroupedCheckboxes-g-${currentGroup}-c-${inputIndex}`"
          :data="{ type: 'checkbox', label: input.checkbox }"
          :selection="selection"
          :index="`GroupedCheckboxes-g-${currentGroup}-c-${inputIndex}`"
          :input-index="inputIndex"
          :group-index="currentGroup"
          @change="updateSelection(inputIndex, input, currentGroup)"
        />
      </div>
      <button
        v-if="collapseList && listIsCollapsed"
        class="c-link msf-form__collapse-trigger"
        @click="setMaxHeightVariable(true)"
      >
        Alle Gebiete ansehen ({{ checkboxes.length }})
      </button>
    </div>
  </div>
</template>
