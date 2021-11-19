<script>
export default {
  name: 'FormCards',
  props: {
    data: {
      type: Object,
      default: null
    },
    index: {
      type: String,
      default: ''
    }
  },
}
</script>

<template>
  <div class="msf-cards">
    <div class="msf-cards__title">{{ data.label }}</div>
    <div
      v-for="(card, cardIndex) in data.choices"
      :key="`msf-card-${cardIndex}`"
      class="msf-input msf-input--card"
    >
      <label :for="`msf-input-${index}-${cardIndex}`">{{ card.title }}</label>
      <input
        :id="`msf-input-${index}-${cardIndex}`"
        :name="`msf-cards-${form_id}`"
        type="radio"
        class="msf-card"
        :value="card.title"
      >
    </div>
  </div>
</template>