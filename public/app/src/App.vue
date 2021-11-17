<script>
  import AppForm from '@/components/AppForm.vue'
  import store from '@/store'

  export default {
    name: 'MosaFormsApp',

    data() {
      return {
        form: store.state.formData
      }
    },

    components: {
      AppForm
    },

    computed: {
      formData() {
        return store.state.formData
      }
    },

    methods: {
      fetchData() {
        store.getFormData('https://wp.lo/wp-json/wp/v2/mosa_form/' + this.form_id)
      }
    },

    mounted() {
      this.fetchData()
    },
  }
</script>

<template>
  <AppForm></AppForm>
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
