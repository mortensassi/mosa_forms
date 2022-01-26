<script>
import {ref, computed} from 'vue'
import store from '@/store'
import AppFormHeader from '@/components/AppFormHeader.vue'
import AppFormProgress from '@/components/AppFormProgress.vue'
import FormStep from '@/components/FormStep.vue'
import FormOverview from '@/components/FormOverview.vue'
import FormAfterSubmission from '@/components/FormAfterSubmission.vue'

export default {
  name: 'AppForm',

  components: {
    AppFormProgress,
    AppFormHeader,
    FormStep,
    FormOverview,
    FormAfterSubmission
  },

  setup() {
    const showOverview = ref(false)
    const showResponse = ref(false)
    const goToStep = async (step) => {
      if (step) {
        if (step === 'overview') {
          showOverview.value = true
        } else if(step === 'after-submit') {
          showResponse.value = true
        }
        else {
          if (showOverview.value === true) {
            if (step === -1) {
              showOverview.value = false
            }
          } else  {
            await store.updateStep(store.state.form.step + step)
          }
        }

        const headerEl = document.querySelector('.c-header')

        const intersectionObserver = new IntersectionObserver(async (entries) => {
          let [entry] = entries
          if (entry.isIntersecting) {
            intersectionObserver.unobserve(headerEl)
          }
        })

        await intersectionObserver.observe(headerEl)
        headerEl.scrollIntoView({behavior: 'smooth', block: 'end'})
      }
    }

    const formData = computed(() => store.state.form.data)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])
    const formResponse = computed(() => store.state.form.response)

    return {
      step,
      formData,
      currentStep,
      goToStep,
      showOverview,
      showResponse,
      formResponse,
      entries: store.state.form.entries
    }
  }
}
</script>

<template>
  <div
    v-if="formData"
    class="msf-form"
  >
    <AppFormProgress
      v-if="step"
      :show-overview="showOverview"
    />
    <AppFormHeader
      v-if="showOverview"
      :data="{ title: 'Fast geschafft!<br>Hier können Sie Ihre Angaben nochmals überprüfen', overview: true }"
    />
    <AppFormHeader
      v-else-if="step && step.header"
      :data="step.header"
    />

    <div class="msf-form__steps">
      <transition
        name="move-up"
      >
        <FormAfterSubmission
          v-if="formResponse"
          key="view-after-submission"
          :data="formResponse"
        />
        <FormOverview
          v-else-if="showOverview"
          key="view-overview"
          :data="formData.acf.steps"
          :acf="formData.acf"
          :show-overview="showOverview"
          @go-to-step="goToStep"
          @close-overview="showOverview = false"
        />
        <FormStep
          v-else
          :key="`mosa-forms_step-${currentStep}`"
          :current-step="currentStep"
          :step="step"
          :show-overview="showOverview"
          @go-to-step="goToStep"
        />
      </transition>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_form.scss"/>
<style lang="scss" src="@styles/components/_step.scss"/>

<style lang="scss">
.move-up-enter-active,
.move-up-leave-active {
  @include anim($dur: 500ms, $mode: ease-in);
}

.move-up-enter-from,
.move-up-leave-to {
  opacity: 0;
  visibility: hidden;
}
</style>
