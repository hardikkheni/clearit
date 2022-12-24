<template>
  <b-row>
    <b-col cols md="7" sm="12">
      <b-card class="shadow-none">
        <ticket-header :get-full-ticket="ticketView.getFullTicket || {}" />
        <b-card-body class="pb-1 pr-0 pl-0 pt-0">
          <role-reminder :get-full-ticket="ticketView.getFullTicket || {}" :checked-reminders="ticketView.checkedReminderItems || []" />
          <doc-review-role
            :role="role(DOCREVIEW)"
            :current-role="currentRole"
            :get-full-ticket="ticketView.getFullTicket || {}"
            :hts-codes="ticketView.htsCodes || []"
            :ticket="ticketView.ticket || {}"
          />
          <isf-role
            :role="role(ISF)"
            :current-role="currentRole"
            :get-full-ticket="ticketView.getFullTicket || {}"
            :hts-codes="ticketView.htsCodes || []"
            :ticket="ticketView.ticket || {}"
            :get-latest-doc="ticketView.getLatestDoc || {}"
          />
          <prekey-role
            :role="role(PREKEY)"
            :current-role="currentRole"
            :get-full-ticket="ticketView.getFullTicket || {}"
            :hts-codes="ticketView.htsCodes || []"
            :ticket="ticketView.ticket || {}"
            :get-latest-doc="ticketView.getLatestDoc || {}"
            :is-freightos-ticket="isFreightosTicket"
            :freight-invoice-docs="ticketView.freightInvoiceDocuments || []"
          />
          <arrival-notice-role
            :role="role(ARRIVAL)"
            :current-role="currentRole"
            :get-full-ticket="ticketView.getFullTicket || {}"
            :hts-codes="ticketView.htsCodes || []"
            :ticket="ticketView.ticket || {}"
            :get-latest-doc="ticketView.getLatestDoc || {}"
          />
          <delivery-role :role="role(DELIVERY)" :current-role="currentRole" :get-full-ticket="ticketView.getFullTicket || {}" />
        </b-card-body>
      </b-card>
    </b-col>
    <b-col cols md="5" sm="12">
      <b-card class="shadow-none">
        <ticket-message :messages="messages" />
      </b-card>
      <b-card class="shadow-none">
        <ticket-note />
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import { BCard, BCardBody, BRow, BCol } from 'bootstrap-vue'

import RoleReminder from '@/views/ticket/components/role-ticket/RoleReminder.vue'
import DocReviewRole from '@/views/ticket/components/role-ticket/DocReviewRole.vue'
import IsfRole from '@/views/ticket/components/role-ticket/ISFRole.vue'
import PrekeyRole from '@/views/ticket/components/role-ticket/PrekeyRole.vue'
import ArrivalNoticeRole from '@/views/ticket/components/role-ticket/ArrivalNoticeRole.vue'
import DeliveryRole from '@/views/ticket/components/role-ticket/DeliveryRole.vue'

import TicketHeader from '@/views/ticket/components/role-ticket/TicketHeader.vue'

import TicketMessage from '@/views/ticket/ticket-message/index.vue'
import TicketNote from '@/views/ticket/ticket-note/index.vue'

import { DOCREVIEW, ISF, PREKEY, ARRIVAL, DELIVERY } from '@/utils/roles'

export default {
  components: {
    RoleReminder,
    DocReviewRole,
    IsfRole,
    PrekeyRole,
    ArrivalNoticeRole,
    DeliveryRole,
    BCard,
    BCardBody,
    TicketHeader,
    TicketMessage,
    TicketNote,
    BRow,
    BCol,
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
    currentRole() {
      return this.ticketView.role || {}
    },
    role() {
      return key => this.ticketView?.rolesList?.[key] || {}
    },
    isFreightosTicket() {
      return true
      // return !!this.ticketView.isFreightosTicket
    },
    messages() {
      return (this.ticketView?.messages || []).map(i => ({ ...i, userId: i.userid }))
    },
  },
}
</script>
