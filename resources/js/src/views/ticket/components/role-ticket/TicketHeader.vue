<template>
  <b-card-header class="pb-1 pr-0 pl-0 pt-0">
    <b-card-title class="w-100">
      <span class="mb-1 d-block d-md-inline">
        <b-avatar v-bind="getTicketBadge(getFullTicket.type, getFullTicket.transportMode)" />
        <span class="h2">{{ '#' + getFullTicket.ticketDisplayId }}</span>
        <span class="ml-1 d-block d-md-inline">
          <blank-avatar
            v-if="getFullTicket.accountVerified == 0"
            style="margin-right: 5px"
            :src="require('@/assets/images/icons/ico-verified-un.png')"
          />
          <a v-else :href="getFullTicket.pdfSignUrl" target="_blank">
            <blank-avatar style="margin-right: 5px" :src="require('@/assets/images/icons/ico-verified.png')" />
          </a>
          <blank-avatar
            style="margin-right: 5px"
            v-bind="{
              ...(getFullTicket.referringCompanyName ? { title: getFullTicket.referringCompanyName, style: { cursor: 'pointer' } } : {}),
            }"
            :src="require(`@/assets/images/icons/${!+getFullTicket.affiliateId ? 'ico-reference' : 'ico-reference-un'}.png`)"
          />
          <blank-avatar
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/${getFullTicket.paymentProfile == 0 ? 'ico-cc-un' : 'ico-cc'}.png`)"
          />
        </span>
        <span class="ml-1">
          <strong v-if="getFullTicket.isDeleted" class="text-danger h5">[DELETED]</strong>
          <b-button v-else variant="flat-danger">
            <feather-icon size="18" icon="XCircleIcon" />
            Cancel Ticket
          </b-button>
        </span>
      </span>
      <span class="float-none float-xl-right mb-1">
        <b-button variant="outline-success mr-1">Back</b-button>
        <b-button variant="success">Old ticket</b-button>
      </span>
    </b-card-title>
    <b-card-title v-if="getFullTicket.affiliateId" class="w-100 mb-1">
      <b-avatar
        class="affiliate-log"
        rounded="lg"
        size="46"
        :src="require(`@/assets/images/icons/${getFullTicket.affiliateCode}-icon.png`)"
      />
    </b-card-title>
    <b-card-title class="w-100">
      <span class="h4">
        {{ getFullTicket.customerName }}
      </span>
      <hr class="dropdown-divider" />
      <div><span class="h6"> Account Note: </span> {{ getFullTicket.accountNote }}</div>
      <hr class="dropdown-divider" />
    </b-card-title>
    <b-card-title class="w-100">
      <b-form>
        <label class="h5 mr-1">Agent Status: </label>
        <b-form-select :value="form.role_status" class="mb-2 mr-1 mb-sm-0 col-md-3" :options="agentRoleStatusList">
          <template #first>
            <b-form-select-option :value="0">Select Statuses</b-form-select-option>
          </template>
        </b-form-select>
        <div v-if="getFullTicket.SBmessage" class="float-right">
          ABI Status: <strong> {{ getFullTicket.SBmessage }}</strong>
        </div>
      </b-form>
    </b-card-title>
  </b-card-header>
</template>

<script>
import { BCardHeader, BCardTitle, BAvatar, BButton, BFormSelect, BFormSelectOption, BForm } from 'bootstrap-vue'
import { mapGetters } from 'vuex'

import { getTicketBadge } from '@/utils/ticket'
import BlankAvatar from '@/components/BlankAvatar.vue'

export default {
  components: { BCardHeader, BCardTitle, BAvatar, BButton, BFormSelect, BFormSelectOption, BForm, BlankAvatar },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {
        role_status: 0,
      },
    }
  },
  computed: {
    url() {
      return process.env.APP_URL
    },
    ...mapGetters('ticket', { agentRoleStatusList: 'agentRoleStatusList' }),
  },
  methods: {
    getTicketBadge,
  },
}
</script>

<style lang="scss">
.affiliate-log {
  .b-avatar-img {
    img {
      object-fit: unset;
    }
  }
}
</style>
