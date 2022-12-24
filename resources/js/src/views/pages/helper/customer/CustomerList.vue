<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">Customers</span>
            <router-link :to="{ name: 'create-helper-affiliate' }" class="btn btn-outline-success">
              Export table
            </router-link>
          </b-col>
          <b-col class="text-md-right" sm="12" md="8" align-self="end">
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
      <data-table ref="customerTable" :filter="search" :provider="dataTable" :columns="columns">
        <template #cell(id)="{ item }"><strong>{{ item.id}}</strong></template>
        <template #cell(name)="{ item }">{{ getCustomerName(item) }}</template>
        <template #cell(city_state)="{ item }">{{ `${item.city}, ${item.state}` }} </template>
        <template #cell(phone)="{ item }">{{ item.phone | phone }} </template>
        <template #cell(account)="{ item }">
            <blank-avatar
                style="margin-right: 5px"
                :src="require(`@/assets/images/icons/${item.isVerified ? 'ico-verified' : 'ico-verified-un'}.png`)"
            />
            <blank-avatar
                style="margin-right: 5px"
                :src="require(`@/assets/images/icons/${item.isCertificate ? 'ico-certificate' : 'ico-certificate-un'}.png`)"
            />
            <blank-avatar
                style="margin-right: 5px"
                :src="require(`@/assets/images/icons/${item.ispga>0 ? 'ico-pga' : 'ico-pga-un'}.png`)"
            />
            <blank-avatar
                style="margin-right: 5px"
                :src="require(`@/assets/images/icons/${item.iscc>0 ? 'ico-cc' : 'ico-cc-un'}.png`)"
            />
        </template>
        <template #cell(CreatedOn)="{ item }">
          <span>
            {{ item.CreatedAt | dateFormat(DATE_FORMAT_SHORT_YEAR) }} <small> [{{ item.CreatedAt | dateFormat(HOUR_FORMAT) }}] </small>
          </span>
        </template>
      </data-table>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BRow, BCol, BFormGroup, BContainer, BButton, BFormInput } from 'bootstrap-vue'
import { mapActions } from 'vuex'
import dayjs from "dayjs"

import BlankAvatar from '@/components/BlankAvatar.vue'
import DataTable from '@/components/datatable/DataTable.vue'
import { DATE_FORMAT_SHORT_YEAR, HOUR_FORMAT } from '@/utils/config'

import { getCustomerName } from '@/utils/customer'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BButton,
    BFormGroup,
    BFormInput,
    BlankAvatar,
    DataTable,
  },
  filters: {
    phone(value) {
      if (value) {
        return `${value?.substr(0, 3)}-${value?.substr(3, 3)}-${value?.substr(-4)}`
      }
      return value
    },
    dateFormat(value, format) {
      return value && dayjs(value).format(format)
    },
  },
  data() {
    return {
      columns: [
        { key: 'id', label: 'CLIENT ID', sortable: true },
        { key: 'name', label: 'NAME', sortable: false },
        { key: 'contactname', label: 'CONTACT', sortable: true },
        { key: 'email', label: 'EMAIL', sortable: true },
        { key: 'city_state', label: 'CITY/STATE', sortable: true },
        { key: 'phone', label: 'PHONE', sortable: true },
        { key: 'CreatedOn', label: 'REGISTERED', sortable: true },
        { key: 'account', label: 'ACCOUNT', sortable: true },
      ],
      search: null,
      term: null,
      DATE_FORMAT_SHORT_YEAR,
      HOUR_FORMAT,
    }
  },
  methods: {
    ...mapActions('customer', { dataTable: 'dataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
      this.status = true
    },
    getCustomerName,
  },
}
</script>
