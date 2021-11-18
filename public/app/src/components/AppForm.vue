<script>
import {computed} from 'vue'
import store from '@/store';

export default {
  name: 'AppForm',

  setup() {
    const formData = computed(() => store.state.formData)

    return {formData}
  }
}
</script>

<template>
  <div
    v-if="formData"
    class="c-form"
  >
    <div
      v-for="(step, stepIndex) in formData.acf['steps']"
      :key="`mosa-forms_step-${stepIndex}`"
      class="c-form__step"
    >
      <p v-for="(input, inputIndex) in step.fields" :key="`mosa-forms_step-input-${inputIndex}`">
        {{ input.acf_fc_layout }}
      </p>
    </div>
  </div>
</template>

<style scoped>

</style>
