import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        form: {
            data: null,
            step: 0,
            stepImage : false,
            entries: {
                fields: []
            }
        }
    }),

    async getFormData(url) {
        const storedFields = this.state.form.entries.fields
        this.state.form.data = await fetchData(url);
        const data = this.state.form.data

        if (data.acf) {
            data.acf.steps.forEach((step, index) => {
                storedFields.push({
                    step: index,
                    fields: []
                })
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
        const storedFields = this.state.form.entries.fields
        const stepFields = storedFields.find(s => s.step === this.state.form.step)
        const field = stepFields.fields.find(s => s.id === entry.id)
        const fieldData = {
            id: entry.id,
            name: entry.name,
            value: entry.value
        }

        if (field) {
            console.log(field)
        } else {
            stepFields.push(fieldData)
        }
    }
}

export default store;
