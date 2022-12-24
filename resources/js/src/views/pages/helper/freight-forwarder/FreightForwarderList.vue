<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">Freight Forwarder Management</span>
            <router-link :to="{ name: 'create-helper-freight-forwarder' }" class="d-inline-block btn btn-outline-success">
              <feather-icon icon="PlusIcon" />
              Add New
            </router-link>
          </b-col>
          <b-col class="text-md-right" sm="12" md="6" align-self="end">
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
      <data-table :filter="search" :provider="dataTable" :columns="columns" @row-clicked="rowClicked">
        <template #cell(contacts)="{ item }">
          <b-button variant="flat-success" @click.prevent="viewContacts(item.contacts)">View Contacts</b-button>
        </template>
      </data-table>
    </b-container>
    <b-modal ref="show-contacts" centered size="lg" title="Contacts" hide-footer>
      <b-table show-empty :fields="contactFields" empty-text="No Conatct Found!." :items="contacts" />
    </b-modal>
  </b-card>
</template>

<script>
import { BCard, BRow, BCol, BFormGroup, BContainer, BButton, BFormInput, BModal, BTable } from 'bootstrap-vue'
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
    BFormInput,
    DataTable,
    BModal,
    BTable,
  },
  data() {
    return {
      columns: [
        { key: 'isfcName', label: 'CONTACT NAME', sortable: true },
        { key: 'isfcAddress1', label: 'LOCATION', sortable: true },
        { key: 'isfcCountry', label: 'COUNTRY', sortable: true },
        { key: 'isfcBusinessPhone', label: 'PHONE', sortable: true },
        { key: 'isfcZip', label: 'POSTAL CODE', sortable: true },
        { key: 'contacts', label: 'CONTACTS', sortable: false },
      ],
      contactFields: [
        { key: 'isfcName', label: 'contact name' },
        { key: 'isfcEmailAddress', label: 'email' },
      ],
      search: null,
      term: null,
      contacts: [],
    }
  },

  watch: {},
  methods: {
    ...mapActions('freight-forwarder', { dataTable: 'dataTable' }),
    triggerSearch() {
      this.search = this.term
    },
    reset() {
      this.term = null
      this.search = null
    },
    viewContacts(contacts) {
      this.contacts = contacts
      this.$nextTick(() => {
        this.$refs['show-contacts'].show()
      })
    },
    rowClicked(data) {
      this.$router.push({
        name: 'edit-helper-freight-forwarder',
        params: { id: data.id },
      })
    },
  },
}
</script>
