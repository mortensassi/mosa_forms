<script>
import {inject, onMounted, ref, watch} from 'vue'
import lozad from 'lozad'

export default {
  name: 'FormAfterSubmission',

  props: {
    data: {
      type: Object,
      default: null
    }
  },

  setup() {
    const lazyResponseImage = ref(null)
    const responseHeader = ref(null)
    const responseBody = ref(null)

    watch(lazyResponseImage, img => {
      if (img) {
        const observer = lozad(img)
        observer.observe()
      }
    })

    const moveHeader = () => {
      const headerEl = responseHeader.value

      if (headerEl) {
        const heroEl = document.querySelector('.c-hero')
        heroEl.style.paddingBottom = `${headerEl.getBoundingClientRect().height}px`
        heroEl.appendChild(headerEl)
      }
    }

    onMounted(async () => {
      const formId = inject('formId')
      sessionStorage.removeItem(`mosa-forms-${formId}`)

      moveHeader()

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

    return {
      lazyResponseImage,
      responseHeader,
      responseBody
    }
  }
}
</script>
<template>
  <div class="msf-response">
    <div
      ref="responseHeader"
      class="msf-response__header"
    >
      <div class="o-wrapper">
        <div class="columns is-marginless">
          <div class="column is-12 is-10-desktop is-offset-1-desktop is-paddingless">
            <div class="msf-response__header-inner">
              <div
                v-if="data.data && data.data.content.image"
                class="c-img c-img--bg msf-response__header-image"
                :style="{
                  '--image-aspect--mobile': `100%`,
                  '--image-aspect': `100%`,
                  '--image-focuspoint': '50% 50%',
                }"
              >
                <div class="c-img__wrap">
                  <img
                    ref="lazyResponseImage"
                    class="c-img__full u-lazy"
                    :src="data.data.content.image.sizes.thumbnail"
                    :data-src="data.data.content.image.sizes.large"
                    :alt="data.data.content.image.alt"
                  >
                  <div
                    class="c-img__placeholder"
                    :style="{'background-image': `url(${data.data.content.image.sizes.thumbnail})`}"
                  />
                </div>
              </div>
              <div
                v-if="data.data && data.data.content.message"
                ref="responseBody"
                class="msf-response__header-content"
                v-html="data.data.content.message"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column is-12 is-10-desktop is-offset-1-desktop">
        <div
          class="msf-response__body"
          v-html="data.data.content.detail"
        />
        <div class="msf-response__cta">
          <a
            href="/"
            class="c-btn c-btn--primary msf-response__btn"
          >Zur Startseite</a>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_response.scss"></style>
