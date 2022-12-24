<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Track And Trace">
        <template #header>
          <span class="lead collapse-title">Track And Trace </span>
        </template>
        <validation-observer ref="etaForm" #default="{ invalid }">
          <b-form @submit.prevent="submit">
            <b-list-group class="mt-1">
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Container #:</b-col>
                  <b-col sm="12" md="5">
                    <validation-provider #default="{ errors }" name="Container" vid="containerNumber">
                      <b-form-input v-model="form.containerNumber" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                  <b-col v-if="hasCtInfo" col>
                    <blank-avatar
                      v-b-modal.ct-modal
                      :style="{ height: '20px', width: '20px' }"
                      :src="require('@/assets/images/icons/ico-ocean-green.png')"
                    />
                    {{ btnLabel }}
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Master BOL:</b-col>
                  <b-col sm="12" md="5">
                    <validation-provider #default="{ errors }" name="Master BOL" vid="mBOL">
                      <b-form-input v-model="form.mBOL" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">House BOL:</b-col>
                  <b-col sm="12" md="5">
                    <validation-provider #default="{ errors }" name="House BOL" vid="hBOL">
                      <b-form-input v-model="form.hBOL" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Estimated Time of Arrival:</b-col>
                  <b-col sm="12" md="5">
                    <validation-provider #default="{ errors }" name="Estimated Time of Arrival" vid="eta">
                      <b-input-group>
                        <flat-pickr v-model="form.eta" class="rounded-left" :config="dateConfig" />
                        <b-input-group-append is-text>
                          <feather-icon icon="CalendarIcon" />
                        </b-input-group-append>
                      </b-input-group>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Last Free Day:</b-col>
                  <b-col sm="12" md="5">
                    <validation-provider #default="{ errors }" name="Last Free Day" vid="lastFreeDay">
                      <b-input-group>
                        <flat-pickr v-model="form.lastFreeDay" class="rounded-left" :config="dateConfig" />
                        <b-input-group-append is-text>
                          <feather-icon icon="CalendarIcon" />
                        </b-input-group-append>
                      </b-input-group>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Comment:</b-col>
                  <b-col col>
                    <validation-provider #default="{ errors }" name="Comment" vid="etaComment">
                      <b-form-textarea v-model="form.etaComment" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                    <b-form-checkbox v-model="form.disableEtaEmails" class="mt-1">Disable customer emails</b-form-checkbox>
                  </b-col>
                </b-row>
              </b-list-group-item>
            </b-list-group>
            <b-row class="mt-1">
              <b-col col class="clearfix">
                <b-button type="submit" :disabled="invalid" size="sm" class="float-right" variant="primary">Update</b-button>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </app-collapse-item>
    </app-collapse>
    <b-modal v-if="hasCtInfo" id="ct-modal" size="lg" hide-footer>
      <template #modal-title>
        <div>
          <blank-avatar
            v-b-modal.ct-modal
            class="mr-1"
            :style="{ height: '20px', width: '20px' }"
            :src="require('@/assets/images/icons/ico-ocean-green.png')"
          />
          Container Tracker
        </div>
      </template>
      <b-row>
        <b-col col>
          <div class="float-right">{{ modalLabel }}</div>
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col cols="12" md="3">
          <b-card border-variant="primary" bg-variant="transparent" class="shadow-none">
            <template #header>
              <h4>Inland Origin</h4>
            </template>
            <div>{{ ctDetail.inland_origin_unlocode }}</div>
            <div>{{ ctDetail.inland_origin_city }}</div>
            <div>{{ ctDetail.inland_origin_name }}</div>
            <div>{{ ctDetail.inland_origin_state }}</div>
            <div>{{ ctDetail.inland_origin_country }}</div>
          </b-card>
        </b-col>
        <b-col cols="12" md="3">
          <b-card border-variant="primary" title="Origin Port" bg-variant="transparent" class="shadow-none">
            <div>{{ ctDetail.origin_port_unlocode }}</div>
            <div>{{ ctDetail.origin_port_city }}</div>
            <div>{{ ctDetail.origin_port_name }}</div>
            <div>{{ ctDetail.origin_port_country }}</div>
          </b-card>
        </b-col>
        <b-col cols="12" md="3">
          <b-card border-variant="primary" title="Destination Port" bg-variant="transparent" class="shadow-none">
            <div>{{ ctDetail.destination_port_unlocode }}</div>
            <div>{{ ctDetail.destination_port_city }}</div>
            <div>{{ ctDetail.destination_port_name }}</div>
            <div>{{ ctDetail.destination_port_country }}</div>
          </b-card>
        </b-col>
        <b-col cols="12" md="3">
          <b-card border-variant="primary" title="Inland Destination Port" bg-variant="transparent" class="shadow-none">
            <div>{{ ctDetail.destination_port_unlocode }}</div>
            <div>{{ ctDetail.inland_destination_city }}</div>
            <div>{{ ctDetail.inland_destination_name }}</div>
            <div>{{ ctDetail.inland_destination_country }}</div>
          </b-card>
        </b-col>
      </b-row>
      <h5>Milestones</h5>
      <b-table-simple>
        <b-thead>
          <b-tr>
            <b-th>Timestamp</b-th>
            <b-th>Description</b-th>
            <b-th>Location</b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <b-tr>
            <b-td>{{ ctDetail.destination_timestamp }}</b-td> <b-td>{{ ctDetail.description }}</b-td>
            <b-td>{{ ctDetail.location_state }} {{ ctDetail.location_country }}</b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
    </b-modal>
  </div>
