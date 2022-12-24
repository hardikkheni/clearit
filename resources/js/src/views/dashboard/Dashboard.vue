<template>
  <b-card>
    <b-container fluid>
      <b-skeleton-wrapper :loading="loading">
        <template #loading>
          <b-row>
            <b-col>
              <b-skeleton height="50px" />
            </b-col>
          </b-row>
          <b-row>
            <b-col col md="3">
              <b-skeleton type="input" />
            </b-col>
            <b-col col md="3">
              <b-skeleton type="input" />
            </b-col>
            <b-col col md="3">
              <b-skeleton type="input" />
            </b-col>
            <b-col col md="3">
              <b-skeleton type="input" />
            </b-col>
          </b-row>
          <b-row v-if="form.role == 'master'" class="mt-1">
            <b-col>
              <b-skeleton height="100px" />
            </b-col>
          </b-row>
          <b-row class="mt-1">
            <b-col>
              <b-skeleton-table :rows="10" :columns="12" />
            </b-col>
          </b-row>
        </template>
        <b-container fluid>
          <b-row>
            <b-col>
              <label class="h4">Filter to see:</label>
              <b-form-radio-group
                v-model="form.type"
                class="ml-1"
                buttons
                button-variant="flat-primary"
                @change="paramChanges({ type: $event, ...(form.type == 'clearance' ? { transport: 0 } : {}) })"
              >
                <b-form-radio v-for="(option, i) in ticketTypes" :key="i" :value="option.value">
                  {{ option.label }}
                </b-form-radio>
              </b-form-radio-group>
              <b-dropdown v-if="form.type == 'clearance'" :text="transportLabel" right variant="outline-primary">
                <b-dropdown-item v-for="(trans, i) in transports" :key="i" @click="paramChanges({ transport: trans.value })">
                  {{ trans.label }}
                </b-dropdown-item>
              </b-dropdown>
            </b-col>
            <b-col col md="2" sm="12">
              <b-form-select
                :value="form.agent"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="dashAgents"
                @change="paramChanges({ agent: $event })"
              >
                <template #first>
                  <b-form-select-option :value="0">All Agents</b-form-select-option>
                </template>
              </b-form-select>
            </b-col>
          </b-row>

          <b-row class="mt-1">
            <b-col>
              <b-form inline>
                <label class="h5 mr-1">Viewing: </label>
                <b-form-select
                  v-model="form.role"
                  class="mb-2 mr-1 mb-sm-0 col-md-2"
                  :options="dashRoles"
                  @change="paramChanges({ role: $event })"
                >
                  <template #first>
                    <b-form-select-option value="master" selected>Master Index</b-form-select-option>
                  </template>
                </b-form-select>
                <label class="h5 mr-1">Agent Status: </label>
                <b-form-select
                  v-model="form.agent_status"
                  class="mb-2 mr-1 mb-sm-0 col-md-2"
                  :options="dashAgentStatus"
                  @change="paramChanges({ agent_status: $event })"
                >
                  <template #first>
                    <b-form-select-option :value="0" selected>All Statuses</b-form-select-option>
                  </template>
                </b-form-select>
                <label class="h5 mr-1">Affiliate: </label>
                <b-form-select
                  v-model="form.affiliateId"
                  class="mb-2 mr-1 mb-sm-0 col-md-2"
                  :options="dashAffiliates"
                  @change="paramChanges({ affiliateId: $event })"
                >
                  <template #first>
                    <b-form-select-option :value="0" selected>All Affiliates</b-form-select-option>
                  </template>
                </b-form-select>
                <b-form-input v-model="form.search" class="mb-2 mr-sm-2 mb-sm-0" placeholder="Search" />
                <b-button class="mb-2 mr-1 mb-sm-0" variant="success" @click.prevent="paramChanges({ search: form.search })">
                  Serach
                </b-button>
                <b-button class="mb-2 mr-1 mb-sm-0" variant="outline-success" @click.prevent="paramChanges({ search: null })">
                  Reset
                </b-button>
              </b-form>
            </b-col>
          </b-row>
          <b-row class="mt-1">
            <b-col>
              <b-form-radio-group
                v-model="form.status"
                class="ml-1 d-inline-flex flex-wrap"
                buttons
                button-variant="flat-primary"
                @change="paramChanges({ status: $event })"
              >
                <b-form-radio v-for="(option, i) in dashTicketStatusList" :key="i" class="col-md-2 text-left" :value="option.value">
                  <span class="b-avatar b-avatar-sm rounded-circle" :style="{ background: `#${option.hexcolor || DEFAULT_HEX_COLOR}` }">
                    <span class="b-avatar-text">&nbsp;</span>
                  </span>
                  <span class="tickit-status-label">
                    {{ option.text }}
                  </span>
                </b-form-radio>
              </b-form-radio-group>
            </b-col>
          </b-row>
          <b-row class="mt-3">
            <b-col cols="12">
              <component :is="dashTable" :items="list" :role="form.role" @row-clicked="rowClicked" />
            </b-col>
            <b-col align-self="start"> {{ pageInfo }} </b-col>
            <b-col v-if="count > limit" align-self="end">
              <b-pagination v-model="page" align="right" :total-rows="count" @change="paramChanges({}, { page: $event })" />
            </b-col>
          </b-row>
        </b-container>
      </b-skeleton-wrapper>
    </b-container>
  </b-card>
