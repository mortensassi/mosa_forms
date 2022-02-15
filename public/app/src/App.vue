<script>
  import AppForm from '@/components/AppForm.vue'
  import store from '@/store'
  import { onBeforeMount, inject, watch } from 'vue'

  export default {
    name: 'MosaFormsApp',

    components: {
      AppForm
    },

    setup() {
      const formId = inject('formId')
      const storedStateName = `mosa-forms-${formId}`
      const storedState = sessionStorage.getItem(storedStateName)
      const clearStorage = () => sessionStorage.clear()

      onBeforeMount(async () => {
        const data = window[`mosaFormsData${formId}`]
        const modifiedNew = data.modified

        if (storedState) {
          if (modifiedNew) {
            const currentModified = new Date(modifiedNew).getTime()
            const storedModified = new Date(JSON.parse(storedState).form.data.modified).getTime()

            if (currentModified > storedModified) {
              await store.getFormData(data)
            } else {
              store.setStoreFromStorage(storedState)
            }
          }
        } else {
          await store.getFormData(data)
        }
      })

      watch(store.state, (newState) => {
        sessionStorage.setItem(storedStateName, JSON.stringify(newState))

      }, { deep: true })

      return { clearStorage }
    },
  }
</script>

<template>
  <div class="o-wrapper">
    <AppForm />
  </div>
</template>