</template>

<script>
import {
  BListGroup,
  BListGroupItem,
  BRow,
  BCol,
  BFormInput,
  BInputGroup,
  BInputGroupAppend,
  BFormCheckbox,
  BFormTextarea,
  BButton,
  VBModal,
  BCard,
  BTableSimple,
  BTbody,
  BThead,
  BTh,
  BTr,
  BTd,
  BForm,
} from 'bootstrap-vue'
import { mapActions } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import FlatPickr from 'vue-flatpickr-component'
import dayjs from 'dayjs'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import BlankAvatar from '@/components/BlankAvatar.vue'
import { dateConfig } from '@/utils/config'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BRow,
    BCol,
    BFormInput,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
    BFormTextarea,
    BButton,
    FlatPickr,
    BlankAvatar,
    BCard,
    BTableSimple,
    BTbody,
    BThead,
    BTh,
    BTr,
    BTd,
    ValidationProvider,
    ValidationObserver,
    BForm,
  },
  filters: {},
  directives: {
    'b-modal': VBModal,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    ctDetail: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dateConfig,
      form: {
        containerNumber: null,
        mBOL: null,
        hBOL: null,
        eta: null,
        etaComment: null,
        disableEtaEmails: false,
      },
    }
  },
  computed: {
    hasCtInfo() {
      return !!this.ctDetail.id
    },
    btnLabel() {
      return `Gate out empty ${dayjs(this.ctDetail.destination_timestamp).format('MM/DD/YYYY hh:MM a')}`
    },
    modalLabel() {
      return `Container Id / ISO: ${this.ctDetail.container_id} / ${this.ctDetail.container_iso}`
    },
  },
  mounted() {
    this.form.containerNumber = this.ticket.containerNumber
    this.form.mBOL = this.ticket.mBOL
    this.form.hBOL = this.ticket.hBOL
    this.form.etaComment = this.ticket.etaComment
    this.form.disableEtaEmails = this.ticket.disableEtaEmails
    this.form.lastFreeDay = this.ticket.lastFreeDay
    this.form.eta = this.ticket.eta
  },
  methods: {
    ...mapActions('ticket', { addEta: 'addEta' }),
    async submit() {
      try {
        const res = await this.addEta({ id: this.ticket.id, data: this.form })
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
        apiResponseHandler(this, err, {}, this.$refs.etaForm)
      }
    },
  },
}
</script>
