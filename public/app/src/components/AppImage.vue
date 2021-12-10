<template>
  <div
    class="c-img c-img--bg msf-form-header-image"
    :style="{
      '--image-aspect': `${595 / 1200 * 100}%`,
      '--image-focuspoint': '50% 50%',
    }"
  >
    <div
        v-if="imageProps"
      class="c-img__wrap"
    >
      <div
        ref="lazyImage"
        class="c-img__full"
        :data-background-image="imageProps.type.full.src"
      />
      <div
        class="c-img__placeholder"
        :style="{'background-image': `url(${imageProps.type.thumb.src})`}"
      />
    </div>
  </div>
</template>

<script>
import lozad from 'lozad'
import {computed, onMounted, ref, watch} from 'vue'

export default {
  name: 'AppImage',

  props: {
    image: {
      type: Number,
      default: 0
    }
  },

  setup(props) {
    const imageObject = ref(null)
    const lazyImage = ref(null)
    const getImage = async () => {
      const res = await fetch(`${import.meta.env.VITE_API_ENDPOINT}wp/v2/media/${props.image}`)
      imageObject.value = await res.json()
    }

    const imageProps = computed(() => {
      if (!imageObject.value) return
      const {height, width, sizes} = imageObject.value.media_details
      const {full, thumbnail} = sizes

      return {height, width, type: {full: {src: full.source_url}, thumb: {src: thumbnail.source_url}}}
    })


    watch(lazyImage, (newImage) => {
      if (newImage) {

        const el = newImage
        const observer = lozad(el);

        observer.observe();
      }
    })

    getImage()


    return {imageObject, imageProps, lazyImage}
  },
}
</script>

<style scoped>

</style>
