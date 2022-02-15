<script>
import _capitalize from 'lodash.capitalize'
import _camelCase from 'lodash.camelcase'
import {computed, watch, onMounted, ref, nextTick, inject} from 'vue'
import store from '@/store'
import {useVuelidate} from '@vuelidate/core'

import smoothscroll from 'smoothscroll-polyfill';
import FormInput from '@/components/FormInput.vue'
import FormCounter from '@/components/FormCounter.vue'
import FormSelect from '@/components/FormSelect.vue'
import FormMultiselect from '@/components/FormMultiselect.vue'
import FormTextarea from '@/components/FormTextarea.vue'
import FormCards from '@/components/FormCards.vue'
import FormGroupedcheckboxes from '@/components/FormGroupedcheckboxes.vue'
import FormPricerange from '@/components/FormPricerange.vue'
import FormPricerangesingle from '@/components/FormPricerangesingle.vue'
import FormButtongroup from '@/components/FormButtongroup.vue'
import FormCountries from '@/components/FormCountries.vue'
import FormChoices from '@/components/FormChoices.vue'
import FormDuplicate from '@/components/FormDuplicate.vue'

export default {
  name: 'FormStep',
  components: {
    FormInput,
    FormCounter,
    FormSelect,
    FormMultiselect,
    FormTextarea,
    FormCards,
    FormGroupedcheckboxes,
    FormButtongroup,
    FormPricerange,
    FormPricerangesingle,
    FormCountries,
    FormChoices,
    FormDuplicate,
  },

  props: {
    step: {
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
    goToStep: {
      type: Number|String,
      default: null
    }
  },

  setup(props, { emit }) {
    const prepareCompName = (name) => _capitalize(_camelCase(name))
    const groups = computed(() => props.step.groups)
    const computedGroups = computed({
      get: () => groups.value,
      set: (val) => groups.value.splice(val.index, val.del, val.data)
    })
    const storedFormEntries = computed(() => store.state.form.entries)
    const formData = computed(() => store.state.form.data)

    const duplicateCount = ref(0)
    const duplicates = ref([])
    const duplicateFields = () => {
      const duplicateField = computedGroups.value[duplicatorGroup.value].fields.filter(field => field.acf_fc_layout === 'duplicate')
      if (duplicateCount.value < duplicateField[0].max_count)
        duplicateCount.value += 1
      computedGroups.value.forEach((group, groupIndex) => {
        const { fields } = computedGroups.value[groupIndex]
        const duplicator = fields.find(field => field['acf_fc_layout'] === 'duplicate')

        if (duplicator) {
          const index = duplicateCount.value
          const duplicatorPosition = fields.indexOf(duplicator)
          const coreFields = fields.filter(field => !field.duplicate && field['acf_fc_layout'] !== 'duplicate' && field.in_duplicate)
          duplicates.value = coreFields.map(field => {
            return {
              ...field,
              duplicate: index,
              subgroup: field.duplicate || index
            }
          })

          computedGroups.value = {
            index: groupIndex + 1,
            del: 0,
            data: {
              title: `${groupIndex + 1}. ${computedGroups.value[duplicatorGroup.value].title}`,
              fields: duplicates.value
            }
          }

          computedGroups.value[groupIndex + 1].fields.push(computedGroups.value[groupIndex].fields.splice(duplicatorPosition, 1)[0])

          const duplicateGroupIndex = computedGroups.value.findIndex(group => group.fields.some(item => item.acf_fc_layout === 'duplicate'))

          store.duplicateFields({
            index: props.currentStep,
            groupTitle: `${groupIndex + 1}. ${group.title}`,
            groupIndex: duplicateGroupIndex,
          }, duplicateCount.value)
        }
      })
    }

    const removeDuplicate = async () => {
      duplicateCount.value -= 1
      const indexOfGroupWithDuplicator = computedGroups.value.findIndex(group => group.fields.some(field => field['acf_fc_layout'] === 'duplicate'))
      const groupWithDuplicator = computedGroups.value.find(group => group.fields.some(field => field['acf_fc_layout'] === 'duplicate'))
      const duplicator = groupWithDuplicator.fields.find(field => field['acf_fc_layout'] === 'duplicate')
      const duplicatorPosition = groupWithDuplicator.fields.indexOf(duplicator)

      computedGroups.value[indexOfGroupWithDuplicator - 1].fields.push(computedGroups.value[indexOfGroupWithDuplicator].fields.splice(duplicatorPosition, 1)[0])

      await computedGroups.value.splice(indexOfGroupWithDuplicator, 1)

      store.removeDuplicates(props.currentStep, indexOfGroupWithDuplicator)
    }

    onMounted(() => {
      if (duplicatorGroup.value && groups.value[duplicatorGroup.value]) {
        const storedGroups = storedFormEntries.value.steps[props.currentStep].groups
        if (storedGroups[duplicatorGroup.value].duplicateCount) {
          duplicateCount.value = Number(storedGroups[duplicatorGroup.value].duplicateCount)
          groups.value[duplicatorGroup.value].fields = groups.value[duplicatorGroup.value].fields.map(field => {
            if (!field.subgroup) {
              return { ...field, ...{ subgroup: 0} }
            } else {
              return field
            }
          })
        } else {
          for(let i = 0; i <= duplicateCount.value; i++) {
            groups.value[duplicatorGroup.value].fields = groups.value[duplicatorGroup.value].fields.map(field => ({ ...field, ...{ subgroup: i} }))
          }
        }
      }
    })

    const duplicatorGroup = computed(() => {
      return groups.value.findIndex(group => group.fields.find(field => field.acf_fc_layout === 'duplicate'))
    })

    const v$ = useVuelidate()

    const prepareStepChange = async (step) => {
      const result = await v$.value.$validate()

      if (result) {
        emit('goToStep', step)
      } else {
        await v$.value.$touch()
        let domRect = document.querySelector('.msf-form .c-input--error');

        if(domRect) {
          /* polyfill smoothscroll */
          smoothscroll.polyfill();
          window.scrollTo({
            top: domRect.getBoundingClientRect().top + document.documentElement.scrollTop - 100,
            behavior: 'smooth'
          });
        }
      }
    }

    return { formData, prepareCompName, duplicateFields, removeDuplicate, duplicateCount, groups, computedGroups, storedFormEntries, duplicatorGroup, prepareStepChange, v$ }
  }
}
</script>

<template>
  <article
    class="msf-step"
  >
    <div
      v-for="(group, groupIndex) in computedGroups"
      :key="`mosa-forms_s-${currentStep}-g-${groupIndex}`"
      class="msf-step__group"
    >
      <div class="columns is-multiline">
        <div class="column is-12 is-3-desktop">
          <h2 class="msf-step__group-title">
            {{ group.title }}
          </h2>
        </div>
        <div class="column is-12 is-8-desktop is-offset-1-desktop">
          <div class="msf-step__group-wrap">
            <component
              :is="`Form${prepareCompName(input.acf_fc_layout)}`"
              v-for="(input, inputIndex) in group.fields"
              :key="`mosa-forms_s-${currentStep}-g-${groupIndex}-i-${inputIndex}`"
              :field-key="`mosa-forms_s-${currentStep}-g-${groupIndex}-i-${inputIndex}`"
              :data-comp-name="`Form${prepareCompName(input.acf_fc_layout)}`"
              :data="input"
              :real-index="inputIndex"
              :step-group-index="groupIndex"
              :index="`${currentStep}-${groupIndex}-${inputIndex}`"
              :step="step"
              :group="group"
              :duplicate-count="duplicateCount"
              @duplicate="duplicateFields"
              @remove-duplicate="removeDuplicate"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column is-12 is-8-desktop is-offset-4-desktop">
        <div class="msf-form__controls">
          <button
            v-if="currentStep > 0"
            class="msf-form__btn c-btn c-btn--secondary"
            type="button"
            @click="$emit('goToStep', -1)"
          >
            Zurück
          </button>
          <button
            v-if="formData.acf.steps.length > currentStep + 1"
            class="msf-form__btn msf-form__btn--next c-btn c-btn--primary"
            :class="{ 'is-disabled' : v$.$errors.length > 0}"
            type="button"
            @click="prepareStepChange(1)"
          >
            Weiter zu Schritt {{ (currentStep + 1) + 1 }}
          </button>
          <button
            v-else-if="!showOverview"
            class="msf-form__btn msf-form__btn--submit c-btn c-btn--primary"
            :class="{ 'is-disabled' : v$.$errors.length > 0}"
            type="button"
            @click="prepareStepChange('overview')"
          >
            Zur Übersicht
          </button>
        </div>
      </div>
    </div>
  </article>
</template>
