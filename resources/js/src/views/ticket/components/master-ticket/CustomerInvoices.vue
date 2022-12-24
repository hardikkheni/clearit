<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Customer Invoices">
        <template #header>
          <span class="lead collapse-title">Customer Invoices</span>
          <b-button size="sm" variant="outline-success" @click.prevent="newRequest">Export to PDF</b-button>
        </template>
        <b-list-group v-for="(inv, i) of invoices" :key="i" class="mt-1">
          <b-list-group-item>
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group label="Invoice Number">
                  <b-form-input v-model="inv.invoiceNumber" />
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group label="Currency">
                  <b-form-input :value="inv.invoiceCurrency" readonly />
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group label="Date">
                  <b-form-input :value="inv.invoiceDate" readonly />
                </b-form-group>
              </b-col>
            </b-row>
            <span class="h5">Ship To:</span>
            <b-row>
              <b-col sm="12" md="6">
                <b-form-group label="Address">
                  <b-form-input v-model="inv.shipAddress" />
                </b-form-group>
              </b-col>
              <b-col sm="12" md="6">
                <b-form-group label="City">
                  <b-form-input v-model="inv.shipCity" />
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col sm="12" md="4">
                <b-form-group label="Country">
                  <b-form-select v-model="inv.shipCountry" :options="countries" />
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group label="State">
                  <b-form-select v-if="states[i].length" v-model="inv.shipstate" :options="states[i]" />
                  <b-form-input v-else v-model="inv.shipstate" />
                </b-form-group>
              </b-col>
              <b-col sm="12" md="4">
                <b-form-group label="Postal code">
                  <b-form-input v-model="inv.shipstate" />
                </b-form-group>
              </b-col>
            </b-row>
          </b-list-group-item>
          <b-list-group-item v-if="info[i].length">
            <b-list-group>
              <span>Vendor details</span>
              <b-list-group-item v-for="(row, x) of info[i]" :key="x">
                <div class="clearfix">
                  <div class="float-left">
                    <span>{{ row.key }}: </span>
                  </div>
                  <div class="float-right">
                    <span>{{ row.value }}</span>
                  </div>
                </div>
              </b-list-group-item>
            </b-list-group>
          </b-list-group-item>
          <b-list-group-item v-if="(inv.items || []).length">
            <b-list-group>
              <b-list-group-item v-for="(row, x) of inv.items || []" :key="x">
                <b-row>
                  <b-col sm="12" md="3">
                    <b-form-group label="HTS code">
                      <b-form-input v-model="row.code" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="6">
                    <b-form-group label="Item description">
                      <b-form-input v-model="row.description" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="3">
                    <b-form-group label="SKU number">
                      <b-form-input v-model="row.sku" />
                    </b-form-group>
                  </b-col>
                </b-row>
                <b-row>
                  <b-col sm="12" md="4">
                    <b-form-group label="Country of Origin">
                      <b-form-select v-model="row.country" :options="countries" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="3">
                    <b-form-group label="Quantity">
                      <b-form-input v-model="row.qty" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="3">
                    <b-form-group label="Unit price">
                      <b-form-input v-model="row.price" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="2">
                    <b-form-group label="Total">
                      <b-form-input :value="Number(row.qty * row.price).toLocaleString('en-US')" readonly />
                    </b-form-group>
                  </b-col>
                </b-row>
                <b-row>
                  <b-col sm="12" md="6">
                    <b-form-group label="Unit of measure">
                      <b-form-input v-model="row.uom" />
                    </b-form-group>
                    <b-form-checkbox> I have a NAFTA/Trade certificate for this item</b-form-checkbox>
                  </b-col>
                  <b-col sm="12" md="6">
                    <label>&nbsp;</label>
                    <br />
                    <b-button variant="primary" size="sm" class="float-right" @click.prevent="updateItem(row)">Update Item</b-button>
                  </b-col>
                </b-row>
              </b-list-group-item>
            </b-list-group>
          </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BListGroup, BListGroupItem, BRow, BCol, BFormInput, BFormGroup, BButton, BFormSelect, BFormCheckbox } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { getCountries, getStates } from '@/utils/config'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BRow,
    BCol,
    BFormInput,
    BFormGroup,
    BButton,
    BFormSelect,
    BFormCheckbox,
  },
  filters: {},
  props: {
    invoices: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      form: {
        country: null,
        state: null,
      },
    }
  },
  computed: {
    countries() {
      return getCountries()
    },
    states() {
      return this.invoices.map(i => getStates(i.shipcountry))
    },
    info() {
      return this.invoices.map(i => this.getInfo(i))
    },
  },
  mounted() {},
  methods: {
    getInfo(invoice) {
      const vendor = invoice.vendor || {}
      if (!vendor) {
        return []
      }
      return [
        { key: 'Name', value: vendor.vendorName },
        { key: 'Address', value: vendor.vendorAddress },
        { key: 'City', value: vendor.vendorCity },
        { key: 'Province', value: vendor.vendorState },
        { key: 'Telephone', value: vendor.vendorPhone },
        { key: 'Fax', value: vendor.vendorFax },
        { key: 'Email', value: vendor.vendorEmail },
      ]
    },
    newRequest(e) {
      e.stopPropagation()
    },
    updateItem(item) {
      console.log('===========================================================')
      console.log(item, 'item')
      console.log('===========================================================')
    },
  },
}
</script>
