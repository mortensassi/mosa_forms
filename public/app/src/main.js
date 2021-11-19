import { v4 as uuidv4 } from 'uuid'
import {createApp} from 'vue'
import App from './App.vue'

const appIds = document.querySelectorAll('[id^="mosa-forms-app"]');

appIds.forEach(aid => {
    const app = createApp(App)

    app.config.globalProperties.form_id = aid.dataset.formId
    app.provide('formId', app.config.globalProperties.form_id)
    app.provide('uuid', uuidv4())

    app.mount(`#${aid.id}`)
});
