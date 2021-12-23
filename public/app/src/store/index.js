import fetchData from "@/api";
import {reactive} from 'vue'

const store = {
  state: reactive({
    form: {
      data: null,
      step: 0,
      stepImage: false,
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
    const {step, group, id, name, value, realIndex} = entry

    this.state.form.entries.steps[step].groups[group].fields[realIndex] = {step, group, id, name, value, realIndex}
  },

  duplicateFields(step) {
    this.state.form.entries.steps[step.index].groups[step.groupIndex].duplicates = step.fields
  }
}

export default store;
