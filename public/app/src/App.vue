<script>
  import AppForm from '@/components/AppForm.vue'
  import store from '@/store'
  import { onMounted, onBeforeMount, inject, watch } from 'vue'

  export default {
    name: 'MosaFormsApp',

    components: {
      AppForm
    },

    setup() {
      const formId = inject('formId')
      const storedStateName = `mosa-forms-${formId}`
      const storedState = localStorage.getItem(storedStateName)

      onBeforeMount(() => {
        if (storedState) {
          store.setStoreFromStorage()
        } else {
          store.getFormData(`${import.meta.env.VITE_API_ENDPOINT}wp/v2/mosa_form/${formId}`)
        }
      })

      watch(store.state, (newState) => {
        localStorage.setItem(storedStateName, JSON.stringify(newState))

      }, { deep: true })
    },
  }
</script>

<template>
  <div class="o-wrapper">
    <AppForm />
  </div>
</template>
