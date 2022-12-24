<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">Daily Reports</span>
          </b-col>
        </b-row>
        <b-row class="mt-2">
          <b-col md="12">
            <b-form-select v-model="report" class="col-md-4" :options="filters" />
            <b-button variant="success" @click.prevent="paramChanges({ report: report })">
              Go
            </b-button>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container v-if="this.$route.params.report" fluid>
      <daily-report-table :page="page" :items="list" :report="this.$route.params.report" />
      <b-row>
        <b-col align-self="start">
          {{ pageInfo }}
        </b-col>
        <b-col v-if="count > limit" align-self="end">
          <b-pagination v-model="page" align="right" :total-rows="count" :per-page="limit" @change="paramChanges({ report: report }, { page: $event })" />
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>
<script>
/* eslint-disable radix */
import {
  BCard,
  BFormSelect,
  BContainer,
  BButton,
  BRow,
  BCol,
  BPagination,
} from 'bootstrap-vue'

import { mapActions, mapState } from 'vuex'
import DailyReportTable from '@/views/pages/helper/daily-reports/DailyReportTable.vue'

export default {
  components: {
    BCard,
    BFormSelect,
    BContainer,
    BButton,
    BRow,
    BCol,
    BPagination,
    DailyReportTable,
  },
  data() {
    const r = this.$route.params
    return {
      report: r.report || '',
      page: parseInt(this.$route.query.page || 1),
      filters: [
        { value: '', text: 'Select a report' },
        { value: 'isverified', text: 'ISF Report - ISF Attached, Customer Verified, Not Filed' },
        { value: 'notverified', text: 'ISF Report - ISF Attached, Customer NOT Verified, Not Filed' },
        { value: 'iskeyed', text: 'Arrival Notice attached, entry number IS keyed in' },
        { value: 'notkeyed', text: 'Arrival Notice attached, entry number is NOT keyed in' },
        { value: 'prekey_billing', text: 'Prekey and Billing' },
        { value: 'release_team_tickets', text: 'Release Team Tickets' },
        { value: 'isf_ticket', text: 'Tickets with newly uploaded ISF Documents' },
      ],
    }
  },
  watch: {
    $route() {
      const r = this.$route
      const q = r.query

      this.page = parseInt(q.page || 1)
      this.$nextTick(() => {
        this.reload()
      })
    },
  },
  async mounted() {
    await this.reload()
  },
  computed: {
    ...mapState('daily-reports', { list: 'list', count: 'count', limit: 'limit' }),
    pageInfo() {
      const currentPageCount = this.page * this.limit < this.count ? this.page * this.limit : this.count
      return `showing ${(this.page - 1) * this.limit + 1} to ${currentPageCount} of ${this.count} entries`
    },
  },
  methods: {
    ...mapActions('daily-reports', { loadDailyReport: 'loadDailyReport' }),
    async reload() {
      if (this.report) {
        const route = this.$route
        await this.loadDailyReport({report: this.report, page: route.query.page})
      }
    },
    paramChanges(params = {}, query = {}) {
      const newR = { ...this.$route.params }
      if (!newR.report) {
        newR.report = ''
      }
      if (newR.report === params.report) {
        if (query.page) {
          this.$router.push({
            name: 'helper-daily-reports',
            params: {
              ...params,
            },
            query: {
              ...query,
            },
          })
        }
      } else if (!params.report) {
        this.$router.push({
          name: 'helper-daily-reports',
        })
      } else {
        this.$router.push({
          name: 'helper-daily-reports',
          params: {
            ...params,
          },
        })
      }
    },
  },
}
</script>
