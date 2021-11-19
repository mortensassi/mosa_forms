import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        form: {
            data: null,
            step: 0,
            entry: {}
        }
    }),

    async getFormData(url) {
        this.state.form.data = await fetchData(url);
    },

    updateStep(step) {
      this.state.form.step = step
    }
}

export default store;
