<script>
import _capitalize from 'lodash.capitalize'
import _camelCase from 'lodash.camelcase'
import { computed } from 'vue'

import FormInput from '@/components/FormInput.vue'
import FormCounter from '@/components/FormCounter.vue'
import FormSelect from '@/components/FormSelect.vue'
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
    }
  },

  setup(props) {
    const prepareCompName = (name) => _capitalize(_camelCase(name))
    const groups = computed(() => props.step.groups)
    const duplicates = computed(() => [])
    const duplicateFields = () => {
      groups.value.forEach((group, groupIndex) => {
        const hasDuplicator = group.fields.find(field => field['acf_fc_layout'] === 'duplicate')

        if (hasDuplicator) {
          const { fields } = groups.value[groupIndex]

          groups.value[groupIndex].fields = [...fields, ...fields]
        }
      })
    }

    return { prepareCompName, duplicateFields, groups, duplicates }
  }
}
</script>

<template>
  <article
      class="msf-step"
  >
    <div
        v-for="(group, groupIndex) in step.groups"
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
                @duplicate="duplicateFields"
                :is="`Form${prepareCompName(input.acf_fc_layout)}`"
                v-for="(input, inputIndex) in group.fields"
                :key="`mosa-forms_s-${currentStep}-g-${groupIndex}-i-${inputIndex}`"
                :data-comp-name="`Form${prepareCompName(input.acf_fc_layout)}`"
                :data="input"
                :index="`${currentStep}-${groupIndex}-${inputIndex}`"
            />
          </div>
        </div>
      </div>
    </div>
  </article>
</template>
