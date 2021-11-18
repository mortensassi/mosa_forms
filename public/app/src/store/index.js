import fetchData from "@/api";
import { reactive } from 'vue'

const store = {
    state: reactive({
        form: {
            data: null,
            options: null
        }
    }),

    async getFormData(url) {
        this.state.form.data = await fetchData(url);
        this.state.form.options = await fetchData(url, 'OPTIONS')
    }
}

export default store;
