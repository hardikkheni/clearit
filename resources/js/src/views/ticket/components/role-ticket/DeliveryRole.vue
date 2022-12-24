<template>
  <div class="ticket-collapse delivery-role">
    <app-collapse accordion type="margin">
      <app-collapse-item ref="collapseItem" class="outline-secondary" title="">
        <template #header>
          <span class="lead collapse-title">Delivery</span>
          <b-button size="sm" variant="secondary btn-icon" @click.prevent="toggleCustReqSec">
            <feather-icon icon="UserIcon" />
          </b-button>
        </template>
        <transition>
          <client-request v-if="isCustReqSecVisible" :current-role="currentRole" :get-full-ticket="getFullTicket || {}" />
        </transition>
        <commercial-invoice :role="role" :get-full-ticket="getFullTicket || {}" />
        <role-verification-check-list :get-full-ticket="getFullTicket || {}" :role="role" />
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

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BButton,
    ClientRequest,
    CommercialInvoice,
    RoleVerificationCheckList,
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
