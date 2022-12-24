<template>
  <b-table responsive :fields="columns" :items="items" :tbody-tr-attr="rowAttr" @row-clicked="$emit('row-clicked', $event)">
    <template #cell(id)="{ item }">
      <router-link v-if="item.has_unread_messages == '1'" style="position: absolute; margin-left: -50px; margin-top: -3px" :to="{}">
        <feather-icon color="teal" icon="MailIcon" />
      </router-link>
      <router-link
        v-if="item.has_unread_upload_notifications == '1'"
        style="position: absolute; margin-left: -50px; margin-top: 15px"
        :to="{}"
      >
        <feather-icon icon="DownloadIcon" />
      </router-link>
      <router-link :to="{}">
        <span class="h4">{{ item.ticketId }}</span> <br />
        <span v-if="item.isdeleted == '1'" class="text-danger">(D)</span>
      </router-link>
    </template>
    <template #cell(customer)="{ item }">
      <router-link :to="{}">
        {{ item.customerName }}
      </router-link>
    </template>
    <template #cell(varified)="{ item }">
      <blank-avatar
        style="margin-right: 5px"
        :src="require(`@/assets/images/icons/${item.userVerified ? 'ico-verified' : 'ico-verified-un'}.png`)"
      />
    </template>
    <template #cell(createdon)="{ item }">
      <span>
        {{ item.createdon | dateFormat(DATE_FORMAT_SHORT_YEAR) }} <small> [{{ item.createdon | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
    <template #cell(type)="{ item }">
      <b-avatar v-bind="getTicketBadge(item.type, parseInt(item.transport || 0))" />
    </template>
    <template #cell(commodityCode)="{ item }">
      <span>{{ item.commodityCode }}</span>
    </template>
    <template #cell(tariffCodeEmailSent)="{ item }">
      <span v-if="item.tariffCodeEmailSent">
        {{ item.tariffCodeEmailSent | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
        <small> [{{ item.tariffCodeEmailSent | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
    <template #cell(has_unread_messages)="{ item }">
      <feather-icon v-if="item.has_unread_messages == '1'" icon="MailIcon" />
    </template>
    <template #cell(Affiliate)="{ item }">
      <span>
        {{ item.affiliateReferenceNumber }}
      </span>
    </template>
    <template #cell(idffiled)="{ item }">
      <span v-if="item.isfFiledOn">
        {{ item.isfFiledOn | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
        <small> [{{ item.isfFiledOn | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
    <template #cell(estdeparture)="{ item }">
      <span v-if="item.departureDate">
        {{ item.departureDate | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
        <small> [{{ item.departureDate | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
    <template #cell(resubmitdate)="{ item }">
      <span v-if="item.isfResubmitDate">
        {{ item.isfResubmitDate | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
        <small> [{{ item.isfResubmitDate | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
    <template #cell(entrynum)="{ item }">
      <span>
        {{ item.SBentryNum }}
      </span>
    </template>
    <template #cell(freightosready)="{ item }">
      <span>
        {{ item.billingReady == '1' ? 'Y' : 'N' }}
      </span>
    </template>
    <template #cell(eta)="{ item }">
      <span v-if="item.eta">
        {{ item.eta | dateFormat(DATE_FORMAT_SHORT_YEAR) }}
        <small> [{{ item.eta | dateFormat(HOUR_FORMAT) }}] </small>
      </span>
    </template>
  </b-table>
</template>

<script>
import { mapState } from 'vuex'
import { BTable, BAvatar } from 'bootstrap-vue'
import dayjs from 'dayjs'

import BlankAvatar from '@/components/BlankAvatar.vue'
import { PAPS_NAME, DATE_FORMAT_SHORT_YEAR, HOUR_FORMAT } from '@/utils/config'

import { getTicketBadge, TICKET_TYPE_AES, TICKET_TYPE_FREIGHT } from '@/utils/ticket'

export default {
  components: { BTable, BAvatar, BlankAvatar },
  filters: {
    dateFormat(value, format) {
      return value && dayjs(value).format(format)
    },
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    role: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT_SHORT_YEAR,
      HOUR_FORMAT,
      TICKET_TYPE_AES,
      TICKET_TYPE_FREIGHT,
    }
  },
  computed: {
    ...mapState('auth', { alibabaId: 'alibabaId', freightosId: 'freightosId' }),
    columns() {
      let extra = []
      switch (this.role) {
        case 'docreview':
          extra = [
            { key: 'commodityCode', label: 'COMMODITIES' },
            { key: 'tariffCodeEmailSent', label: 'EMAIL SENT' },
          ]
          break
        case 'isf':
          extra = [
            { key: 'idffiled', label: 'ISF Filed' },
            { key: 'estdeparture', label: 'Est Departure' },
            { key: 'resubmitdate', label: 'Resubmit Date' },
          ]
          break
        case 'prekey':
          extra = [
            { key: 'entrynum', label: 'Entry Num' },
            { key: 'freightosready', label: 'FreightOS Ready' },
          ]
          break
        case 'arrival':
        case 'release':
          extra = [
            { key: 'eta', label: 'Est Time Arrival' },
            { key: 'itBondRequired', label: 'IT Bond Required' },
            { key: 'onFile', label: 'IT Bond Status' },
          ]
          break
        default:
          break
      }
      return [
        {
          key: 'id',
          label: 'TICKET ID',
        },
        { key: 'customer', label: 'CUSTOMER' },
        {
          key: 'ccn',
          label: `${PAPS_NAME} / Cargo Control No.`,
        },
        { key: 'varified', label: 'VARIFIED' },
        { key: 'createdon', label: 'Created' },
        { key: 'type', label: 'TYPE' },
        { key: 'checkboxCounts', label: 'CHECKLIST' },
        ...extra,
        { key: 'status', label: 'Status' },
        { key: 'agentStatus', label: 'Agent Status' },
        { key: 'has_unread_messages', label: 'Msg' },
        { key: 'Affiliate', label: 'Affiliate' },
      ]
    },
  },
  methods: {
    getTicketBadge,
    rowAttr(
      item,
      // _type
    ) {
      // if (!item || type !== 'row') return {}
      return {
        style: item.dashboardColor ? `background-color: ${item.dashboardColor};` : '',
      }
    },
  },
}
</script>
