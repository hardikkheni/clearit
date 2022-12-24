<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Carrier details">
        <template #header>
          <span class="lead collapse-title">Carrier details </span>
        </template>
        <validation-observer ref="clientDetailsForm" #default="{ invalid }">
          <b-form @submit.prevent="submit">
            <b-list-group class="mt-1">
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="4">Mode of Transport:</b-col>
                  <b-col col>
                    <b-form-radio-group v-model="form.transport" :options="typeOfOptions" @change="transportChangeHandler" />
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-collapse :visible="needToBeFiled">
                <b-list-group-item>
                  <b-row>
                    <b-col sm="12" md="4">ISF Filed:</b-col>
                    <b-col sm="12" md="3"><b-form-checkbox v-model="form.isffiled"> ISF has been filed</b-form-checkbox></b-col>
                    <b-col v-if="form.isffiled" sm="12" md="5">
                      <b-input-group>
                        <flat-pickr v-model="form.isfFiledOn" class="rounded-left" :config="dateConfig" />
                        <b-input-group-append is-text>
                          <feather-icon icon="CalendarIcon" />
                        </b-input-group-append>
                      </b-input-group>
                    </b-col>
                  </b-row>
                </b-list-group-item>
              </b-collapse>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="4">Carrier:</b-col>
                  <b-col col><b-form-input v-model="form.carrier" /></b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="4"> {{ PAPS_NAME }}/Cargo Control Number:</b-col>
                  <b-col col><b-form-input v-model="form.vendor_carrier_ref" /></b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="4"> Entry Number:</b-col>
                  <b-col col>
                    <validation-provider #default="{ errors }" name="Entry Number" vid="SBentryNum" rules="min:9">
                      <b-input-group :prepend="`${form.SBfilerCode} - `">
                        <b-form-input v-model="form.SBentryNum" v-mask="entryNumbderMask" masked />
                      </b-input-group>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-collapse :visible="isDeliveryRequired">
                <b-list-group-item>
                  <b-row>
                    <b-col cols="12" class="mb-1">
                      <b-form-checkbox v-model="form.requiresDelivery">Requires Delivery</b-form-checkbox>
                    </b-col>
                    <b-col cols="12">
                      <b-collapse :visible="form.requiresDelivery">
                        <b-row>
                          <b-col sm="12" md="4"> Delivery Type:</b-col>
                          <b-col col>
                            <b-form-select v-model="form.deliverySelection" :options="ticketDeliveryOptions">
                              <template #first>
                                <b-form-select-option :value="null">Select</b-form-select-option>
                              </template>
                            </b-form-select>
                          </b-col>
                        </b-row>
                        <b-collapse :visible="mayHaveLoadingDock">
                          <b-row class="mt-1">
                            <b-col sm="12" md="4">Is there a Loading Dock available?</b-col>
                            <b-col col>
                              <b-form-radio-group v-model="form.haveLoadingDock" :options="requiredOptions" />
                            </b-col>
                          </b-row>
                        </b-collapse>
                        <b-row class="mt-1">
                          <b-col sm="12" md="4"> Is a Lift-gate required?</b-col>
                          <b-col col>
                            <b-form-radio-group v-model="form.requiresLiftGate" :options="requiredOptions" />
                          </b-col>
                        </b-row>
                        <b-row class="mt-1">
                          <b-col sm="12" md="4"> Delivery Address:</b-col>
                          <b-col col>
                            <b-form-textarea v-model="form.deliveryAddress" />
                          </b-col>
                        </b-row>
                      </b-collapse>
                    </b-col>
                  </b-row>
                </b-list-group-item>
              </b-collapse>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="4"> Freight Forwarder:</b-col>
                  <b-col col>
                    <b-form-select
                      v-model="form.ISFConsolidator_id"
                      :options="ISFConsolidators"
                      @change="getContactsOfForForwarder(form.ISFConsolidator_id)"
                    >
                      <template #first>
                        <b-form-select-option :value="null">Select a Freight Forwarder</b-form-select-option>
                      </template>
                    </b-form-select>
                  </b-col>
                </b-row>
                <b-row v-if="form.ISFConsolidator_id" class="mt-1">
                  <b-col sm="12" md="4"> Freight Forwarder Contact:</b-col>
                  <b-col col>
                    <b-overlay variant="light" opacity="0.5" blur="0px" rounded="sm">
                      <b-form-select v-model="form.ISFConsolidatorContact_id" :options="contactsOfForwarder">
                        <template #first>
                          <b-form-select-option :value="null">Select a contact</b-form-select-option>
                          <b-form-select-option :value="0">Add a new contact</b-form-select-option>
                        </template>
                      </b-form-select>
                    </b-overlay>
                  </b-col>
                </b-row>
                <b-collapse :visible="createContacts" class="mt-1">
                  <hr />
                  <b-row>
                    <b-col sm="12" md="4"> Contact Name:<span class="text-danger">*</span></b-col>
                    <b-col col>
                      <b-form-input v-model="form.isfcName" />
                    </b-col>
                  </b-row>
                  <b-row class="mt-1">
                    <b-col sm="12" md="4"> Contact email address:<span class="text-danger">*</span></b-col>
                    <b-col col>
                      <validation-provider #default="{ errors }" name="Contact email address" vid="isfcEmailAddress" rules="email">
                        <b-form-input v-model="form.isfcEmailAddress" />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-col>
                  </b-row>
                  <b-row class="mt-1">
                    <b-col sm="12" md="4"> Contact phone number:</b-col>
                    <b-col col>
                      <b-form-input v-model="form.isfcMobilePhone" />
                    </b-col>
                  </b-row>
                </b-collapse>
              </b-list-group-item>
            </b-list-group>
            <b-row class="mt-1">
              <b-col col class="clearfix">
                <b-button type="submit" class="float-right" variant="primary" :disabled="invalid">Update Shipping Details</b-button>
              </b-col>
            </b-row>
          </b-form>
        </validation-observer>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
