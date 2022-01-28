import fetchData from '@/api'
import {reactive, inject} from 'vue'

const store = {
  state: reactive({
    progress: {
      heroElPaddingBottom: null
    },
    form: {
      data: null,
      step: 0,
      stepImage: false,
      entries: {
        steps: []
      },
      response: null
    }
  }),

  async getFormData(formData) {
    const storedSteps = this.state.form.entries.steps
    this.state.form.data = formData;
    const data = this.state.form.data

    if (data.acf) {
      data.acf.steps.forEach((step) => {
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

  setHeroElPaddingBottom(val) {
    this.state.progress.heroElPaddingBottom = val
  },

  setStoreFromStorage(storedState) {
    if (storedState) {
      this.state.form = JSON.parse(storedState).form
    }
  },

  setResponse(res) {
    this.state.form.response = res
  },

  setImage(image) {
    this.state.form.stepImage = image
  },

  updateStep(step) {
    this.state.form.step = step
  },

  setFormEntry(entry) {
    const {step, group, subgroup, id, name, value, type, realIndex, link} = entry

    this.state.form.entries.steps[step].groups[group].fields[realIndex] = {step, group, subgroup, id, name, value, type, realIndex, link}
  },

  duplicateFields(step, count) {
    this.state.form.entries.steps[step.index].groups[step.groupIndex].duplicates = step.fields
    this.state.form.entries.steps[step.index].groups[step.groupIndex].duplicateCount = count
  },

  removeDuplicates(stepIndex, groupIndex, count) {
    const groupWithDuplicator = this.state.form.entries.steps[stepIndex].groups[groupIndex]
    groupWithDuplicator.fields = groupWithDuplicator.fields.filter(field => field.subgroup === count)
    groupWithDuplicator.duplicates = groupWithDuplicator.fields.filter(field => field.subgroup === count)
    groupWithDuplicator.duplicateCount = count
  }
}

export default store;
