<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">Unverified Users</span>
          </b-col>
          <b-col class="text-md-right" sm="12" md="8" align-self="end">
            <b-form-group class="d-inline-block align-middle mr-1">
              <b-form-select v-model="affiliateId" :options="allAffiliateOptions" @change="paramChanges({ affiliateId: $event })">
                <template #first>
                  <b-form-select-option :value="0">All Affiliates</b-form-select-option>
                </template>
              </b-form-select>
            </b-form-group>

            <b-form-group class="d-inline-block align-middle mr-1">
              <b-form-select v-model="all" :options="varifiedOptions" @change="paramChanges({ all: $event })" />
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
      <data-table ref="agentTable" :filter="search" :extra="extra" :provider="dataTable" :columns="columns" @row-clicked="rowClicked">
        <template #cell(name)="{ item }">
          {{ item.name }}
          <img v-if="item.is_freightos_customer == 1" :src="freightImg" height="50" />
        </template>
        <template #cell(account_type)="{ item }">
          {{ item.status | accountType }}
        </template>
        <template #cell(annual_bond)="{ item }">
          <template v-if="item.bondType == 2 && item.isBondRequested == 1">
            <template v-if="item.bondrequestcompleted"> Requested </template>
          </template>
          <template v-else-if="item.bondType == 2 && item.needBondVerify == 1"> Unverified </template>
          <template v-else-if="item.bondType == 2">
            <feather-icon class="text-success" icon="CheckIcon" size="18" />
          </template>
        </template>
        <template #cell(edit)="{ item }">
          <b-button variant="blue">POA</b-button>
          <b-button variant="success">Bond</b-button>
          <b-button variant="danger">Delete</b-button>
        </template>
      </data-table>
    </b-container>
  </b-card>
</template>

<script>
/* eslint-disable radix */
import { BCard, BRow, BCol, BFormGroup, BFormSelect, BFormSelectOption, BContainer, BButton, BFormInput } from 'bootstrap-vue'
import { mapActions, mapGetters } from 'vuex'

import DataTable from '@/components/datatable/DataTable.vue'
import { USER_STATUS_PERSONAL, USER_STATUS_COMMERCAIL, USER_STATUS_NON_RESIDENT } from '@/utils/user'

import freightImg from '@/assets/images/icons/freightos.png'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BButton,
    BFormGroup,
    BFormInput,
    DataTable,
    BFormSelect,
    BFormSelectOption,
  },
  filters: {
    accountType(value) {
      switch (+value) {
        case USER_STATUS_PERSONAL:
          return 'Personal'
        case USER_STATUS_COMMERCAIL:
          return 'Business'
        case USER_STATUS_NON_RESIDENT:
          return 'Non-Resident'
        default:
          return value
      }
    },
  },
  data() {
    const { params } = this.$route
    return {
      freightImg,
      varifiedOptions: [
        { text: 'Ready for verification', value: 0 },
        { text: 'Show all user', value: 1 },
      ],
      columns: [
        { key: 'name', label: 'NAME', sortable: false },
        { key: 'email', label: 'EMAIL', sortable: false },
        { key: 'account_type', label: 'ACCOUNT TYPE', sortable: false },
        { key: 'annual_bond', label: 'ANNUAL BOND', sortable: false },
        { key: 'edit', label: 'EDIT', sortable: false },
      ],
      affiliateId: parseInt(params.affiliateId || 0),
      all: parseInt(params.all || 0),
      extra: { affiliateId: 0, all: 0 },
      term: null,
      search: null,
    }
  },
  computed: {
    ...mapGetters('affiliate', { allAffiliateOptions: 'allAffiliates' }),
  },
  watch: {
    affiliateId() {
      this.extra = { ...this.extra, affiliateId: this.affiliateId }
    },
    all() {
      this.extra = { ...this.extra, all: this.all }
    },
    $route() {
      const { params } = this.$route
      this.affiliateId = parseInt(params.affiliateId || 0)
      this.all = parseInt(params.all || 0)
    },
  },
  async mounted() {
    await this.getAllAffiliates()
  },
  methods: {
    ...mapActions('affiliate', { getAllAffiliates: 'all' }),
    ...mapActions('customer', { dataTable: 'unverifiedWithPoaDataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
      this.affiliateId = 0
      this.all = 0
    },
    paramChanges(params = {}) {
      const newR = { ...this.$route.params }
      if (!newR.affiliateId) {
        newR.affiliateId = 0
      }
      if (!newR.all) {
        newR.all = 0
      }
      this.$router.push({
        name: this.$route.name,
        params: {
          ...newR,
          ...params,
        },
      })
    },
    rowClicked(data) {
      // this.$router.push({ name: 'edit-helper-agent', params: { id: data.id } })
    },
  },
}
</script>
