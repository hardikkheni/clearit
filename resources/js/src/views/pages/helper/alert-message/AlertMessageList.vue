<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">Alert Messages</span>
            <router-link :to="{ name: 'create-helper-alert-message' }" class="d-inline-block btn btn-outline-success">
              <feather-icon icon="PlusIcon" />
              Add
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
      <data-table ref="agentTable" :filter="search" :extra="extra" :provider="dataTable" :columns="columns" @row-clicked="rowClicked">
        <template #cell(createdBy)="{ item }">{{ `${item.creator.firstname} ${item.creator.lastname}` }} </template>
        <template #cell(createdOn)="{ item }">{{ item.createdOn | formatDate }} </template>

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
import dayjs from 'dayjs'

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
    formatDate(value) {
      return dayjs(value).format('YYYY-MM-DD HH:mm:ss')
    },
  },
  data() {
    return {
      statusOptions: [
        { text: 'Active Alert Messages Only', value: true },
        { text: 'All Alert Messages', value: null },
      ],
      columns: [
        { key: 'subject', label: 'SUBJECT', sortable: true },
        { key: 'createdBy', label: 'Created By', sortable: false },
        { key: 'createdOn', label: 'Created On', sortable: true },
        { key: 'isActive', label: 'Active', sortable: true },
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
    ...mapActions('alert-message', { dataTable: 'dataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
      this.status = true
    },
    rowClicked(data) {
      this.$router.push({ name: 'edit-helper-alert-message', params: { id: data.id } })
    },
  },
}
</script>
