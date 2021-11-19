<script>
import _capitalize from 'lodash.capitalize'
import { computed, watch, ref } from 'vue'
import store from '@/store'
import FormInput from '@/components/FormInput.vue'
import FormSelect from '@/components/FormSelect.vue'
import FormTextarea from '@/components/FormTextarea.vue'
import FormCards from '@/components/FormCards.vue'


export default {
  name: 'AppForm',

  components: {
    FormInput, FormSelect, FormTextarea, FormCards
  },

  setup() {
    const formData = computed(() => store.state.form.data)
    const prepareCompName = (name) => _capitalize(name)
    const goToStep = (step) => store.updateStep(store.state.form.step + step)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])
    let stepTransitionName = ref('move-left')

    watch(currentStep, (currentVal, oldVal) => {
      stepTransitionName.value = currentVal > oldVal ? 'move-left' : 'move-right'
    })


    return {
      stepTransitionName,
      step,
      formData,
      currentStep,
      goToStep,
      prepareCompName
    }
  }
}
</script>

<template>
  <div
    v-if="formData"
    class="msf-form"
  >
    <div
      class="msf-form__container"
    >
      <div
        :key="`mosa-forms_step-${currentStep}`"
        class="msf-step"
      >
        <component
          :is="`Form${prepareCompName(input.acf_fc_layout)}`"
          v-for="(input, inputIndex) in step.fields"
          :key="`mosa-forms_step-${currentStep}-input-${inputIndex}`"
          :data="input"
          :index="`${currentStep}-${inputIndex}`"
        />
      </div>
    </div>
    <button
      v-if="currentStep > 0"
      class="msf-form__btn"
      type="button"
      @click="goToStep(-1)"
    >
      Previous
    </button>
    <button
      v-if="formData.acf.steps.length > currentStep + 1"
      class="msf-form__btn"
      type="button"
      @click="goToStep(1)"
    >
      Next
    </button>
    <button
      v-else
      type="button"
    >
      Yodelihi
    </button>
  </div>
</template>

<style lang="scss" src="@styles/components/_form.scss" scoped />
<style lang="scss" src="@styles/components/_step.scss" scoped />

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>