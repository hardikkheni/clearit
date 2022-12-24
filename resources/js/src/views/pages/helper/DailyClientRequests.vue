<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">My Requests</span>
          </b-col>
        </b-row>
        <b-row v-if="list[0]" class="mt-2">
          <b-col md="12">
            <h4 class="text-success">Open customer requests created by {{ list[0].requestedBy }}</h4>
          </b-col>
          <b-col md="12">Reminders were emailed on {{ list[0].reminderEmailSentOn }} to the following customers</b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-table v-if="list[0]" responsive :fields="columns" :items="list">
        <template #cell(customer)="{ item }">
          <router-link v-if="item.clientName" :to="item.link">
            {{ item.clientName }}
          </router-link>
          <span v-else>N/A</span>
        </template>
        <template #cell(question)="{ item }">
          <feather-icon :icon="`${item.requestType === 'Information' ? 'Info' : 'FileText'}Icon`" :class="`text-success`"></feather-icon><span>{{ item.documentType }}</span>
          <div class="pl-1">{{ item.question }}</div>
        </template>
        <template #cell(created)="{ item }">
          {{ item.createdOn }}
        </template>
        <template #cell(responded)="{ item }">
          <span v-if="item.receivedOn" class="text-success">
            {{ item.receivedOn }}
          </span>
          <feather-icon v-else icon="ClockIcon" :class="`text-success`"></feather-icon>
        </template>
      </b-table>
      <b-row v-else>
        <b-col sm="12">
          The list is empty
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BRow, BCol, BContainer, BTable } from 'bootstrap-vue'
import { mapActions, mapState } from 'vuex'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BTable,
  },
  data() {
    return {
    }
  },
  async mounted() {
    await this.reload()
  },
  computed: {
    ...mapState('customer-request', { list: 'customerRequests' }),
    columns() {
      return [
        { key: 'customer', label: 'Customer' },
        { key: 'question', label: 'Document Type / Question' },
        { key: 'created', label: 'Created' },
        { key: 'responded', label: 'Responded ?' },
      ]
    },
  },
  methods: {
    ...mapActions('customer-request', { getRequests: 'dailyMails' }),
    async reload() {
      await this.getRequests()
    },
  },
}
</script>
