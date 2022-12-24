<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">Affiliate Management</span>
            <router-link :to="{ name: 'create-helper-affiliate' }" class="btn btn-outline-success">
              <feather-icon icon="PlusIcon" />
              Add Affiliate
            </router-link>
          </b-col>
          <b-col class="text-md-right" sm="12" md="8" align-self="end">
            <b-form-group class="d-inline-block align-middle mr-1">
              <b-form-radio-group v-model="status">
                <b-form-radio v-for="(checkbox, i) of statusOptions" :key="i" :value="checkbox.value">
                  {{ checkbox.text }}
                </b-form-radio>
              </b-form-radio-group>
            </b-form-group>

            <b-form-group class="d-inline-block align-middle mr-1">
              <b-form-input v-model="term" name="search" placeholder="Search" />
            </b-form-group>

            <b-form-group class="d-inline-block align-middle mr-1">
              <b-button variant="success" @click.prevent="triggerSearch"> Search </b-button>
              <b-button variant="outline-success" @click.prevent="reset"> Reset </b-button>
            </b-form-group>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <data-table ref="affiliateTable" :filter="search" :extra="extra" :provider="dataTable" :columns="columns" @row-clicked="rowClicked">
        <template #cell(contact)="{ item }">{{ `${item.contactfirstname || ''} ${item.contactlastname || ''}` }} </template>
        <template #cell(phone)="{ item }">{{ item.phone | phone }} </template>
        <template #cell(isCreditAccount)="{ item }">
          <feather-icon
            :class="`text-${item.isCreditAccount ? 'success' : 'danger'}`"
            :icon="`${item.isCreditAccount ? 'Check' : 'X'}Icon`"
            size="18"
          />
        </template>
        <template #cell(isActive)="{ item }">
          <feather-icon :class="`text-${item.isActive ? 'success' : 'danger'}`" :icon="`${item.isActive ? 'Check' : 'X'}Icon`" size="18" />
        </template>
      </data-table>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BRow, BCol, BFormGroup, BFormRadioGroup, BFormRadio, BContainer, BButton, BFormInput } from 'bootstrap-vue'
import { mapActions } from 'vuex'

import DataTable from '@/components/datatable/DataTable.vue'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BButton,
    BFormGroup,
    BFormRadioGroup,
    BFormRadio,
    BFormInput,
    DataTable,
  },
  filters: {
    phone(value) {
      if (value) {
        return `${value?.substr(0, 3)}-${value?.substr(3, 3)}-${value?.substr(-4)}`
      }
      return value
    },
  },
  data() {
    return {
      statusOptions: [
        { text: 'Active Affiliates Only', value: true },
        { text: 'All Affiliate', value: null },
      ],
      columns: [
        { key: 'companyname', label: 'COMPLANY NAME', sortable: true },
        { key: 'contact', label: 'CONTACT', sortable: false },
        { key: 'phone', label: 'PHONE', sortable: true },
        { key: 'notificationEmail', label: 'NOTIFICATION EMAIL', sortable: true },
        { key: 'website', label: 'WEBSITE', sortable: true },
        { key: 'affiliateCode', label: 'AFFILIATE CODE', sortable: true },
        { key: 'isCreditAccount', label: 'CREDIT ACCOUNT', sortable: true },
        { key: 'isActive', label: 'ACTIVE', sortable: true },
        { key: 'createdByUserId', label: 'REFERRALS', sortable: true },
      ],
      status: true,
      search: null,
      term: null,
      extra: { isActive: true },
    }
  },
  watch: {
    status() {
      this.extra = { isActive: this.status }
    },
  },
  methods: {
    ...mapActions('affiliate', { dataTable: 'dataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
      this.status = true
    },
    rowClicked(data) {
      this.$router.push({ name: 'edit-helper-affiliate', params: { id: data.id } })
    },
  },
}
</script>
