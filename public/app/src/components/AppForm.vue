<script>
import { computed } from 'vue'
import store from '@/store';

export default {
  name: 'AppForm',

  setup() {
    const formData = computed(() => store.state.formData)

    return { formData }
  }
}
</script>

<template>
  <pre>Hello From Form</pre>
</template>

<style scoped>

</style>
