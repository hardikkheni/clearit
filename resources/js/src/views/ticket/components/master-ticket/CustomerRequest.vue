<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" :title="`Customer Requests (${requests.open.length})`">
        <template #header>
          <span class="lead collapse-title">Customer Requests ({{ requests.open.length }})</span>
          <b-button size="sm" variant="outline-success" @click.prevent="newRequest">New request</b-button>
        </template>
        <template v-if="requests.open.length">
          <span class="text-danger">Open requests:</span>
          <b-list-group class="mt-1">
            <b-list-group-item v-for="(req, i) of requests.open" :key="i">
              <div class="clearfix">
                <div class="float-right">
                  <b-form-checkbox inline @change="setAsReceived(req.id)">Received</b-form-checkbox>
                  <b-button size="sm" variant="flat-danger" class="btn-icon">
                    <feather-icon icon="Trash2Icon" />
                  </b-button>
                </div>
                <span v-if="req.document" class="text-primary">
                  <feather-icon class="mr-1" icon="FileTextIcon" />
                  {{ req.document }}
                  <template v-if="!req.description">({{ req.createdOn | dateFormat(DATE_FORMAT, '/') }})</template>
                </span>
                <div v-if="req.description" :class="{ 'pl-2': req.document }">
                  <feather-icon v-if="!req.document" class="text-warning mr-1" icon="InfoIcon" />
                  {{ req.description }} ({{ req.createdOn | dateFormat(DATE_FORMAT, '/') }})
                </div>
              </div>
            </b-list-group-item>
          </b-list-group>
          <hr />
        </template>
        <template v-if="requests.closed.length">
          <span class="text-primary">
            Close requests:
            <feather-icon
              v-b-tooltip="showCloseCustReq ? `Hide` : `Show`"
              class="ml-1 text-primary"
              :icon="showCloseCustReq ? `EyeOffIcon` : `EyeIcon`"
              @click="showCloseCustReq = !showCloseCustReq"
            />
          </span>
          <b-list-group v-if="showCloseCustReq" class="mt-1">
            <b-list-group-item v-for="(req, i) of requests.closed" :key="i">
              <div class="clearfix">
                <div class="float-right">
                  <span>{{ req.createdOn | dateFormat(DATE_FORMAT, '/') }}</span>
                </div>
                <span v-if="req.document">
                  <feather-icon class="mr-1 text-primary" icon="FileTextIcon" />
                  {{ req.document }}
                  <template v-if="!req.description">({{ req.createdOn | dateFormat(DATE_FORMAT, '/') }})</template>
                </span>
                <div v-if="req.description" :class="{ 'pl-2': req.document }">
                  <feather-icon v-if="!req.document" class="text-warning mr-1" icon="InfoIcon" />
                  {{ req.description }} ({{ req.createdOn | dateFormat(DATE_FORMAT, '/') }})
                </div>
              </div>
            </b-list-group-item>
          </b-list-group>
        </template>
      </app-collapse-item>
    </app-collapse>
    <customer-request-modal
      v-model="showCustReqModal"
      :ticket="ticket"
      :client="client"
      :doc-upload-types="docUploadTypes"
      @saved="$emit('saved')"
    />
  </div>
</template>

<script>
import { BButton, BListGroup, BListGroupItem, BFormCheckbox, VBTooltip } from 'bootstrap-vue'
import { mapActions } from 'vuex'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import CustomerRequestModal from '@/views/ticket/components/master-ticket/CustomerRequestModal.vue'

import { dateFormat } from '@/utils/filters'
import { DATE_FORMAT } from '@/utils/config'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BButton,
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BFormCheckbox,
    CustomerRequestModal,
  },
  filters: {
    dateFormat,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    clientRequests: {
      type: Array,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
    client: {
      type: Object,
      required: true,
    },
    docUploadTypes: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
      showCloseCustReq: false,
      showCustReqModal: false,
    }
  },
  computed: {
    requests() {
      return this.clientRequests.reduce(
        (accu, i) => {
          if (i.receivedOn) {
            accu.closed.push(i)
          } else {
            accu.open.push(i)
          }
          return accu
        },
        { open: [], closed: [] },
      )
    },
  },
  methods: {
    ...mapActions('customer-request', { maskAsReceived: 'maskAsReceived' }),
    newRequest(e) {
      e.stopPropagation()
      this.showCustReqModal = true
    },
    async setAsReceived(id) {
      try {
        const res = await this.maskAsReceived(id)
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
        this.$emit('saved')
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
  },
}
</script>
