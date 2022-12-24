<template>
  <b-card-header class="pb-1 pr-0 pl-0 pt-0">
    <b-card-title class="w-100">
      <span class="mb-1 d-block d-md-inline">
        <b-avatar v-bind="getTicketBadge(ticket.type, ticket.transportMode)" />
        <span class="h2">{{ '#' + getTicketNumberFromId(ticket.id) }}</span>
        | <router-link :to="{}">{{ getUserName(user) }}</router-link>
      </span>
      <span class="float-none float-xl-right mb-1">
        <strong v-if="ticket.isDeleted" class="text-danger h5">[DELETED]</strong>
        <b-button v-else v-b-modal.delete-ticket size="sm" variant="flat-danger">
          <feather-icon size="18" icon="Trash2Icon" />
          Delete Ticket
        </b-button>
      </span>
    </b-card-title>
    <b-card-title class="w-100">
      <hr class="dropdown-divider" />
      <template v-if="client.customerNote">
        <div>
          {{ client.customerNote }}
        </div>
        <hr class="dropdown-divider" />
      </template>
    </b-card-title>
    <b-card-title class="w-100">
      <b-row>
        <b-col>
          <b-form inline>
            <label class="h5 mr-1">Agent Status: </label>
            <b-form-select
              :value="ticket.agentStatusTypeId"
              class="mb-2 mr-1 mb-sm-0 col-md-6"
              :options="agentStatusTypeOptions"
              @change="changeAgentStatusTypeId"
            >
              <template #first>
                <b-form-select-option :value="0">Select Statuses</b-form-select-option>
              </template>
            </b-form-select>
          </b-form>
        </b-col>
        <b-col md="4">
          <blank-avatar
            class="float-right"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/ico-${ticket.isverified == 0 ? 'verified-un' : 'verified'}.png`)"
          />
          <blank-avatar
            class="float-right"
            :title="affiliate.companyname"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/ico-${ticket.isreference == 0 ? 'reference-un' : 'reference'}.png`)"
          />
          <blank-avatar
            class="float-right"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/ico-${ticket.iscertificate == 0 ? 'certificate-un' : 'certificate'}.png`)"
          />
          <blank-avatar
            class="float-right"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/ico-${ticket.ispga == 0 ? 'pga-un' : 'pga'}.png`)"
          />
          <blank-avatar
            class="float-right"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/ico-${ticket.iscc == 0 ? 'cc-un' : 'cc'}.png`)"
          />
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col md="8">
          <span class="h6">UPDATE STATUS OF TICKET: </span>
          <b-button v-b-toggle:collapse-status class="mx-1" variant="primary">
            {{ ticket.status }} {{ ticket.substatus ? ' - ' + ticket.substatus : '' }}
          </b-button>
          <router-link :to="{}">New ticket</router-link>
        </b-col>
        <b-col md="4">
          <b-form-checkbox :checked="ticket.requires_broker_review" class="float-right" @change="changeRequireBrokerReview">
            Requires broker review
          </b-form-checkbox>
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col>
          <b-collapse id="collapse-status">
            <div class="mb-1 text-muted">Select Ticket Status:</div>
            <b-list-group class="col-md-4">
              <template v-for="(status, i) of groupedTicketStatuses">
                <b-list-group-item v-if="status.children.length" :key="i" class="p-0">
                  <b-dropdown variant="secondary-link" dropright no-caret block menu-class="w-100 m-0 text-secondary p-0">
                    <template #button-content>
                      {{ status.statusName }}
                      <feather-icon class="float-right" icon="ChevronRightIcon" />
                    </template>
                    <b-dropdown-item
                      v-for="(substatus, ii) of status.children"
                      :key="ii"
                      link-class="text-wrap"
                      @click="changeStatus(substatus.id)"
                    >
                      {{ substatus.statusName }}
                    </b-dropdown-item>
                  </b-dropdown>
                </b-list-group-item>
                <b-list-group-item v-else :key="i" class="h6 text-center" @click="changeStatus(status.id)">
                  {{ status.statusName }}
                </b-list-group-item>
              </template>
            </b-list-group>
          </b-collapse>
        </b-col>
      </b-row>
    </b-card-title>
    <b-modal id="delete-ticket" ok-variant="primary" ok-only ok-title="Cancel" modal-class="modal-danger" hide-footer centered>
      <template #modal-title> <feather-icon icon="Trash2Icon" /> Cancel Ticket #{{ ticket.id }} </template>
      <b-card-text>
        This action will cancel this ticket and remove it from all dashboard views.Please use the box below to provide details as to why
        this ticket is being cancelled.
      </b-card-text>
      <validation-observer ref="loginForm" #default="{ invalid }">
        <b-form @submit.prevent="deleteTicket">
          <b-form-group>
            <validation-provider #default="{ errors }" name="Deletion Reason" vid="reason" rules="required">
              <b-form-textarea v-model="reason" />
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <b-button type="submit" class="float-right" :disabled="invalid" variant="primary">Cancel</b-button>
        </b-form>
      </validation-observer>
    </b-modal>
  </b-card-header>
</template>

<script>
import {
  BCardHeader,
  BCardTitle,
  BAvatar,
  BButton,
  BFormSelect,
  BFormSelectOption,
  BForm,
  BRow,
  BCol,
  BFormCheckbox,
  BCollapse,
  VBToggle,
  BListGroup,
  BListGroupItem,
  BDropdown,
  BDropdownItem,
  VBModal,
  BCardText,
  BFormTextarea,
  BFormGroup,
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapMutations } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import { required } from '@validations'
import { getUserName } from '@/utils/user'
import { getTicketBadge, getTicketNumberFromId } from '@/utils/ticket'
import BlankAvatar from '@/components/BlankAvatar.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCardHeader,
    BCardTitle,
    BAvatar,
    BButton,
    BFormSelect,
    BFormSelectOption,
    BForm,
    BlankAvatar,
    BRow,
    BCol,
    BFormCheckbox,
    BCollapse,
    BListGroup,
    BListGroupItem,
    BDropdown,
    BDropdownItem,
    BCardText,
    BFormTextarea,
    BFormGroup,
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    'b-toggle': VBToggle,
    'b-modal': VBModal,
  },
  props: {
    ticketView: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      required,
      reason: null,
    }
  },

  computed: {
    url() {
      return process.env.APP_URL
    },
    ...mapGetters('ticket', { agentRoleStatusList: 'agentRoleStatusList' }),
    ticket() {
      return this.ticketView.ticket || {}
    },
    user() {
      return this.ticketView.ticket_user || {}
    },
    client() {
      return this.ticketView.client || {}
    },
    affiliate() {
      return this.ticketView.affiliate || {}
    },
    agentStatusTypeOptions() {
      return (this.ticketView.agentStatusTypes || []).map(i => ({ ...i, value: i.id, text: i.statusName }))
    },
    groupedTicketStatuses() {
      return this.ticketView.groupedTicketStatuses || []
    },
  },
  methods: {
    ...mapActions('ticket', {
      updateRequireBrokerReview: 'updateRequireBrokerReview',
      removeTicket: 'delete',
      updateAgentStatusType: 'updateAgentStatusType',
      updateStatus: 'updateStatus',
    }),
    ...mapMutations('ticket', { setState: 'setState' }),
    getTicketBadge,
    getTicketNumberFromId,
    getUserName,
    async changeAgentStatusTypeId(agentStatusTypeId) {
      try {
        const res = await this.updateAgentStatusType({ id: this.ticket.id, agentStatusTypeId })
        this.$emit('change')
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
      } catch (err) {
        apiResponseHandler(this, err)
      }
    },
    async changeRequireBrokerReview() {
      const checked = !this.ticket.requires_broker_review
      try {
        const data = await this.updateRequireBrokerReview({ id: this.ticket.id, requires_broker_review: checked })
        this.setState({ key: 'ticketView.ticket.requires_broker_review', value: data.requires_broker_review })
      } catch (err) {
        this.setState({ key: 'ticketView.ticket.requires_broker_review', value: !checked })
      }
    },
    async changeStatus(statusId) {
      try {
        const res = await this.updateStatus({ id: this.ticket.id, statusId })
        this.$emit('change')
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
      } catch (err) {
        apiResponseHandler(this, err)
      }
    },
    async deleteTicket() {
      try {
        const res = await this.removeTicket({ id: this.ticket.id, reason: this.reason })
        this.$emit('change')
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
      } catch (err) {
        apiResponseHandler(this, err)
      }
    },
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
