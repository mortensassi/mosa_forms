<script>
import store from '@/store';
import {computed, onMounted, ref, watch} from 'vue'

export default {
  name: 'AppFormProgress',

  props: {
    showOverview: {
      type: Boolean,
      default: false
    }
  },

  setup(props) {
    const formProgress = ref(null)
    const boundingBox = ref(null)
    const formData = computed(() => store.state.form.data)
    const currentStep = computed(() => store.state.form.step)
    const step = computed(() => formData.value.acf.steps[currentStep.value])
    const heroElPaddingBottom = computed(() => store.state.progress.heroElPaddingBottom)
    const stepImage = computed(() => store.state.form.stepImage)
    const heroEl = document.querySelector('.c-hero')

    const progressItemClasses = (index) => {
      if (props.showOverview || currentStep.value > index) return 'msf-progress__item--checked'
      if (currentStep.value === index) return 'msf-progress__item--active'
    }

    const positionBar = () => {
      const pos = ref(null)
      if (stepImage.value) {
        pos.value = boundingBox.value.height / 2

        if (heroElPaddingBottom.value) {
          heroEl.style.paddingBottom = ''
        }
      } else {
        pos.value = boundingBox.value.height / 3 * 2

        if (heroEl) {
          const heroStyles = window.getComputedStyle(heroEl)
          if (!heroElPaddingBottom.value) {
            store.setHeroElPaddingBottom(heroStyles.getPropertyValue('padding-bottom'))
          }

          heroEl.style.paddingBottom = `calc(${heroElPaddingBottom.value} + ${pos.value}px)`
        }
      }

      formProgress.value.style.setProperty('--offset', `${pos.value}px`)
    }

    onMounted(async () => {
      if (formProgress.value) {
        boundingBox.value = await formProgress.value.getBoundingClientRect()

        positionBar()
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
    })

    watch(stepImage, async (n) => {
      if (formProgress.value) {
        boundingBox.value = await formProgress.value.getBoundingClientRect()
      }
      positionBar()
    }, { deep: true })

    return { formData, formProgress, step, stepImage, currentStep, progressItemClasses }
  }
}
</script>

<template>
  <div
    ref="formProgress"
    class="msf-progress"
    :class="{ 'msf-progress--overlayed' : step.header.image }"
  >
    <div class="columns">
      <div class="column is-12 is-10-desktop is-offset-1-desktop">
        <div
          class="msf-progress__inner"
          :style="`--progress-step: ${currentStep }`"
        >
          <div class="msf-progress__steps">
            <div
              v-for="(item, itemIndex) in formData.acf.steps"
              :key="`progress-item-${itemIndex}`"
              class="msf-progress__item"
              :class="progressItemClasses(itemIndex)"
            >
              <div
                class="msf-progress__item-value"
              >
                <svg
                  v-if="showOverview || currentStep > itemIndex"
                  class="msf-progress__item-icon"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 15"
                ><path
                  d="m18.111 1.746-10.96 10.96-6.01-6.01"
                  stroke="#fff"
                  stroke-width="3"
                /></svg>
                <span v-else>{{ itemIndex + 1 }}</span>
              </div>
              <div class="msf-progress__item-label">
                {{ item.header.title }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_progress.scss"></style>
