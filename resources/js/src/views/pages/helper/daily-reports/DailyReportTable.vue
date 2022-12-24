<template>
  <b-row class="mt-3">
    <b-col cols="12">
      <b-table responsive :fields="columns" :items="items" :current-page="page">
        <template #cell(ticket_no)="{ item }">
          <router-link to="{}">
            {{ item['Ticket #'] }}
          </router-link>
        </template>
        <template #cell(status)="{ item }">
          {{ item.Status }}
        </template>
        <template #cell(agent_status)="{ item }">
          <span v-if="item['Agent status']">{{ item['Agent status'] }}</span>
          <span v-else>N/A</span>
        </template>
        <template #cell(customer)="{ item }">
          <span v-if="item.Customer">{{ item.Customer }}</span>
          <span v-else>N/A</span>
        </template>
        <template #cell(entry_no)="{ item }">
          <span v-if="item['entry number']">
            {{ item['entry number'] | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
          <span v-else>-</span>
        </template>
        <template #cell(eta)="{ item }">
          <span v-if="item.eta">
            {{ item.eta | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
          <span v-else>N/A</span>
        </template>
        <template #cell(last_free_day)="{ item }">
          <span v-if="item.lastFreeDay">
            {{ item.lastFreeDay | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
          <span v-else>-</span>
        </template>
        <template #cell(ticket_created)="{ item }">
          <span v-if="item['Ticket created']">
            {{ item['Ticket created'] | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
        </template>
        <template #cell(uploaded)="{ item }">
          <span v-if="item['AN Uploaded']">
            {{ item['AN Uploaded'] | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
        </template>
        <template #cell(verified)="{ item }">
          {{ item['User Verified'] }}
        </template>
        <template #cell(isf_uploaded)="{ item }">
          <span v-if="item['ISF Uploaded']">
            {{ item['ISF Uploaded'] | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
          <span v-else>-</span>
        </template>
        <template #cell(container_no)="{ item }">
          <span v-if="item.containerNumber">{{ item.containerNumber }}</span>
          <span v-else>N/A</span>
        </template>
        <template #cell(departure_date)="{ item }">
          <span v-if="item.departureDate">
            {{ item.departureDate | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
          </span>
          <span v-else>N/A</span>
        </template>
        <template #cell(mbol)="{ item }">
          <span v-if="item.mBOL">{{ item.mBOL }}</span>
          <span v-else>N/A</span>
        </template>
        <template #cell(icon)="{ item }">
          <blank-avatar
            v-if="item.affiliateid"
            style="margin-right: 5px"
            :src="require(`@/assets/images/icons/${item.affiliateCode}-icon.png`)"
          />
        </template>
      </b-table>
    </b-col>
  </b-row>
</template>
<script>
import { BTable, BRow, BCol } from 'bootstrap-vue'
import { mapState } from 'vuex'
import dayjs from 'dayjs'
import BlankAvatar from '@/components/BlankAvatar.vue'
import { DATE_FORMAT_SHORT_YEAR } from '@/utils/config'

export default {
  components: { BTable, BRow, BCol, BlankAvatar },
  filters: {
    dateFormat(value, format) {
      return value && dayjs(value).format(format)
    },
  },
  props: {
    page: {
      type: Number,
      default: 1,
    },
    items: {
      type: Array,
      default: null,
    },
    report: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT_SHORT_YEAR,
    }
  },
  computed: {
    ...mapState('daily-reports', { list: 'list' }),
    columns() {
      let extra = []
      switch (this.report) {
        case 'isverified':
        case 'notverified':
          extra = [
            { key: 'verified', label: 'Verified' },
            { key: 'ticket_created', label: 'Ticket Created' },
            { key: 'isf_uploaded', label: 'ISF Uploaded' },
          ]
          break
        case 'iskeyed':
        case 'notkeyed':
        case 'prekey_billing':
        case 'release_team_tickets':
          extra = [
            { key: 'entry_no', label: 'Entry #' },
            { key: 'eta', label: 'ETA' },
            { key: 'last_free_day', label: 'Last Free Day' },
            { key: 'ticket_created', label: 'Ticket Created' },
            { key: 'uploaded', label: 'An Uploaded' },
          ]
          break
        case 'isf_ticket':
          extra = [
            { key: 'ticket_created', label: 'Ticket Created' },
            { key: 'container_no', label: 'Container #' },
            { key: 'eta', label: 'ETA' },
            { key: 'departure_date', label: 'Departure Dt' },
            { key: 'mbol', label: 'mBOL' },
            { key: 'isf_uploaded', label: 'ISF Doc Uploaded' },
          ]
          break
        default:
          break
      }
      return [
        { key: 'ticket_no', label: 'Ticket #' },
        { key: 'status', label: 'Status' },
        { key: 'agent_status', label: 'Agent Status' },
        { key: 'customer', label: 'Customer' },
        ...extra,
        { key: 'icon', label: 'Affiliate Logo' },
      ]
    },
  },
}
</script>
