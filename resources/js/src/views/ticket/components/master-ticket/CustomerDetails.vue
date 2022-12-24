<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Customer details">
        <template #header>
          <span class="lead collapse-title">Customer details</span>
        </template>
        <validation-observer ref="affiliateReferanceForm" #default="{ invalid }">
          <b-form class="mt-1" @submit.prevent="submit">
            <b-row>
              <b-col md="4">
                <validation-provider v-if="!ticketAffiliate.id" #default="{ errors }" name="Affiliate" vid="affiliateId" rules="required">
                  <b-form-select v-model="form.affiliateId" :options="affiliateOptions">
                    <template #first>
                      <b-form-select-option :value="null">Select an affiliate</b-form-select-option>
                    </template>
                  </b-form-select>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
                <span v-else class="h5 ml-3">
                  <b-button size="sm" variant="primary" disabled>{{ ticketAffiliate.companyname }}</b-button>
                </span>
              </b-col>
              <b-col md="5">
                <b-form-group label="Reference #" label-cols-md="4">
                  <validation-provider #default="{ errors }" name="Referance Number" vid="affiliateReferenceNumber" rules="required">
                    <b-form-input v-model="form.affiliateReferenceNumber" />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-col>
              <b-col md="3">
                <b-button type="submit" variant="primary" class="btn-sm-block" :disabled="invalid">Save</b-button>
                <b-button
                  v-if="ticketAffiliate.id"
                  variant="danger"
                  class="btn-sm-block my-1 my-md-0 ml-md-1"
                  @click.prevent="removeTicketAffiliate()"
                >
                  Remove
                </b-button>
              </b-col>
            </b-row>
            <b-form-checkbox v-model="form.disableChatEmail">Disable chat emails</b-form-checkbox>
          </b-form>
        </validation-observer>
        <hr />
        <b-list-group class="mt-1">
          <b-list-group-item v-for="(col, i) of info" :key="i">
            <div class="clearfix">
              <div class="float-left">
                <span>{{ col.key }}: </span>
              </div>
              <div class="float-right">
                <span>{{ col.value }}</span>
              </div>
            </div>
          </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import {
  BButton,
  BListGroup,
  BListGroupItem,
  BFormCheckbox,
  BRow,
  BCol,
  BFormSelect,
  BForm,
  BFormSelectOption,
  BFormInput,
  BFormGroup,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import dayjs from 'dayjs'
import { mapActions } from 'vuex'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import { formatPhone, formatBoolean } from '@/utils/filters'
import { getAccountTypeLabel, userStatuses, getCorporateType, getCorporateRole, USER_STATUS_PERSONAL } from '@/utils/user'
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
    BFormSelect,
    BFormSelectOption,
    BFormInput,
    BRow,
    BCol,
    BForm,
    BFormGroup,
    ValidationProvider,
    ValidationObserver,
  },
  filters: {},
  props: {
    affiliates: {
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
    ticketAffiliate: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
      form: {
        affiliateId: null,
        affiliateReferenceNumber: null,
        disableChatEmail: false,
      },
    }
  },
  computed: {
    info() {
      const hasStatus = userStatuses.includes(this.client.status)
      // eslint-disable-next-line eqeqeq
      const isPersonalStatus = +this.client.status == USER_STATUS_PERSONAL
      const info = [
        {
          key: 'Account Type',
          value: getAccountTypeLabel(this.client.status),
        },
        {
          key: 'First Name',
          value: this.client.firstname,
        },
        {
          key: 'Last Name',
          value: this.client.lastname,
        },
        ...(this.client.corporateType && hasStatus
          ? [{ key: 'Business Structure', value: getCorporateType(this.client.corporateType) }]
          : []),
        ...(this.client.corporate && hasStatus ? [{ key: 'Business Role', value: getCorporateRole(this.client.corporate) }] : []),
        ...(this.client.busname ? [{ key: 'Legal Business Name', value: this.client.busname }] : []),
        ...(this.client.tradename ? [{ key: 'Trade Name', value: this.client.tradename }] : []),
        { key: 'Address', value: this.client.address },
        { key: 'City', value: this.client.city },
        { key: 'Province', value: this.client.state },
        { key: 'Postal/ZIP code', value: this.client.zip },
        { key: 'Telephone', value: formatPhone(this.client.phone) },
        { key: 'Fax', value: formatPhone(this.client.fax) },
        { key: 'Email', value: this.client.email },
        ...(isPersonalStatus ? [{ key: 'Date of Birth', value: dayjs(this.client.birthDate).format(DATE_FORMAT) }] : []),
        { key: 'Verified customer', value: formatBoolean(this.client.isVerified) },
        { key: 'Referred customer', value: !this.client.companyname ? formatBoolean(this.client.isReference) : this.client.companyname },
        ...(isPersonalStatus ? [{ key: 'Tax ID #', value: this.client.taxId }] : []),
        { key: 'Importer Assigned Number', value: this.client.iannumber },
      ]
      return info
    },
    affiliateOptions() {
      return this.affiliates.map(i => ({ ...i, value: i.id, text: i.companyname }))
    },
  },
  mounted() {
    this.form.affiliateId = this.ticketAffiliate.id
    this.form.affiliateReferenceNumber = this.ticket.affiliateReferenceNumber
    this.$refs.affiliateReferanceForm.reset()
  },
  methods: {
    ...mapActions('ticket', { removeAffiliateReferance: 'removeAffiliateReferance', addAffiliateReferance: 'addAffiliateReferance' }),
    removeTicketAffiliate() {
      this.$swal({
        title: 'Are you sure?',
        text: 'You want to remove this!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        customClass: {
          confirmButton: 'btn btn-outline-danger',
          cancelButton: 'btn btn-primary ml-1',
        },
        buttonsStyling: false,
      }).then(async result => {
        if (result.isConfirmed) {
          try {
            const res = await this.removeAffiliateReferance(this.ticket.id)
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
            this.$emit('removed')
          } catch (err) {
            apiResponseHandler(this, err)
          }
        }
      })
    },
    async submit() {
      try {
        const res = await this.addAffiliateReferance({ id: this.ticket.id, data: this.form })
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
        this.$emit('removed')
      } catch (err) {
        apiResponseHandler(this, err, this.$refs.affiliateReferanceForm)
      }
    },
  },
}
</script>

<style lang="scss" scoped>
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
