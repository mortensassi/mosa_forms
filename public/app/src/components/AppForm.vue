<script>
import _capitalize from 'lodash.capitalize'
import {computed} from 'vue'
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


    return {
      formData,
      currentStep,
      goToStep,
      prepareCompName
    }
  }
}
</script>

<template>
  <form
    v-if="formData"
    class="msf-form"
  >
    <div class="msf-form__wrapper">
      <div
        class="msf-form__container"
        :style="{transform: `translateX(calc(-100% * ${currentStep}))`}"
      >
        <div
          v-for="(step, stepIndex) in formData.acf.steps"
          :key="`mosa-forms_step-${stepIndex}`"
          class="msf-step"
        >
          <h2 class="msf-step__headline">
            {{ step.title }}
          </h2>
          <component
            :is="`Form${prepareCompName(input.acf_fc_layout)}`"
            v-for="(input, inputIndex) in step.fields"
            :key="`mosa-forms_step-${stepIndex}-input-${inputIndex}`"
            :data="input"
            :index="`${stepIndex}-${inputIndex}`"
          />
        </div>
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
      v-if="formData.acf.steps.length > currentStep"
      class="msf-form__btn"
      type="button"
      @click="goToStep(1)"
    >
      Next
    </button>
    <button v-else type="button">Yodelihi</button>
  </form>
</template>

<style lang="scss" scoped>
  .msf-form {
    background-color: hsl(160, 15%, 45%);
    color: #fff;
    max-width: 580px;
    margin: 0 auto;
  }

  .msf-form__wrapper {
    overflow: hidden;
  }

  .msf-form__container {
    transition: 0.3s transform cubic-bezier(0.3, 1, 0.9, 1);
    display: flex;
    flex-flow: row nowrap;
  }
  
  .msf-step {
    width: 100%;
    flex: 1 0 100%;
  }
</style>
