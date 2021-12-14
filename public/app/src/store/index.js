import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        form: {
            data: null,
            step: 0,
            stepImage : false
        }
    }),

    async getFormData(url) {
        this.state.form.data = await fetchData(url);
    },

    setImage(image) {
        this.state.form.stepImage = image
    },

    updateStep(step) {
      this.state.form.step = step
    }
}

export default store;
