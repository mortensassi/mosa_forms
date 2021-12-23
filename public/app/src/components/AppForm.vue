<script>
import {ref, computed} from 'vue'
import store from '@/store'
import AppFormHeader from '@/components/AppFormHeader.vue'
import AppFormProgress from '@/components/AppFormProgress.vue'
import FormStep from '@/components/FormStep.vue'
import FormOverview from '@/components/FormOverview.vue'

export default {
  name: 'AppForm',

  components: {
    AppFormProgress,
    AppFormHeader,
    FormStep,
    FormOverview
  },

  setup() {
    const showOverview = ref(false)
    const goToStep = async (step) => {
      if (step) {
        if (step === 'overview') {
          showOverview.value = true
        } else {
          if (showOverview.value === true) {
            if (step === -1) {
              showOverview.value = false
            }
          } else {
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
        headerEl.scrollIntoView({behavior: 'smooth'})
      }
    }

    const formData = computed(() => store.state.form.data)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])

    return {
      step,
      formData,
      currentStep,
      goToStep,
      showOverview
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
    <AppFormHeader
      v-if="showOverview"
      :data="{ title: 'Fast geschafft!<br>Hier können Sie Ihre Angaben nochmals überprüfen', overview: true }"
    />
    <AppFormHeader
      v-else-if="step.header"
      :data="step.header"
    />

    <div class="msf-form__steps">
      <transition
        name="move-up"
      >
        <FormOverview
          v-if="showOverview"
          @close-overview="showOverview = false"
        />
        <FormStep
          v-else
          :key="`mosa-forms_step-${currentStep}`"
          :current-step="currentStep"
          :step="step"
        />
      </transition>
    </div>

    <div class="columns">
      <div class="column is-12 is-8-desktop is-offset-4-desktop">
        <div class="msf-form__controls">
          <button
            v-if="currentStep > 0"
            class="msf-form__btn c-btn c-btn--secondary"
            type="button"
            @click="goToStep(-1)"
          >
            Zurück
          </button>
          <button
            v-if="formData.acf.steps.length > currentStep + 1"
            class="msf-form__btn msf-form__btn--next c-btn c-btn--primary"
            type="button"
            @click="goToStep(1)"
          >
            Weiter zu Schritt {{ (currentStep + 1) + 1 }}
          </button>
          <button
            v-else
            class="msf-form__btn msf-form__btn--submit c-btn c-btn--primary"
            type="button"
            @click="goToStep('overview')"
          >
            Zur Übersicht
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_form.scss"/>
<style lang="scss" src="@styles/components/_step.scss"/>

<style lang="scss">
.move-up-enter-active,
.move-up-leave-active {
  @include anim($dur: 200ms, $prop: opacity);
}

.move-up-enter-from,
.move-up-leave-to {
  opacity: 0;
}
</style>
