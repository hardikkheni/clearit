<template>
  <b-container fluid>
    <b-overlay variant="white" :show="loading" spinner-variant="primary" blur="0" opacity=".5" rounded="sm">
      <b-row>
        <b-col cols="1">
          <b-form-group :style="{ width: '100px' }">
            <b-form-select v-model="length" size="sm" :options="lengthOptions" />
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-table
            ref="table"
            :per-page="length"
            :current-page="page"
            striped
            hover
            responsive
            :filter="filter"
            :fields="columns"
            :items="draw"
            :sort-desc.sync="sort.order"
            :sort-by.sync="sort.column"
            show-empty
            :busy.sync="loading"
            @row-clicked="rowClicked"
          >
            <template v-for="(index, name) in $scopedSlots" v-slot:[name]="data">
              <slot :name="name" v-bind="data" />
            </template>
          </b-table>
        </b-col>
        <b-col align-self="start">
          <span class="text-nowrap">{{ pageInfo }}</span>
        </b-col>
        <b-col v-if="filtered && filtered > length" align-self="end">
          <b-pagination v-model="page" :total-rows="filtered" :per-page="length" align="right" class="my-0">
            <template #first-text>
              <feather-icon icon="ChevronsLeftIcon" size="16" />
            </template>
            <template #prev-text>
              <feather-icon icon="ChevronLeftIcon" size="16" />
            </template>
            <template #next-text>
              <feather-icon icon="ChevronRightIcon" size="16" />
            </template>
            <template #last-text>
              <feather-icon icon="ChevronsRightIcon" size="16" />
            </template>
          </b-pagination>
        </b-col>
      </b-row>
    </b-overlay>
  </b-container>
</template>

<script>
import { BTable, BRow, BCol, BPagination, BFormSelect, BFormGroup, BContainer, BOverlay } from 'bootstrap-vue'

export default {
  name: 'DataTable',
  components: {
    BTable,
    BRow,
    BCol,
    BPagination,
    BFormSelect,
    BFormGroup,
    BContainer,
    BOverlay,
  },
  props: {
    columns: {
      type: Array,
      required: true,
    },
    provider: {
      type: Function,
      default: () => () => {},
    },
    search: {
      type: [String, Array, Object],
      default: () => '',
    },
    filter: {
      type: String,
      default: null,
    },
    extra: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      total: 0,
      filtered: 0,
      loading: false,
      page: 1,
      length: 10,
      sort: {},
      lengthOptions: [10, 20, 30, 50].map(e => ({ value: e, text: e })),
    }
  },
  computed: {
    pageInfo() {
      const currentPageCount = this.page * this.length < this.filtered ? this.page * this.length : this.filtered
      return `showing ${(this.page - 1) * this.length + 1} to ${currentPageCount} of ${this.filtered} etries`
    },
  },
  watch: {
    extra() {
      this.refresh()
    },
  },
  methods: {
    // eslint-disable-next-line consistent-return
    async draw(ctx) {
      const { filter: search, sortBy: column, sortDesc: order, currentPage: page, perPage: length } = ctx
      const { extra } = this
      try {
        this.loading = true
        const { rows, total, filtered } = await this.provider({
          page,
          length,
          search,
          sort: { order: order ? 'desc' : 'asc', column },
          extra,
        })
        this.loading = false
        this.total = total
        this.filtered = filtered
        return rows
      } catch (err) {
        this.loading = false
        if (!err.cancelled) {
          this.total = 0
          this.filtered = 0
          console.error(err)
          return []
        }
      }
    },
    refresh() {
      if (!this.page) {
        this.page = 1
      } else {
        this.$refs.table.refresh()
      }
    },
    rowClicked(...params) {
      this.$emit('row-clicked', ...params)
    },
  },
}
</script>
