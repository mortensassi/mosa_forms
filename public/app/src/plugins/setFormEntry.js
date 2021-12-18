import store from '@/store'

export default {
  install: (app) => {
    app.provide('setFormEntry', (entry) => {
      store.setFormEntry(entry)
    })
  }
}
