<template>
  <div
    class="c-img c-img--bg msf-form-header-image"
    :style="{
      '--image-aspect--mobile': `${595 / 1200 * 100}%`,
      '--image-aspect': `${595 / 1200 * 100}%`,
      '--image-focuspoint': '50% 50%',
    }"
  >
    <div
      v-if="image"
      class="c-img__wrap"
    >
      <div
        ref="lazyImage"
        class="c-img__full"
        :data-background-image="image.url"
      />
    </div>
  </div>
</template>

<script>
import store from '@/store'
import lozad from 'lozad'
import {ref, watch, onBeforeUnmount} from 'vue'

export default {
  name: 'AppImage',

  props: {
    image: {
      type: Object,
      default: null
    }
  },

  setup() {
    const imageObject = ref(null)
    const lazyImage = ref(null)

    watch(lazyImage, (newImage) => {
      if (newImage) {
        const observer = lozad(newImage);

        observer.observe();

        store.setImage(newImage)
      }
    })

    onBeforeUnmount(() => {
      store.setImage(null)
    })


    return {imageObject, lazyImage}
  },
}
</script>

<style scoped>

</style>
