<script>
import AppImage from '@/components/AppImage.vue'
import AppFormProgress from '@/components/AppFormProgress.vue'

export default {
  name: 'AppFormHeader',
  components: { AppImage, AppFormProgress },
  props: {
    data: {
      type: Object,
      default: null
    },
    showOverview: {
      type: Boolean,
      default: false
    },
    step: {
      type: Object,
      default: null
    }
  },
}
</script>

<template>
  <header
    class="msf-step__header"
    :class="{ 'msf-step__header--has-image' : data.image }"
  >
    <AppImage
      v-if="data.image"
      :image="data.image"
    />

    <AppFormProgress
      v-if="step"
      :show-overview="showOverview"
    />
    
    <div class="msf-step__header-content">
      <div class="columns is-multiline">
        <div
          class="column"
          :class="data.overview ? 'is-12 is-9-desktop' : 'is-12 is-3-desktop'"
        >
          <h1
            class="msf-step__headline"
            v-html="data.title"
          />
        </div>
        <div class="column is-12 is-8-desktop is-offset-1-desktop">
          <div v-html="data.editor_content" />
        </div>
      </div>
    </div>
  </header>
</template>
