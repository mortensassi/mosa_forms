<script>
  import AppForm from '@/components/AppForm.vue'
  import store from '@/store'
  import { onBeforeMount, inject, watch } from 'vue'
  import fetchData from '@/api'

  export default {
    name: 'MosaFormsApp',

    components: {
      AppForm
    },

    setup() {
      const formId = inject('formId')
      const storedStateName = `mosa-forms-${formId}`
      const storedState = localStorage.getItem(storedStateName)

      onBeforeMount(async () => {
        const data = window[`mosaFormsData${formId}`]
        const modifiedNew = data.modified

        if (storedState) {
          if (modifiedNew) {
            const currentModified = new Date(modifiedNew).getTime()
            const storedModified = new Date(JSON.parse(storedState).form.data.modified).getTime()

            if (currentModified > storedModified) {
              console.log('is newer!')
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
