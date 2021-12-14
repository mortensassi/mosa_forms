<script>
import _capitalize from 'lodash.capitalize'
import _camelCase from 'lodash.camelcase'
import {computed, watch, ref} from 'vue'
import store from '@/store'
import AppFormHeader from '@/components/AppFormHeader.vue'
import AppFormProgress from '@/components/AppFormProgress.vue'
import FormInput from '@/components/FormInput.vue'
import FormSelect from '@/components/FormSelect.vue'
import FormTextarea from '@/components/FormTextarea.vue'
import FormCards from '@/components/FormCards.vue'
import FormGroupedcheckboxes from '@/components/FormGroupedcheckboxes.vue'
import FormPricerange from '@/components/FormPricerange.vue'
import FormButtongroup from '@/components/FormButtongroup.vue'


export default {
  name: 'AppForm',

  components: {
    AppFormProgress, AppFormHeader, FormInput, FormSelect, FormTextarea, FormCards, FormGroupedcheckboxes, FormButtongroup, FormPricerange
  },

  setup() {
    let stepTransitionName = ref('move-left')

    const prepareCompName = (name) => _capitalize(_camelCase(name))
    const goToStep = (step) => store.updateStep(store.state.form.step + step)

    const formData = computed(() => store.state.form.data)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])

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
    <AppFormProgress />

    <div class="msf-form__steps">
      <transition :name="stepTransitionName">
        <article
          :key="`mosa-forms_step-${currentStep}`"
          class="msf-step"
        >
          <AppFormHeader :data="step.header" />

          <div
            v-for="(group, groupIndex) in step.groups"
            :key="`mosa-forms_s-${currentStep}-g-${groupIndex}`"
            class="msf-step__group"
          >
            <div class="columns is-multiline">
              <div class="column is-12 is-3-desktop">
                <h2 class="msf-step__group-title">
                  {{ group.title }}
                </h2>
              </div>
              <div class="column is-12 is-8-desktop is-offset-1-desktop">
                <component
                  :is="`Form${prepareCompName(input.acf_fc_layout)}`"
                  v-for="(input, inputIndex) in group.fields"
                  :key="`mosa-forms_s-${currentStep}-g-${groupIndex}-i-${inputIndex}`"
                  :data-comp-name="`Form${prepareCompName(input.acf_fc_layout)}`"
                  :data="input"
                  :index="`${currentStep}-${groupIndex}-${inputIndex}`"
                />
              </div>
            </div>
          </div>
        </article>
      </transition>
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

<style lang="scss" src="@styles/components/_form.scss"/>
<style lang="scss" src="@styles/components/_step.scss"/>

<style>
.move-left-enter-active,
.move-right-enter-active,
.move-left-leave-active,
.move-right-leave-active {
  transition: transform 0.5s ease;
}

.move-left-enter-to,
.move-right-leave-to {
  transform: translateX(100%)
}

.move-left-enter-to,
.move-left-leave-from {
  transform: translateX(0)
}

.move-right-enter-to,
.move-right-leave-from {
  transform: translateX(0);
}

.move-left-leave-to,
.move-right-enter-from {
  transform: translateX(-100%)
}
</style>
