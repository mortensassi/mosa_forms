<script>
import _throttle from 'lodash.throttle'
import {ref, computed, onMounted} from 'vue'
import FormInput from '@/components/FormInput.vue'

export default {
  name: 'FormGroupedCheckboxes',
  components: {FormInput},
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: null
    }
  },

  setup(props) {
    const checkboxesEl = ref(null)
    const selection = ref([])
    const updateSelection = (val) => {
      if (selection.value.includes(val)) {
        const index = selection.value.indexOf(val)
        if (index > -1) {
          selection.value.splice(selection.value.indexOf(val), 1)
        }
      } else {
        selection.value.push(val)
      }
    }
    const currentGroup = ref(0)
    const checkboxes = computed(() => props.data.groups[currentGroup.value].checkboxes.split('\r\n'))
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

    onMounted(() => {
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
      collapseList,
      listIsCollapsed,
      setMaxHeight,
      setMaxHeightVariable,
      updateSelection,
      selection
    }
  }
}
</script>

<template>
  <div class="msf-input msf-input--grouped-checkboxes">
    <div
      class="msf-input__group"
    >
      <button
        v-for="(button, groupIndex) in data.groups"
        :key="`FormGroupedcheckboxes-button-${groupIndex}`"
        class="c-btn c-btn--pill msf-form__btn"
        :class="{ 'is-hovered' : groupIndex === currentGroup }"
        @click="currentGroup = groupIndex"
      >
        <span class="c-btn__label">{{ `${button.name}` }} <span v-if="groupIndex === currentGroup">({{ selection.length }})</span> </span>
      </button>

      <div
        ref="checkboxesEl"
        class="msf-input__checkboxes"
        :class="{ 'msf-input__checkboxes--collapsed' : collapseList }"
      >
        <FormInput
          v-for="(input, inputIndex) in checkboxes"
          :key="`GroupedCheckboxes-${currentGroup}-checkbox-${inputIndex}`"
          :data="{ type: 'checkbox', label: input }"
          :index="`${index}-GroupedCheckboxes-${groupIndex}-checkbox-${inputIndex}`"
          @change="updateSelection"
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