/* eslint-disable object-shorthand */
/* eslint-disable radix */
import {
  BListGroup,
  BListGroupItem,
  BRow,
  BCol,
  BFormRadioGroup,
  BFormInput,
  BInputGroup,
  BInputGroupAppend,
  BFormCheckbox,
  BCollapse,
  BFormSelect,
  BFormSelectOption,
  BFormTextarea,
  BButton,
  BOverlay,
  BForm,
} from 'bootstrap-vue'
import { mapActions, mapGetters } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import FlatPickr from 'vue-flatpickr-component'
import { mask } from 'vue-the-mask'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import { PAPS_NAME, dateConfig } from '@/utils/config'
import { entryNumbderMask } from '@/utils/masks'

import {
  ticketDeliverySelection,
  transportModesRequiresDelivery,
  TICKET_TRANSPORT_OCEAN,
  TICKET_TRANSPORT_TRUCK,
  TICKET_TRANSPORT_AIR,
  TICKET_TRANSPORT_COURIER,
  TICKET_DELIVERY_SELECTION_COMMERCIAL,
} from '@/utils/ticket'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BRow,
    BCol,
    BFormRadioGroup,
    BFormInput,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
    BCollapse,
    BFormSelect,
    BFormSelectOption,
    BFormTextarea,
    BButton,
    BOverlay,
    ValidationProvider,
    ValidationObserver,
    BForm,
    FlatPickr,
  },
  filters: {},
  directives: {
    mask,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    isfConsolidatorList: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      PAPS_NAME,
      // eslint-disable-next-line radix
      ticketDeliveryOptions: Object.keys(ticketDeliverySelection).map(i => ({ text: ticketDeliverySelection[i], value: parseInt(i) })),
      requiredOptions: [
        { text: 'Yes', value: true },
        { text: 'No', value: false },
      ],
      typeOfOptions: [
        { text: 'Truck', value: TICKET_TRANSPORT_TRUCK },
        { text: 'Ocean', value: TICKET_TRANSPORT_OCEAN },
        { text: 'Air', value: TICKET_TRANSPORT_AIR },
        { text: 'Courier', value: TICKET_TRANSPORT_COURIER },
      ],
      form: {
        transport: null,
        requiresLiftGate: false,
        isffiled: false,
        isfFiledOn: null,
        carrier: null,
        vendor_carrier_ref: null,
        SBfilerCode: '8PY',
        SBentryNum: null,
        requiresDelivery: false,
        haveLoadingDock: false,
        deliverySelection: null,
        deliveryAddress: null,
        ISFConsolidator_id: null,
        ISFConsolidatorContact_id: null,
        isfcName: null,
        isfcEmailAddress: null,
        isfcMobilePhone: null,
      },
      dateConfig,
      entryNumbderMask,
    }
  },
  computed: {
    ...mapGetters('freight-forwarder', { contactsOfForwarder: 'contactsOfForwarder' }),
    ISFConsolidators() {
      return this.isfConsolidatorList.map(i => ({ ...i, value: i.id, text: i.isfcName }))
    },
    createContacts() {
      return this.form.ISFConsolidatorContact_id === 0
    },
    isDeliveryRequired() {
      return transportModesRequiresDelivery.includes(this.form.transport)
    },
    needToBeFiled() {
      return this.form.transport === TICKET_TRANSPORT_OCEAN
    },
    mayHaveLoadingDock() {
      return this.form.deliverySelection === TICKET_DELIVERY_SELECTION_COMMERCIAL
    },
  },
  watch: {
    async 'form.ISFConsolidator_id'() {
      if (this.form.ISFConsolidator_id) {
        this.form.ISFConsolidatorContact_id = null
      }
    },
  },
  async mounted() {
    this.form.carrier = this.ticket.carrier
    this.form.transport = parseInt(this.ticket.transport || 0)
    this.form.isffiled = this.ticket.isffiled
    this.form.isfFiledOn = this.ticket.isfFiledOn
    this.form.sbentrynum = this.ticket.sbentrynum
    this.form.ISFConsolidator_id = parseInt(this.ticket.ISFConsolidator_id || 0) || null
    await this.getContactsOfForForwarder(this.form.ISFConsolidator_id)
    this.form.ISFConsolidatorContact_id = parseInt(this.ticket.ISFConsolidatorContact_id || 0) || null
    this.form.vendor_carrier_ref = this.ticket.vendor_carrier_ref
    this.form.deliverySelection = parseInt(this.ticket.deliverySelection || 0) || null
    this.form.deliveryAddress = this.ticket.deliveryAddress
    this.form.requiresDelivery = this.ticket.requiresDelivery
    this.form.requiresLiftGate = this.ticket.requiresLiftGate
    this.form.haveLoadingDock = this.ticket.haveLoadingDock
    this.form.SBentryNum = this.ticket.SBentryNum
  },
  methods: {
    ...mapActions('freight-forwarder', { getContactsOfForForwarder: 'getContactsOfForForwarder' }),
    ...mapActions('ticket', { addCarrierDetails: 'addCarrierDetails' }),
    transportChangeHandler() {
      // eslint-disable-next-line eqeqeq
      if (this.form.transport != TICKET_TRANSPORT_TRUCK) {
        this.form.isffiled = false
      }
    },
    async submit() {
      try {
        const res = await this.addCarrierDetails({ id: this.ticket.id, data: this.form })
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
        apiResponseHandler(this, err, {}, this.$refs.clientDetailsForm)
      }
    },
  },
}
</script>
