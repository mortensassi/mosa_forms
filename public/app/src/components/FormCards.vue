<script>
import {inject, onMounted, ref} from 'vue'

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

  setup() {
    const uuid = inject('uuid')
    const currentCard = ref(0)

    return {uuid, currentCard}
  }
}
</script>

<template>
  <div class="msf-cards">
    <div class="msf-cards__title">
      {{ data.label }}
    </div>
    <div class="msf-cards__wrapper" :style="{ transform: `translateX(calc(-100% * ${currentCard}))` }">
      <div
        v-for="(card, cardIndex) in data.choices"
        :key="`msf-card-${cardIndex}`"
        class="msf-card"
        :class="{ 'msf-card--current' : currentCard === cardIndex }"
      >
        <label
          :for="`msf-input-${index}-${cardIndex}`"
          class="msf-card__label"
        >{{ card.title }}</label>
        <input
          :id="`msf-input-${index}-${cardIndex}`"
          :name="`msf-cards-${form_id}`"
          type="radio"
          class="msf-card__control"
          :value="card.title"
          :checked="cardIndex === 0"
          @click="currentCard = cardIndex"
        >
        <div class="msf-card__content">
          <div
            class="msf-card__description"
            v-html="card.description"
          />
          <dl class="msf-card__pricing msf-deflist">
            <div
              v-for="(row, rowIndex) in card.pricing_table.pricing"
              :key="`msf-input-${index}-card-pricing-row-${rowIndex}`"
              class="msf-deflist__row"
            >
              <dt class="msf-deflist__row-title">
                {{ row.title }}
              </dt>
              <dd class="msf-deflist__row-data">
                <span
                  v-for="(price, priceIndex) in row.price"
                  :key="`msf-cards-price-${priceIndex}-${uuid}`"
                >
                  {{ price.value }}
                  <span
                    v-if="priceIndex === 0"
                    class="msf-deflist__price-separator"
                  />
                </span>
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" src="@styles/components/_cards.scss" scoped />