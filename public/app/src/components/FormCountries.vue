<script>
import { computed } from 'vue'
import countriesList from "@/assets/countries.js";

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

  setup() {
    const countries = computed(() => {
      return countriesList.reduce((r, e) => {
        // get first letter of name of current element
        let group = e.name[0];
        // if there is no property in accumulator with this letter create it
        if(!r[group]) r[group] = {group, children: [e]}
        // if there is push current element to children array for that letter
        else r[group].children.push(e);
        // return accumulator
        return r;
      }, {})
    })

    return { countries }
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
              type="text"
              class="c-input__control"
            >
          </div>
          <div
            v-for="(country, countryIndex) in countries"
            :key="`Countries-${index}-group-${countryIndex}`"
            class="msf-select__group"
          >
            <span class="msf-select__group-label">{{ country.group }}</span>
            <button
              v-for="(child, childIndex) in country.children"
              :key="`Countries-${index}-group-${countryIndex}-child-${childIndex}`"
              class="msf-select__choice"
            >
              {{ child.name }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_select.scss"></style>