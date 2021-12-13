<template>
  <header class="msf-step__header">
    <AppImage
      v-if="data.image"
      :image="data.image"
    />
    <div class="msf-step__header-content">
      <div class="columns is-multiline">
        <div class="column is-12 is-3-desktop">
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

<script>
import AppImage from '@/components/AppImage.vue'

export default {
  name: 'AppFormHeader',
  components: { AppImage },
  props: {
    data: {
      type: Object,
      default: null
    }
  },
}
</script>
