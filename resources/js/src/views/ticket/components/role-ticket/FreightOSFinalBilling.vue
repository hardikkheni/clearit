<template>
  <div id="ticket-fosfb-collapse-card" class="ticket-collapse-card">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> FreightOS Final Billing </b-card-title>
      <hr />
      <freight-os-invoice-charges :ticket-id="ticketId" :freight-invoice-docs="freightInvoiceDocs" />
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle } from 'bootstrap-vue'
import FreightOsInvoiceCharges from '@/views/ticket/components/FreightOsInvoiceCharges.vue'

export default {
  components: {
    BCard,
    BCardTitle,
    FreightOsInvoiceCharges,
  },
  filters: {},
  props: {
    ticketId: {
      type: [String, Number],
      required: true,
    },
    freightInvoiceDocs: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {}
  },
  computed: {},
  watch: {},
  mounted() {},
  methods: {},
}
</script>

<style lang="scss">
// @import '~@core/scss/base/bootstrap-extended/include';

// #ticket-fosfb-collapse-card {
//   &.ticket-fosfb-collapse-card-danger {
//     background-color: rgba($danger, 0.3);
//   }
//   &.ticket-fosfb-collapse-card-warning {
//     background-color: rgba($yellow, 0.3);
//   }
//   &.ticket-fosfb-collapse-card-success {
//     background-color: rgba($success, 0.3);
//   }
// }
</style>
