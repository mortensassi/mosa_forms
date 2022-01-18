<script>
import {ref, computed} from 'vue'
import store from '@/store'
import AppFormHeader from '@/components/AppFormHeader.vue'
import AppFormProgress from '@/components/AppFormProgress.vue'
import FormStep from '@/components/FormStep.vue'
import FormOverview from '@/components/FormOverview.vue'
import _groupBy from "lodash.groupby";

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

    const collection = computed(() => {
      const { steps } = store.state.form.entries
      const stepsCopy = JSON.parse(JSON.stringify(steps))


      stepsCopy.forEach(step => {
        step.groups.forEach(group => {
          if (group.fields.find(entry => {
            if (!entry) return
            return entry.subgroup !== undefined
          })) {
            group.fields = _groupBy(group.fields, 'subgroup')
          }
        })
      })

      return stepsCopy
    })

    return {
      step,
      formData,
      currentStep,
      goToStep,
      collection,
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
          :data="formData.acf.steps"
          :acf="formData.acf"
          :collection="collection"
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
  @include anim($dur: 200ms, $prop: opacity);
}

.move-up-enter-from,
.move-up-leave-to {
  opacity: 0;
}
</style>
