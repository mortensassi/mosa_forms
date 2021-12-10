<template>
  <div class="msf-input msf-input--grouped-checkboxes">
    <div
      class="msf-input__group"
    >
      <button
        v-for="(button, groupIndex) in data.groups"
        :key="`FormGroupedcheckboxes-button-${groupIndex}`"
        class="c-btn c-btn--pill"
        @click="currentGroup = groupIndex"
      >
        <span class="c-btn__label">{{ `${button.name}` }}</span>
      </button>

      <div class="msf-input__checkboxes">
        <FormInput
          v-for="(input, inputIndex) in data.groups[currentGroup].checkboxes.split('\r\n')"
          :key="`GroupedCheckboxes-${currentGroup}-checkbox-${inputIndex}`"
          :data="{ type: 'checkbox', label: input }"
          :index="inputIndex"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import FormInput from '@/components/FormInput.vue'

export default {
  name: 'FormGroupedCheckboxes',
  components: { FormInput },
  props: {
    data: {
      type: Object,
      default: null
    }
  },

  setup() {
    const currentGroup = ref(0)

    return { currentGroup }
  }
}
</script>
