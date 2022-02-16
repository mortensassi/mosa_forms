import {reactive} from 'vue'

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
    const {step, group, subgroup, id, name, value, type, realIndex, link, listHeight} = entry

    this.state.form.entries.steps[step].groups[group].fields[realIndex] = {step, group, subgroup, id, name, value, type, realIndex, link, listHeight}
  },

  duplicateFields(step, count) {
    this.state.form.entries.steps[step.index].groups.splice(step.groupIndex + 1, 0, { fields: [], title: step.groupTitle})
    this.state.form.entries.steps[step.index].groups[step.groupIndex].duplicates = step.fields
    this.state.form.entries.steps[step.index].groups[step.groupIndex].duplicateCount = count
  },

  removeDuplicates(stepIndex, index) {
    this.state.form.entries.steps[stepIndex].groups.splice(index, 1)
  }
}

export default store;
