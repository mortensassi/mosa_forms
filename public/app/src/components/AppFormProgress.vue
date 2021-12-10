<template>
  <div
    ref="formProgress"
    class="msf-progress"
    :class="{ 'msf-progress--overlayed' : step.header.image }"
  >
    <div class="columns">
      <div class="column is-12 is-10-desktop is-offset-1-desktop">
        <div class="msf-progress__inner">
          <div
            v-for="(item, itemIndex) in formData.acf.steps"
            :key="`progress-item-${itemIndex}`"
            class="msf-progress-item"
            :class="{ 'msf-progress__item--active' : itemIndex === currentStep }"
          >
            <div class="msf-progress-item__value">
              {{ itemIndex + 1 }}
            </div>
            <svg
              v-if="itemIndex < formData.acf.steps.length - 1"
              class="msf-progress-item__separator"
            ><use xlink:href="#slider-scrollbar-bg"></use></svg>
            <div class="msf-progress-item__label">
              {{ item.header.title }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from '@/store';
import {computed, onMounted, ref, watch} from 'vue'

export default {
  name: 'AppFormProgress',

  setup() {
    const formProgress = ref(null)
    const formData = computed(() => store.state.form.data)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])

    onMounted(() => {
      if (formProgress.value) {
        const progressEl = formProgress.value
        if (step.value.header.image) {
          const headerEl = progressEl.nextSibling
          if (headerEl) {
            const headerImage = headerEl.querySelector('.msf-form-header-image')
            const parentBox = progressEl.parentNode.getBoundingClientRect()
            const headerImageBox = headerImage.getBoundingClientRect()

            progressEl.style.top = `${headerImageBox.bottom - parentBox.top}px`
          }
        }
      }
    })

    return { formData, formProgress, step, currentStep }
  }
}
</script>

<style lang="scss" src="@styles/components/_progress.scss"></style>
