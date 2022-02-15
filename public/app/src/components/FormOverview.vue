<script>
import {ref, computed} from 'vue'
import _capitalize from 'lodash.capitalize'
import _camelCase from 'lodash.camelcase'
import store from '@/store'
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
import _groupBy from 'lodash.groupby'


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
    },
    acf: {
      type: Object,
      default: null
    },
    currentStep: {
      type: Number,
      default: 0
    },
    showOverview: {
      type: Boolean,
      default: false
    }
  },

  emits: {
    closeOverview: {
      type: Boolean,
      default: false
    },
    goToStep: {
      type: Number|String,
      default: null
    }
  },

  setup(props, { emit }) {
    const { entries } = store.state.form
    const complianceUserOptIn = ref(0)
    const submissionError = ref(false)
    const userCompliance = computed(() => {
      const complianceCount = props.acf.compliance_opt_check.length
      return complianceCount === complianceUserOptIn.value
    })

    const collection = computed(() => {
      const {steps} = store.state.form.entries
      const stepsCopy = JSON.parse(JSON.stringify(steps))

      stepsCopy.forEach(step => {
        step.groups.forEach(group => {
          const groupedFields = field => {
            if (!field) return
            return field.subgroup !== undefined && field.subgroup >= 0
          }

          if (group.fields.find(groupedFields)) {
            const filteredFields = group.fields.filter(groupedFields)
            group.fields = _groupBy(filteredFields, 'subgroup')
          }
        })
      })

      return stepsCopy
    })

    const goToStep = async (step) => {
      store.updateStep(step)
      emit('closeOverview')
    }

    const fieldFileName = (value) => {
      return _capitalize(_camelCase(value))
    }

    const updateComplianceState = (evt) => {
      if (evt.target.checked) {
        complianceUserOptIn.value += 1
      } else {
        complianceUserOptIn.value -= 1
      }
    }

    const submitRequest = async () => {
      const { vowo } = window;

      const data = store.state.form.entries.steps
      const inquiry =  []

      data.forEach(step => {
        step.groups.forEach(group => {
          group.fields.forEach(field => {

            let key = field.value.fieldname || field.name

            if (field.subgroup && field.subgroup > 0) {
              key = key + (field.subgroup + 1)
            }

            if (field && field.type === 'field.name') {
              inquiry.push({ [key] : field.value.selection.map(option => option.value) })
            } else if(field && field.type === 'multiselect') {
              inquiry.push({ [key] : field.value.selection.map(option => option.fieldname) })
            } else if(field && field.type === 'grouped_checkboxes') {
              inquiry.push({ [key] : field.value.selection.map(option => option.fieldname) })
            } else if(field && field.type === 'choices') {
              inquiry.push({ [key] : field.value.value.fieldname })
            } else if(field && field.type === 'price_range') {
              const collection = field.value.inputData.collection
              if (collection.length > 1) {
                collection.forEach((item, i) => {
                  key = field.value.fieldname[i] || field.name
                  inquiry.push({ [key] : item.val })
                })
              } else {
                key = field.value.fieldname[1] || field.name
                inquiry.push({ [key] : collection[0].val })
              }
            } else if(field && field.type === 'button_group') {
              inquiry.push({ [key] : field.value.selection.map(option => option.fieldname) })
            } else if(field && field.type === 'counter') {
              inquiry.push({ [key] : field.value.userInput.map(option => option.value) })
            } else if(field && field.type === 'select') {
              inquiry.push({ [key] : field.value.userInput.fieldname })
            } else if (field && field.type === 'input') {
              inquiry.push({ [key] : field.value.userInput })
            } else if (field && field.type === 'countries') {
              inquiry.push({ [key] : field.value.userInput.alpha2 })
            }
          })
        })
      })

      const formData = new FormData();
      formData.append('action', 'doInquiryOfPetition')
      formData.append('nonce',  vowo.nonce);
      formData.append('inquiry', JSON.stringify(inquiry))

      let response = await fetch(vowo.ajaxurl, {
        method: 'POST',
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const res = await response.json()

      if (res.success) {
        store.setResponse(res)
        goToStep('after-submit')
      } else {
        submissionError.value = true
      }
    };


    return {
      entries,
      goToStep,
      fieldFileName,
      userCompliance,
      updateComplianceState,
      submitRequest,
      collection,
      submissionError
    }
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
              <div v-if="Array.isArray(group.fields)" style="display: contents;">
                <div
                  v-for="(field, fieldIndex) in group.fields"
                  :key="`step-${stepIndex}-group-${groupIndex}-field-${fieldIndex}`"
                  class="msf-overview-element"
                >
                  <div v-if="field && field.type">
                    <component
                      :is="fieldFileName(field.type)"
                      :data="field"
                    />
                  </div>
                </div>
              </div>
              <div v-else>
                <div
                  v-for="(subgroup, subgroupIndex) in group.fields"
                  :key="`step-${stepIndex}-group-${groupIndex}-subgroup-${subgroupIndex}`"
                  class="msf-overview-element__fields msf-overview-element__fields--sub"
                >
                  <div class="msf-overview-element__title">
                    {{ `${group.title} ${group.fields.length > 0 ? Number(subgroupIndex) + 1 : ''}` }}
                  </div>
                  <div
                    v-for="(field, fieldIndex) in subgroup"
                    :key="`step-${stepIndex}-group-${groupIndex}-subgroup-${subgroupIndex}-field-${fieldIndex}`"
                  >
                    <div
                      v-if="field && field.type"
                      class="msf-overview-element__field"
                    >
                      <component
                        :is="fieldFileName(field.type)"
                        :data="field"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button
            class="c-btn c-btn--secondary msf-overview-button"
            @click="goToStep(stepIndex)"
          >
            Angaben Bearbeiten
          </button>
        </div>
      </div>
    </div>
    <div class="msf-overview__step columns is-multiline">
      <div class="column is-12 is-3-desktop">
        <h2 class="msf-step__group-title">
          {{ acf.compliance_headline }}
        </h2>
      </div>
      <div class="column is-12 is-8-desktop is-offset-1-desktop">
        <div v-if="acf.compliance_opt_check">
          <div
            v-for="(input, inputIndex) in acf.compliance_opt_check"
            :key="`Compliance-c-${inputIndex}`"
          >
            <div class="c-input c-input--checkbox msf-input msf-input--checkbox">
              <label
                :for="`Compliance-c-${inputIndex}`"
                class="c-input__label msf-input__label msf-input__label--checkbox"
                v-html="input.text"
              ></label>
              <input
                :id="`Compliance-c-${inputIndex}`"
                class="c-input__control msf-input__control msf-input__control--checkbox"
                type="checkbox"
                name="checkbox"
                :required="input.is_required"
                @change="updateComplianceState"
              >
            </div>
          </div>
        </div>
        <div
          v-if="acf.compliance_additional_info"
          class="msf-overview-compliance-text"
        >
          <div
            v-for="(row, rowIndex) in acf.compliance_additional_info"
            :key="`compliance-additional-info-${rowIndex}`"
            v-html="row.text"
          />
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column is-12 is-8-desktop is-offset-4-desktop">
        <div v-if="submissionError" class="msf-info msf-info--error">
          <div class="msf-info__text">Leider ist etwas schief gelaufen!</div>
          <button class="msf-info__close" @click="submissionError = !submissionError">&times;</button>
        </div>
        <div class="msf-form__controls">
          <button
              class="msf-form__btn c-btn c-btn--secondary"
              type="button"
              @click="$emit('goToStep', -1)"
          >
            Zurück
          </button>
          <button
              v-if="showOverview"
              class="msf-form__btn msf-form__btn--submit c-btn c-btn--primary"
              :class="{ 'is-disabled' : !userCompliance }"
              type="button"
              @click="submitRequest"
          >
            Mietgesuch aufgeben
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style src="@styles/components/_overview.scss"></style>
<style src="@styles/components/_info.scss"></style>
