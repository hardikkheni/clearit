<template>
  <div class="ticket-collapse ifs-role">
    <app-collapse accordion type="margin">
      <app-collapse-item ref="collapseItem" class="outline-secondary" title="">
        <template #header>
          <span class="lead collapse-title">IFS</span>
          <b-button size="sm" variant="secondary btn-icon" @click.prevent="toggleCustReqSec">
            <feather-icon icon="UserIcon" />
          </b-button>
        </template>
        <transition>
          <client-request v-if="isCustReqSecVisible" :current-role="currentRole" :get-full-ticket="getFullTicket || {}" />
        </transition>
        <commercial-invoice :role="role" :get-full-ticket="getFullTicket || {}" />
        <role-verification-check-list :get-full-ticket="getFullTicket || {}" :role="role" />
        <isf-filling :get-full-ticket="getFullTicket || {}" :role="role" :current-role="currentRole" :get-latest-doc="getLatestDoc" />
        <isf-doc-review :get-full-ticket="getFullTicket || {}" :role="role" :ticket="ticket" />
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BButton } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import ClientRequest from '@/views/ticket/components/role-ticket/ClientRequest.vue'
import CommercialInvoice from '@/views/ticket/components/role-ticket/CommercialInvoice.vue'
import RoleVerificationCheckList from '@/views/ticket/components/role-ticket/RoleVerificationCheckList.vue'
import IsfFilling from '@/views/ticket/components/role-ticket/ISFFilling.vue'
import IsfDocReview from '@/views/ticket/components/role-ticket/ISFDocReview.vue'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BButton,
    ClientRequest,
    CommercialInvoice,
    RoleVerificationCheckList,
    IsfFilling,
    IsfDocReview,
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
    role: {
      type: Object,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
    htsCodes: {
      type: Array,
      required: true,
    },
    getLatestDoc: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isCustReqSecVisible: false,
    }
  },
  computed: {
    isCollapseItemVisible() {
      return !!this.$refs.collapseItem?.visible
    },
  },
  methods: {
    toggleCustReqSec(e) {
      if (this.isCollapseItemVisible) {
        e.stopPropagation()
      }
      this.isCustReqSecVisible = !this.isCustReqSecVisible
    },
  },
}
</script>
