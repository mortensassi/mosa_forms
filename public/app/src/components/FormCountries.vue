<script>
import Fuse from 'fuse.js'
import {ref, computed, watch} from 'vue'
import countriesList from "@/assets/countries.json";

export default {
  name: 'FormCountryselect',
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: null
    }
  },

  setup(props) {
    const searchInput = ref('')
    const autocompleteStr = ref('')
    const selection = ref(null)
    const showDropdown = ref(false)

    const countries = computed(() => {
      return countriesList.reduce((r, e) => {
        // get first letter of name of current element
        let group = e.name[0];
        // if there is no property in accumulator with this letter create it
        if (!r[group]) r[group] = {group, children: [e]}
        // if there is push current element to children array for that letter
        else r[group].children.push(e);
        // return accumulator
        return r;
      }, {})
    })

    const searchEngine = () => {
      const options = {keys: ['name']}
      const index = Fuse.createIndex(options.keys, countriesList)

      return new Fuse(countriesList, options, index)
    }

    const searchRequest = (val) => {
      selection.value = ''
      autocompleteStr.value = ''

      const result = searchEngine().search(val)
      if (result[0]) {
        const resultStr = result[0].item.name
        autocompleteStr.value = val + resultStr.slice(val.length)
      }
    }

    const setSelection = (val) => {
      if (typeof val === 'string') {
        selection.value = val
        searchInput.value = val
        autocompleteStr.value = ''
      }
      else if (autocompleteStr.value) {
        selection.value = autocompleteStr.value
        searchInput.value = autocompleteStr.value
      }
    }

    const toggleDropdown = (val) => {
      showDropdown.value = val
    }

    watch(selection, (n) => {
      if (n) {
        document.getElementById(`Countries-${props.index}-input`).blur()
        toggleDropdown(false)
      }
    })

    return {
      countries,
      searchInput,
      searchRequest,
      autocompleteStr,
      setSelection,
      selection,
      toggleDropdown,
      showDropdown
    }
  }
}
</script>

<template>
  <div class="c-input msf-input msf-input--choices">
    <div class="c-input__label msf-input__label msf-input__label--choices">
      {{ data.label }}
      <span
        v-if="data.is_required"
        class="c-txt c-txt--highlight"
      >*</span>
      <div class="msf-select">
        <div class="msf-select__inner">
          <div class="c-input">
            <label
              :for="`Countries-${index}-input`"
              class="c-input__label is-sr-only"
            />
            <input
              :id="`Countries-${index}-input`"
              v-model="searchInput"
              type="text"
              class="c-input__control msf-select__search-input"
              @input="searchRequest(searchInput)"
              @keydown.enter="setSelection"
              @focus="toggleDropdown(true)"
              @click="setSelection(true)"
            >
            <span
              v-if="autocompleteStr"
              class="c-input__control msf-select__autocomplete"
            >{{ autocompleteStr }}</span>
          </div>
          <transition name="moveup">
            <div class="msf-select__group-wrapper" v-if="showDropdown">
              <div
                v-for="(country, countryIndex) in countries"
                :key="`Countries-${index}-group-${countryIndex}`"
                class="msf-select__group"
              >
                <span class="msf-select__group-label">{{ country.group }}</span>
                <button
                  v-for="(child, childIndex) in country.children"
                  :key="`Countries-${index}-group-${countryIndex}-child-${childIndex}`"
                  :class="{'is-selected' : child.name === selection}"
                  class="msf-select__choice"
                  @click="setSelection(child.name)"
                >
                  {{ child.name }}
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_select.scss"></style>
