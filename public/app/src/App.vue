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
  <AppForm />
</template>

<style>
[id^='mosa-form'] {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
