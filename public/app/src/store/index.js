import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        formData: null
    }),

    async getFormData(url) {
        this.state.formData = await fetchData(url);
    }
}

export default store;
