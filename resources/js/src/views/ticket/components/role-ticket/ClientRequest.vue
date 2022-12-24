<template>
  <div class="ticket-collapse-card">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title>
        Customer requests <feather-icon icon="HelpCircleIcon" />
        <b-button class="float-right" size="sm" variant="outline-success">Add</b-button>
      </b-card-title>
      <hr />
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle, BButton } from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardTitle,
    BButton,
  },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
    currentRole: {
      type: Object,
      required: true,
    },
  },
}
</script>
