<script>
  import AppForm from '@/components/AppForm.vue'
  import store from '@/store'
  import { onMounted, inject } from 'vue'

  export default {
    name: 'MosaFormsApp',

    components: {
      AppForm
    },

    setup() {
      const formId = inject('formId')
      onMounted(() => store.getFormData(`${import.meta.env.VITE_API_ENDPOINT}wp/v2/mosa_form/${formId}`))
    },
  }
</script>

<template>
  <div class="o-wrapper">
    <AppForm />
  </div>
</template>
