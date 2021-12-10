<template>
  <header class="msf-form-header">
    <AppImage
      v-if="data.image"
      :image="data.image"
    />
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

<style scoped>

</style>