</template>

<script>
/* eslint-disable radix */
import { mapActions, mapGetters, mapState } from 'vuex'
import {
  BCard,
  BFormSelect,
  BContainer,
  BForm,
  BFormInput,
  BButton,
  BSkeletonWrapper,
  BSkeleton,
  BRow,
  BCol,
  BTable,
  BSkeletonTable,
  BFormSelectOption,
  BFormRadio,
  BFormRadioGroup,
  BDropdown,
  BDropdownItem,
  BAvatar,
  BPagination,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

import MasterTable from '@/views/dashboard/tables/MasterTable.vue'
import CommonTable from '@/views/dashboard/tables/CommonTable.vue'

import { DEFAULT_HEX_COLOR } from '@/utils/ticket'

export default {
  components: {
    BCard,
    BFormSelect,
    BForm,
    BContainer,
    BFormInput,
    BButton,
    BSkeletonWrapper,
    BSkeleton,
    BRow,
    BCol,
    BTable,
    BSkeletonTable,
    BFormSelectOption,
    MasterTable,
    CommonTable,
    BFormRadio,
    BFormRadioGroup,
    BDropdown,
    BDropdownItem,
    BAvatar,
    BPagination,
  },
  directives: {
    Ripple,
  },
  data() {
    const r = this.$route.params
    return {
      form: {
        role: r.role || 'docreview',
        type: r.type || 'all',
        transport: parseInt(r.transport || 0),
        agent: parseInt(r.agent || 0),
        status: r.status || null,
        search: r.search || null,
        agent_status: parseInt(r.agent_status || 0),
        affiliateId: parseInt(r.affiliateId || 0),
      },
      page: parseInt(this.$route.query.page || 1),
      ticketTypes: [
        { value: 'all', label: 'All' },
        { value: 'clearance', label: 'Clearance' },
        { value: 'custom', label: 'Consulting' },
        { value: 'freight', label: 'Freight' },
        { value: 'car', label: 'Car' },
      ],
      transports: [
        { value: 0, label: 'All Clearance' },
        { value: 1, label: 'Truck' },
        { value: 2, label: 'Ocean' },
        { value: 3, label: 'Air' },
        { value: 4, label: 'Courier' },
      ],
      DEFAULT_HEX_COLOR,
    }
  },

  computed: {
    ...mapState('auth', { list: 'list', loading: 'loading', count: 'count', limit: 'limit' }),
    ...mapGetters('auth', {
      dashRoles: 'dashRoles',
      dashAgentStatus: 'dashAgentStatus',
      dashAffiliates: 'dashAffiliates',
      dashAgents: 'dashAgents',
      dashTicketStatusList: 'dashTicketStatusList',
    }),
    transportLabel() {
      return this.transports.find(e => e.value === this.form.transport)?.label
    },
    pageInfo() {
      const currentPageCount = this.page * this.limit < this.count ? this.page * this.limit : this.count
      return `showing ${(this.page - 1) * this.limit + 1} to ${currentPageCount} of ${this.count} etries`
    },
    dashTable() {
      return `${this.form.role === 'master' ? 'master' : 'common'}-table`
    },
  },
  watch: {
    $route() {
      const r = this.$route
      const p = r.params
      const q = r.query
      this.form = {
        role: p.role || 'master',
        type: p.type || 'all',
        transport: parseInt(p.transport || 0),
        agent: parseInt(p.agent || 0),
        status: p.status || null,
        search: p.search || null,
        agent_status: parseInt(p.agent_status || 0),
        affiliateId: parseInt(p.affiliateId || 0),
      }

      this.page = parseInt(q.page || 1)

      // reload dashboard
      this.$nextTick(() => {
        this.reload()
      })
    },
  },
  async mounted() {
    await this.reload()
  },
  methods: {
    ...mapActions('auth', { getDashboard: 'dashboard' }),
    async reload() {
      const route = this.$route
      await this.getDashboard({ ...route.params, page: route.query.page })
    },
    paramChanges(params = {}, query = {}) {
      const newR = { ...this.$route.params }
      if (!newR.role) {
        newR.role = 'master'
      }
      if (!newR.type) {
        newR.type = 'all'
      }
      if (!newR.status) {
        newR.status = 'all'
      }
      if (!newR.from) {
        newR.from = 'from:'
      }
      if (!newR.to) {
        newR.to = 'to:'
      }
      if (!newR.agent) {
        newR.agent = 0
      }
      if (!newR.transport) {
        newR.transport = 0
      }
      if (!newR.agent_status) {
        newR.agent_status = 0
      }
      if (!newR.affiliateId) {
        newR.affiliateId = 0
      }
      if (!newR.search) {
        newR.search = null
      }
      this.$router.push({
        name: 'dashboard',
        params: {
          ...newR,
          ...params,
        },
        query: {
          ...query,
        },
      })
    },
    rowClicked(row) {
      this.$router.push({
        name: 'view-ticket',
        params: {
          role: this.form.role,
          guid: row.guid,
        },
      })
    },
  },
}
</script>
<style lang="scss" scoped>
.tickit-status-label {
  width: 80%;
  display: inline-block;
  vertical-align: middle;
}
</style>
