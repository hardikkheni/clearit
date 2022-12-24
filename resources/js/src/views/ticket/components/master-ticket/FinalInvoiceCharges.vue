<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Final Invoice Charges">
        <template #header>
          <span class="lead collapse-title">Final Invoice Charges</span>
        </template>
        <freight-os-invoice-charges :ticket-id="ticketId" :freight-invoice-docs="freightInvoiceDocs" />
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import FreightOsInvoiceCharges from '@/views/ticket/components/FreightOsInvoiceCharges.vue'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
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
  mounted() {},
  methods: {},
}
</script>
