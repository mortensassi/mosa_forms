<script>
import _capitalize from 'lodash.capitalize'
import {computed} from 'vue'
import store from '@/store'
import FormInput from '@/components/FormInput.vue'
import FormSelect from '@/components/FormSelect.vue'
import FormTextarea from '@/components/FormTextarea.vue'


export default {
  name: 'AppForm',

  components: {
    FormInput, FormSelect, FormTextarea
  },

  setup() {
    const formData = computed(() => store.state.form.data)
    const formOptions = computed(() => store.state.form.options)
    const prepareCompName = (name) => _capitalize(name)

    return {
      formData,
      formOptions,
      prepareCompName
    }
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
      <component
        :is="`Form${prepareCompName(input.acf_fc_layout)}`"
        v-for="(input, inputIndex) in step.fields"
        :key="`mosa-forms_step-input-${inputIndex}`"
        :data="input"
        :index="`${stepIndex}-${inputIndex}`"
      />
    </div>
  </div>
</template>

<style scoped>

</style>
