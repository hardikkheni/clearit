<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">FreightOS Billing</span>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row class="mt-1">
        <b-col>
          <b-form inline @submit.prevent="submit">
            <label class="h5 mr-1">Viewing: </label>
            <b-form-select v-model="form.isBilled" class="mb-2 mr-1 mb-sm-0 col-md-2" :options="isBilledOptions" />
            <template v-if="form.isBilled === 1">
              <label class="h5 mr-1">From: </label>
              <flat-pickr v-model="form.from" class="mb-2 mr-1 mb-sm-0 col-md-2 form-control" :config="dateConfig" />
              <label class="h5 mr-1">To: </label>
              <flat-pickr v-model="form.to" class="mb-2 mr-1 mb-sm-0 col-md-2 form-control" :config="dateConfig" />
            </template>
            <b-button type="submit" class="mb-2 mr-1 mb-sm-0" variant="success"> Filter </b-button>
          </b-form>
        </b-col>
      </b-row>
      <b-row class="mt-2">
        <b-col>
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton-table :rows="5" :columns="9" />
            </template>
            <b-table-simple small outlined hover responsive>
              <b-thead>
                <b-th>Ticket #</b-th>
                <b-th>Customer</b-th>
                <b-th>FreightOS #</b-th>
                <b-th>SB #</b-th>
                <b-th>Entry #</b-th>
                <b-th>Amount</b-th>
                <b-th>Received</b-th>
                <b-th>Data Sent</b-th>
                <b-th>Invoice Emailed</b-th>
              </b-thead>
              <b-tbody>
                <template v-if="!!(data || []).length">
                  <b-tr v-for="(row, i) of freightosBilling.rows" :key="i">
                    <b-td>{{ row.ticketid }}</b-td>
                    <b-td>{{ row.customer }}</b-td>
                    <b-td>{{ row.affiliateReferenceNumber }}</b-td>
                    <b-td>{{ row.sbNum }}</b-td>
                    <b-td>{{ row.entryNum }}</b-td>
                    <b-td>{{ row.invoiceAmount }}</b-td>
                    <b-td>{{ row.receivedOn }}</b-td>
                    <b-td>{{ row.dataSent }}</b-td>
                    <b-td>{{ row.invoiceSent }}</b-td>
                  </b-tr>
                  <b-tr>
                    <b-td />
                    <b-td />
                    <b-td />
                    <b-td />
                    <b-td class="text-right">Total:</b-td>
                    <b-td>{{ freightosBilling.total }}</b-td>
                    <b-td />
                    <b-td />
                    <b-td />
                  </b-tr>
                </template>
                <template v-else>
                  <b-td class="text-center" colspan="9">No data found!.</b-td>
                </template>
              </b-tbody>
            </b-table-simple>
          </b-skeleton-wrapper>
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
/* eslint-disable no-param-reassign */
import {
  BCard,
  BRow,
  BCol,
  BContainer,
  BForm,
  BFormSelect,
  BButton,
  BSkeletonWrapper,
  BSkeletonTable,
  BTableSimple,
  BThead,
  BTbody,
  BTh,
  BTr,
  BTd,
} from 'bootstrap-vue'
import FlatPickr from 'vue-flatpickr-component'
import dayjs from 'dayjs'

import { dateConfig } from '@/utils/config'
// import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BForm,
    BFormSelect,
    FlatPickr,
    BButton,
    BSkeletonWrapper,
    BSkeletonTable,
    BTableSimple,
    BThead,
    BTbody,
    BTh,
    BTr,
    BTd,
  },
  data() {
    const date = dayjs().format('YYYY-MM-DD')
    return {
      form: {
        isBilled: 0,
        from: date,
        to: date,
      },
      isBilledOptions: [
        { text: 'Ready for Billing', value: 0 },
        { text: 'Billed', value: 1 },
      ],
      loading: false,
      data: [],
      dateConfig,
    }
  },
  computed: {
    freightosBilling() {
      let total = 0
      const rows = (this.data || []).map(i => {
        i.invoiceAmount = parseFloat(i.invoiceAmount || 0)
          .toFixed(2)
          .toString()
        total += parseFloat(i.invoiceAmount)
        return {
          ticketid: i.ticketid,
          affiliateReferenceNumber: i.affiliateReferenceNumber,
          customer: i.Customer || 'N/A',
          sbNum: i.SBNum || 'N/A',
          entryNum: i.entryNum || 'N/A',
          dataSent: i.DataSent || 'N/A',
          receivedOn: i.ReceivedOn ? dayjs(i.ReceivedOn).format('MM/DD/YY hh:mm A') : 'N/A',
          invoiceSent: i.InvoiceSent ? dayjs(i.InvoiceSent).format('MM/DD/YY hh:mm A') : 'N/A',
          invoiceAmount: `$ ${i.invoiceAmount.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')}`,
        }
      })
      return {
        rows,
        total: `$ ${total
          .toFixed(2)
          .toString()
          .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')}`,
      }
    },
  },
  mounted() {
    this.submit()
  },
  methods: {
    async submit() {
      this.loading = true
      try {
        const { data } = (await this.$api.post('/helper/ticket/freightos-billing', this.form)).data
        this.loading = false
        this.data = data
      } catch (err) {
        this.loading = false
      }
    },
  },
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
</style>
