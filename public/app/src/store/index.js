import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        form: {
            data: null,
            step: 0,
            stepImage : false,
            entries: {
                steps: []
            }
        }
    }),

    async getFormData(url) {
        const storedSteps = this.state.form.entries.steps
        this.state.form.data = await fetchData(url);
        const data = this.state.form.data

        if (data.acf) {
            data.acf.steps.forEach((step, index) => {
                const groups = step.groups
                const stepStoreObject = {
                    title: step.header.title
                }

                if (groups) {
                    stepStoreObject.groups = step.groups.map(g => {
                        return {
                            title: g.title,
                            fields: []
                        }
                    })
                }

                storedSteps.push(stepStoreObject)
            })
        }
    },

    setImage(image) {
        this.state.form.stepImage = image
    },

    updateStep(step) {
      this.state.form.step = step
    },

    setFormEntry(entry) {
        const { step, group, id, name, value } = entry
        const storeGroupFields = this.state.form.entries.steps[step].groups[group].fields
        const storeEntry = storeGroupFields.find(field => field.id === id)

        if (storeEntry) {
            storeGroupFields[id] = { step, group, id, name, value }
        } else {
            storeGroupFields.push({ step, group, id, name, value })
        }
    },
}

export default store;
