<template>
  <b-row>
    <b-col cols md="7" sm="12">
      <b-card class="shadow-none">
        <ticket-header :ticket-view="ticketView || {}" @change="reload" />
        <b-card-body class="pb-1 pr-0 pl-0 pt-0">
          <reminders
            :ticket="ticket"
            :checked-reminders="ticketView.checkedReminderItems || []"
            :reminders="ticketView.reminderItems || []"
            :agents="agents"
            @reload="reload"
          />
          <customer-request
            :client-requests="ticketView.clientRequests || []"
            :ticket="ticket"
            :client="client"
            :doc-upload-types="documentUploadTypes"
            @saved="reload"
          />
          <customer-details
            :affiliates="ticketView.affiliates || []"
            :ticket="ticket"
            :client="client"
            :ticket-affiliate="ticketView.ticketAffiliate || {}"
            @removed="reload"
          />
          <sold-to-details :sold-to="ticketView.soldTo || {}" />
          <carrier-details :ticket="ticket" :isf-consolidator-list="ticketView.isfConsolidatorList || []" @saved="reload" />
          <track-and-trace :ticket="ticket" :ct-detail="containerTrakingDetail" />
          <billing :ticket="ticket" :count-charge="ticketView.count_charge || 0" :client="client" @saved="reload" />
          <final-invoice-charges
            v-if="isFreightosTicket"
            :ticket-id="ticket.ticketId || 0"
            :freight-invoice-docs="ticketView.freightInvoiceDocuments || []"
          />
          <pga-documents
            :pga-docs="ticketView.pgaDocuments || []"
            :pga-reqs="ticketView.pgaRequests || []"
            :ticket="ticket"
            @saved="reload"
          />
          <upload-documents
            :ticket="ticket"
            :docs="ticketView.documents || []"
            :missing-docs="ticketView.missingDocuments || []"
            :doc-upload-types="documentUploadTypes"
            @saved="reload"
            @removed="reload"
          />
          <ticket-hts
            :ticket="ticket"
            :commodities="commodities"
            :hts-codes="ticketView.htsCodes || []"
            :client="client"
            @reload="reload"
          />
          <customer-invoices :invoices="ticketView.invoices || []" />
          <personal-identification-upload v-if="showPifc" :upds="uploadedPersonalDocs" />
          <ticket-actions :notifications="notifications" />
        </b-card-body>
      </b-card>
    </b-col>
    <b-col cols md="5" sm="12">
      <b-card class="shadow-none">
        <assign-agent :ticket="ticket" :agents="agents" :agent="agent" :processing-agent="processingAgent" @change="reload" />
      </b-card>
      <b-card class="shadow-none">
        <ticket-message :messages="messages" />
      </b-card>
      <b-card class="shadow-none">
        <ticket-note :notes="notes" :ticket="ticket" />
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import { BCard, BCardBody, BRow, BCol } from 'bootstrap-vue'
import { mapActions } from 'vuex'

import TicketHeader from '@/views/ticket/components/master-ticket/TicketHeader.vue'
import Reminders from '@/views/ticket/components/master-ticket/Reminders.vue'
import CustomerRequest from '@/views/ticket/components/master-ticket/CustomerRequest.vue'
import CustomerDetails from '@/views/ticket/components/master-ticket/CustomerDetails.vue'
import SoldToDetails from '@/views/ticket/components/master-ticket/SoldToDetails.vue'
import CarrierDetails from '@/views/ticket/components/master-ticket/CarrierDetails.vue'
import TicketMessage from '@/views/ticket/ticket-message/index.vue'
import TicketNote from '@/views/ticket/ticket-note/index.vue'

import TrackAndTrace from '@/views/ticket/components/master-ticket/TrackAndTrace.vue'
import Billing from '@/views/ticket/components/master-ticket/Billing.vue'
import PgaDocuments from '@/views/ticket/components/master-ticket/PgaDocuments.vue'
import FinalInvoiceCharges from '@/views/ticket/components/master-ticket/FinalInvoiceCharges.vue'
import UploadDocuments from '@/views/ticket/components/master-ticket/UploadDocuments.vue'
import TicketHts from '@/views/ticket/components/master-ticket/TicketHts.vue'
import CustomerInvoices from '@/views/ticket/components/master-ticket/CustomerInvoices.vue'
import TicketActions from '@/views/ticket/components/master-ticket/TicketActions.vue'

import { DOCREVIEW, ISF, PREKEY, ARRIVAL, DELIVERY } from '@/utils/roles'
import { USER_STATUS_PERSONAL } from '@/utils/user'
import PersonalIdentificationUpload from '@/views/ticket/components/PersonalIdentificationUpload.vue'
import AssignAgent from '@/views/ticket/components/master-ticket/AssignAgent.vue'

export default {
  components: {
    BCard,
    BCardBody,
    BRow,
    BCol,
    Reminders,
    CustomerRequest,
    CustomerDetails,
    SoldToDetails,
    CarrierDetails,
    TicketHeader,
    TicketMessage,
    TicketNote,
    TrackAndTrace,
    Billing,
    PgaDocuments,
    FinalInvoiceCharges,
    UploadDocuments,
    TicketHts,
    CustomerInvoices,
    TicketActions,
    PersonalIdentificationUpload,
    AssignAgent,
  },
  props: {
    ticketView: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      DOCREVIEW,
      ISF,
      PREKEY,
      ARRIVAL,
      DELIVERY,
    }
  },
  computed: {
    ticket() {
      return this.ticketView.ticket || {}
    },
    currentRole() {
      return this.ticketView.role || {}
    },
    role() {
      return key => this.ticketView?.rolesList?.[key] || {}
    },
    isFreightosTicket() {
      return !!this.ticketView.isFreightosTicket
    },
    messages() {
      return (this.ticketView?.messages || []).map(i => ({ ...i, userId: i.userid }))
    },
    commodities() {
      // eslint-disable-next-line eqeqeq
      return (this.ticketView.commodities || []).filter(i => i.tuhId && i.ticketId == this.ticket.id)
    },
    notifications() {
      return this.ticketView.ticketNotifications || []
    },
    client() {
      return this.ticketView.client || {}
    },
    uploadedPersonalDocs() {
      return this.ticketView.uploadedPersonalDocs || []
    },
    showPifc() {
      // eslint-disable-next-line eqeqeq
      return this.client.status == USER_STATUS_PERSONAL && this.uploadedPersonalDocs.length
    },
    agents() {
      return this.ticketView.agents || []
    },
    agent() {
      return this.ticketView.agent || {}
    },
    processingAgent() {
      return this.ticketView.processingAgent || {}
    },
    documentUploadTypes() {
      return this.ticketView.documentUploadTypes || []
    },
    containerTrakingDetail() {
      return this.ticketView.containerTrakingDetail || {}
    },
    notes() {
      return this.ticketView.notes || []
    },
  },
  methods: {
    ...mapActions('ticket', { getTicket: 'getTicket' }),
    async reload(id) {
      this.$emit('scroll-to', id)
      await this.getTicket({
        ...this.$route.params,
      })
    },
  },
}
</script>
