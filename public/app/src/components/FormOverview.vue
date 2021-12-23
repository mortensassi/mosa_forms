<script>
import store from '@/store'

export default {
  name: 'FormOverview',

  emits: ['closeOverview'],

  setup(props, { emit }) {
    const { entries } = store.state.form

    const goToStep = async (step) => {
      await emit('closeOverview')
      store.updateStep(step)
    }

    return { entries, goToStep }
  }
}
</script>

<template>
  <div class="msf-overview">
    <div
      v-for="(step, stepIndex) in entries.steps"
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
          <pre>{{ step.groups }}</pre>
          <button @click="goToStep(stepIndex)">
            Angaben Bearbeiten
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
