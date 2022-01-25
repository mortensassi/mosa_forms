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
      const formUrl = `${window.location.origin}/wp-json/wp/v2/mosa_form/${formId}`

      onBeforeMount(async () => {
        const modifiedNew = await fetchData(formUrl + '?_fields[]=modified')

        if (storedState) {
          if (modifiedNew) {
            const currentModified = new Date(modifiedNew.modified).getTime()
            const storedModified = new Date(JSON.parse(storedState).form.data.modified).getTime()

            if (currentModified > storedModified) {
              await store.getFormData(formUrl)
            } else {
              store.setStoreFromStorage(storedState)
            }
          }
        } else {
          await store.getFormData(formUrl)
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
