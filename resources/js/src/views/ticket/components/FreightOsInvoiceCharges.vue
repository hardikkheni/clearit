<template>
  <b-list-group>
    <b-list-group-item>
      <validation-observer ref="affiliateForm" #default="{ invalid }">
        <b-form inline @submit.prevent>
          <b-form-group class="col-md-5 col-sm-12">
            <validation-provider #default="{ errors }" name="Freight Item" vid="freighittem" rules="required">
              <b-form-select v-model="form.freightItemId" class="w-100" :options="freightInvoiceDocumentOptions" name="freighittem">
                <template #first>
                  <b-form-select-option :value="null">Select a FreightOS Invoice Item</b-form-select-option>
                </template>
              </b-form-select>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <b-form-group class="col-md-4">
            <validation-provider #default="{ errors }" name="Freight Amount" vid="freight_amount" rules="required|float">
              <b-input-group prepend="$">
                <b-form-input v-model="form.freight_amount" type="number" step="0.01" name="freight_amount" />
              </b-input-group>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <b-form-group class="col-md-2">
            <b-button variant="primary" type="submit" :disabled="invalid">Add</b-button>
          </b-form-group>
        </b-form>
      </validation-observer>
    </b-list-group-item>
    <b-list-group-item>
      <b-table-simple>
        <b-thead>
          <b-tr>
            <b-th>Description</b-th>
            <b-th>Amount</b-th>
            <b-th>&nbsp;</b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <template v-if="computedFreightInvoiceItems.rows.length">
            <b-tr v-for="(tr, i) of computedFreightInvoiceItems.rows" :key="i">
              <b-td>{{ tr.description }}</b-td>
              <b-td>{{ tr.amount | currencyFormat }}</b-td>
              <b-td>
                <b-button size="sm" variant="flat-danger" class="btn-icon" @click.prevent="deleteFreightInvoiceItem(tr.id)">
                  <feather-icon icon="Trash2Icon" />
                </b-button>
              </b-td>
            </b-tr>
            <b-tr>
              <b-td>total</b-td>
              <b-td>{{ computedFreightInvoiceItems.total | currencyFormat }}</b-td>
              <b-td> &nbsp; </b-td>
            </b-tr>
          </template>
          <b-tr v-else>
            <b-td class="text-center" colspan="3">No Charges Found!</b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
    </b-list-group-item>
  </b-list-group>
</template>

<script>
import {
  BButton,
  BForm,
  BFormSelect,
  BFormSelectOption,
  BFormGroup,
  BInputGroup,
  BFormInput,
  BTableSimple,
  BThead,
  BTr,
  BTbody,
  BTd,
  BTh,
  BListGroup,
  BListGroupItem,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import { required, float } from '@validations'
import { currencyFormat } from '@/utils/filters'

export default {
  components: {
    BButton,
    BForm,
    ValidationProvider,
    ValidationObserver,
    BFormSelect,
    BFormSelectOption,
    BFormGroup,
    BInputGroup,
    BFormInput,
    BTableSimple,
    BThead,
    BTr,
    BTbody,
    BTd,
    BTh,
    BListGroup,
    BListGroupItem,
  },
  filters: { currencyFormat },
  props: {
    ticketId: {
      type: [String, Number],
      required: true,
    },
    freightInvoiceDocs: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      required,
      float,
      form: {
        freightItemId: null,
        freight_amount: 0,
      },
      freightInvoiceItems: [],
      dateTime: null,
      invoiceDateTime: null,
    }
  },
  computed: {
    freightInvoiceDocumentOptions() {
      return this.freightInvoiceDocs.map(i => ({ ...i, value: i.id, text: i.description }))
    },
    computedFreightInvoiceItems() {
      return this.freightInvoiceItems.reduce(
        (accu, row) => {
          accu.rows.push(row)
          // eslint-disable-next-line no-param-reassign
          accu.total += parseFloat(row.amount)
          return accu
        },
        { rows: [], total: 0 },
      )
    },
  },
  watch: {
    // eslint-disable-next-line object-shorthand
    'form.freightItemId'() {
      this.form.freight_amount = this.freightInvoiceDocs.find(i => i.id === this.form.freightItemId)?.defaultFee
    },
  },
  async mounted() {
    await this.getFreightInvoiceItems()
    await this.getInvoiceDateTime()
    await this.getDateTime()
  },
  methods: {
    async getFreightInvoiceItems() {
      const id = this.ticketId
      try {
        const { data } = (await this.$api.get(`/ticket/${id}/freight-invoice-item-list`)).data
        this.freightInvoiceItems = data
      } catch (err) {
        this.freightInvoiceItems = []
      }
    },
    async deleteFreightInvoiceItem(id) {
      console.log('===========================================================')
      console.log(id, 'id')
      console.log('===========================================================')
    },
    async getDateTime() {
      const id = this.ticketId
      if (!id) return
      try {
        const { data } = (await this.$api.get(`/ticket/${id}/get-fc-datetime`)).data
        this.dateTime = data
      } catch (err) {
        this.dateTime = null
      }
    },
    async getInvoiceDateTime() {
      const id = this.ticketId
      if (!id) return
      try {
        const { data } = (await this.$api.get(`/ticket/${id}/get-fc-invoice-datetime`)).data
        this.invoiceDateTime = data
      } catch (err) {
        this.invoiceDateTime = null
      }
    },
  },
}
</script>
