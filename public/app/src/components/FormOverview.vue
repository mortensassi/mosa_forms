<script>
import _capitalize from 'lodash.capitalize'
import _camelCase from 'lodash.camelcase'
import _groupBy from 'lodash.groupby'
import store from '@/store'
import {computed, ref} from 'vue'
import Groupedcheckboxes from '@/components/Overview/OverviewGroupedcheckboxes.vue'
import Multiselect from '@/components/Overview/OverviewMultiselect.vue'
import Pricerange from '@/components/Overview/OverviewPricerange.vue'
import Counter from '@/components/Overview/OverviewCounter.vue'
import Choices from '@/components/Overview/OverviewChoices.vue'
import Input from '@/components/Overview/OverviewInput.vue'
import Countries from '@/components/Overview/OverviewCountries.vue'
import Pricerangesingle from '@/components/Overview/OverviewPricerangesingle.vue'
import Select from '@/components/Overview/OverviewSelect.vue'
import Buttongroup from '@/components/Overview/OverviewButtongroup.vue'


export default {
  name: 'FormOverview',

  components: {
    Groupedcheckboxes,
    Multiselect,
    Pricerange,
    Counter,
    Choices,
    Input,
    Countries,
    Pricerangesingle,
    Select,
    Buttongroup
  },

  props: {
    data: {
      type: Object,
      default: null
    }
  },

  emits: ['closeOverview'],

  setup(props, { emit }) {
    const { entries } = store.state.form

    const goToStep = async (step) => {
      await emit('closeOverview')
      store.updateStep(step)
    }

    const collection = computed(() => {
      const steps = JSON.parse(JSON.stringify(store.state.form.entries))

      steps.forEach(step => {
        step.groups.forEach(group => {
          if (group.fields.find(entry => {
            if (!entry) return
            return entry.subgroup !== undefined
          })) {
            group.fields = _groupBy(group.fields, 'subgroup')
          }
        })
      })

      return steps
    })

    const fieldFileName = (value) => {
      return _capitalize(_camelCase(value))
    }

    return { entries, goToStep, collection, fieldFileName }
  }
}
</script>

<template>
  <div class="msf-overview">
    <div
      v-for="(step, stepIndex) in collection"
      :key="`msf-overview-step-${stepIndex}`"
      class="msf-overview__step"
    >
      <div class="columns is-multiline">
        <div class="column is-12 is-3-desktop">
          <h2 class="msf-step__group-title">
            {{ step.title }}
          </h2>
        </div>
        <div class="column is-12 is-8-desktop is-offset-1-desktop">
          <div
            v-for="(group, groupIndex) in step.groups"
            :key="`step-${stepIndex}-group-${groupIndex}`"
            class="msf-overview-element"
          >
            <div
              class="msf-overview-element__fields"
              :class="{ 'msf-overview-element__fields--with-duplicates' : group.duplicates }"
            >
              <div v-if="typeof group.fields === Object">
                <pre>'yoo'</pre>
                <div
                  v-for="(subgroup, subgroupIndex) in group.fields"
                  :key="`step-${stepIndex}-group-${groupIndex}-subgroup-${subgroupIndex}`"
                >
                  <div
                    v-for="(field, fieldIndex) in subgroup"
                    :key="`step-${stepIndex}-group-${groupIndex}-subgroup-${subgroupIndex}-field-${fieldIndex}`"
                  >
                    <div v-if="field && field.type">
                      <component
                        :is="fieldFileName(field.type)"
                        :data="field"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <div
                  v-for="(field, fieldIndex) in group.fields"
                  :key="`step-${stepIndex}-group-${groupIndex}-field-${fieldIndex}`"
                >
                  <div v-if="field && field.type">
                    <component
                      :is="fieldFileName(field.type)"
                      :data="field"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button
            class="c-btn c-btn--secondary"
            @click="goToStep(stepIndex)"
          >
            Angaben Bearbeiten
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style src="@styles/components/_overview.scss"></style>
