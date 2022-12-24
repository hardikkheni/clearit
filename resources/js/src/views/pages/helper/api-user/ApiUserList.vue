<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" class="mb-1">
            <b-form-group label="Link to API documentation:">
              <b-form-input value="https://link-to-documentation" />
            </b-form-group>
            <b-button variant="success"> Save Changes </b-button>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">API Users</span>
            <router-link :to="{ name: 'create-helper-api-user' }" class="d-inline-block btn btn-outline-success">
              <feather-icon icon="PlusIcon" />
              Add User
            </router-link>
          </b-col>
          <b-col class="text-md-right" sm="12" md="6" align-self="end">
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
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <data-table ref="agentTable" :filter="search" :extra="extra" :provider="dataTable" :columns="columns" @row-clicked="rowClicked">
        <template #cell(firstname)="{ item }">{{ `${item.firstname} ${item.lastname}` }} </template>

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
        { text: 'Active Users Only', value: true },
        { text: 'All Users', value: null },
      ],
      columns: [
        { key: 'company', label: 'COMPANY', sortable: true },
        { key: 'firstname', label: 'NAME', sortable: true },
        { key: 'email', label: 'EMAIL', sortable: true },
        { key: 'token', label: 'TOKEN', sortable: true },
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
    ...mapActions('api-user', { dataTable: 'dataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
      this.status = true
    },
    rowClicked(data) {
      this.$router.push({ name: 'edit-helper-api-user', params: { id: data.id } })
    },
  },
}
</script>
