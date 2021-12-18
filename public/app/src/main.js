import { v4 as uuidv4 } from 'uuid'
import {createApp} from 'vue'
import App from './App.vue'
import tooltipPlugin from './plugins/tooltip'
import setFormEntryPlugin from './plugins/setFormEntry'

const appIds = document.querySelectorAll('[id^="mosa-forms-app"]');

appIds.forEach(appId => {
    const app = createApp(App)

    app.use(tooltipPlugin)
    app.use(setFormEntryPlugin)
    app.config.globalProperties.form_id = appId.dataset.formId
    app.provide('formId', app.config.globalProperties.form_id)
    app.provide('uuid', uuidv4())

    app.mount(`#${appId.id}`)
});
