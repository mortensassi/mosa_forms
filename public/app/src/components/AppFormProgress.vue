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
    const stepImage = computed(() => store.state.form.stepImage)
    const heroEl = document.querySelector('.c-hero--has-bg')
    const heroElPaddingBottom = heroEl.style.paddingBottom

    const moveBar = (image) => {
      const progressEl = formProgress.value
      const parentBox = progressEl.parentNode.getBoundingClientRect()

      if (image) {
        const headerImageBox = image.getBoundingClientRect()

        progressEl.style.transform = `translateY(calc(-50% + ${headerImageBox.bottom - parentBox.top}px))`
      } else {
        const heroEl = document.querySelector('.c-hero')
        const heroElBox = heroEl.getBoundingClientRect()

        progressEl.style.transform = `translateY(calc(-66% + ${heroElBox.bottom - parentBox.top}px))`
      }
    }

    onMounted(async () => {
      moveBar(document.querySelector('.msf-form-header-image'))
      heroEl.style.paddingBottom = '169px'

      const headerEl = document.querySelector('.c-header')

      const intersectionObserver = new IntersectionObserver(async (entries) => {
        let [entry] = entries
        if (entry.isIntersecting) {
          intersectionObserver.unobserve(headerEl)
        }
      })

      await intersectionObserver.observe(headerEl)
      headerEl.scrollIntoView({behavior: 'smooth'})
    })

    watch(stepImage, (n) => {
      if (n) {
        moveBar(n)
        heroEl.style.paddingBottom = heroElPaddingBottom
      } else {
        moveBar()
        heroEl.style.paddingBottom = '169px'
      }
    }, { deep: true })

    return { formData, formProgress, step, stepImage, currentStep }
  }
}
</script>

<style lang="scss" src="@styles/components/_progress.scss"></style>
