<template>
  <b-table responsive :fields="columns" :items="items" @row-clicked="$emit('row-clicked', $event)">
    <template #cell(id)="{ item }">
      <router-link v-if="item.has_unread_messages" style="position: absolute; margin-left: -33px; margin-top: -3px" :to="{}">
        <feather-icon color="teal" icon="MailIcon" />
      </router-link>
      <router-link v-if="item.has_unread_upload_notifications" style="position: absolute; margin-left: -33px; margin-top: 15px" :to="{}">
        <feather-icon icon="DownloadIcon" />
      </router-link>
      <router-link :to="{}">
        <span class="h4">{{ getTicketNumberFromId(item.id) }}</span> <br />
        <span v-if="item.isDeleted" class="text-danger">(D)</span>
      </router-link>
    </template>
    <template #cell(eno)="{ item }">
      <router-link v-if="item.transactionNumber" :to="{}">
        <span class="h4">{{ getTicketNumberFromId(item.id) }}</span> <br />
      </router-link>
      <span v-else>N/A</span>
    </template>

    <template #cell(ccn)="{ item }">
      <router-link v-if="item.vendor_carrier_ref" :to="{}">
        {{ item.vendor_carrier_ref }}
      </router-link>
      <span v-else>N/A</span>
    </template>
    <template #cell(name)="{ item }">
      <span :style="{ color: '#c63645' }" v-if="item.isReference">(R)</span>
      <router-link :to="{}">
        <span>{{ item.ticket_name }}</span>
      </router-link>
    </template>
    <template #cell(agent_status)="{ item }">
      <span v-if="item.agent_status_type">{{ item.agent_status_type }}</span>
      <span v-else>N/A</span>
    </template>
    <template #cell(createdon)="{ item }">
      {{ item.createdOn | dateFormat(DATE_FORMAT) }} <br />
      [{{ item.createdOn | dateFormat(HOUR_FORMAT) }}]
    </template>
    <template #cell(type)="{ item }">
      <b-avatar v-bind="getTicketBadge(item.type, parseInt(item.transport || 0))" />
    </template>
    <template #cell(eta)="{ item }">
      <span v-if="item.eta">
        {{ item.eta | dateFormat(DATE_FORMAT) }} <br />
        [{{ item.eta | dateFormat(HOUR_FORMAT) }}]
      </span>
      <span v-else>-</span><br />
      <span v-if="item.lastFreeDay">
        {{ item.lastFreeDay | dateFormat(DATE_FORMAT) }}
      </span>
    </template>
    <template #cell(agents)="{ item }">
      <span v-if="item.agent_guid && item.processing_agent_guid">
        <span v-if="item.agent_guid">
          <router-link :to="{}">
            {{ item.agent_guid }}
          </router-link>
          <br />
        </span>
        <router-link v-if="item.processing_agent_guid" :to="{}">
          {{ item.processing_agent_name }}
        </router-link>
      </span>
      <span v-else> N/A </span>
    </template>
    <template #cell(amount)="{ item }">
      <template v-if="item.amount && isFinite(Number(item.amount))">
        <span> ${{ Number(item.amount).toLocaleString('en-US') }} </span><br />
        <template v-if="item.type === TICKET_TYPE_AES">
          <span v-if="item.isPaid"> (paid) </span>
        </template>
        <template v-else-if="item.type === TICKET_TYPE_FREIGHT">
          <span v-if="item.isPaid"> (accepted) </span>
          <span v-else> (unpaid) </span>
        </template>
        <template v-else>
          <span v-if="item.isPaid"> (paid) </span>
          <span v-else> (unpaid) </span>
        </template>
      </template>
      <template v-else>
        <span>{{ additinalCharges(item) }}</span>
      </template>
    </template>
    <template #cell(isf)="{ item }">
      <b-avatar v-if="item.isffiled" variant="flat-success" :src="require('@/assets/images/icons/customer-check.png')" />
    </template>
    <template #cell(status)="{ item }">
      <span class="badge badge-pill" :style="{ background: `#${item.hexcolor}`, color: `#${item.texthexcolor}` }">{{ item.status }}</span>
    </template>
    <template #cell(account)="{ item }">
      <span class="d-flex">
        <blank-avatar
          style="margin-right: 5px"
          :src="require(`@/assets/images/icons/${item.isVerified ? 'ico-verified' : 'ico-verified-un'}.png`)"
        />
        <blank-avatar
          style="margin-right: 5px"
          :src="require(`@/assets/images/icons/${item.isCertificate ? 'ico-certificate' : 'ico-certificate-un'}.png`)"
        />
        <blank-avatar style="margin-right: 5px" :src="require(`@/assets/images/icons/${item.ispga ? 'ico-pga' : 'ico-pga-un'}.png`)" />
        <blank-avatar style="margin-right: 5px" :src="require(`@/assets/images/icons/${item.isCc ? 'ico-cc' : 'ico-cc-un'}.png`)" />
        <blank-avatar
          v-if="item.affiliateId && item.affiliateId == alibabaId"
          style="margin-right: 5px"
          :src="require('@/assets/images/icons/freightos-icon.png')"
        />
        <blank-avatar
          v-if="item.affiliateId && item.affiliateId == freightosId"
          style="margin-right: 5px"
          :src="require('@/assets/images/icons/alibaba-icon.png')"
        />
      </span>
    </template>
  </b-table>
</template>

<script>
import { mapState } from 'vuex'
import { BTable, BAvatar } from 'bootstrap-vue'

import BlankAvatar from '@/components/BlankAvatar.vue'
import { PAPS_NAME, DATE_FORMAT, HOUR_FORMAT } from '@/utils/config'

import { getTicketNumberFromId, getTicketBadge, TICKET_TYPE_AES, TICKET_TYPE_FREIGHT, getAdditionalCharges } from '@/utils/ticket'
import { dateFormat } from '@/utils/filters'

export default {
  components: { BTable, BAvatar, BlankAvatar },
  filters: {
    dateFormat,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      columns: [
        {
          key: 'id',
          label: 'TICKET ID',
        },
        { key: 'eno', label: 'ENTRY NO.' },
        {
          key: 'ccn',
          label: `${PAPS_NAME} / Cargo Control No.`,
        },
        { key: 'name', label: 'Name' },
        { key: 'agent_status', label: 'Agent Status' },
        { key: 'createdon', label: 'Created' },
        { key: 'type', label: 'Type' },
        { key: 'eta', label: 'ETA / LAST FREE DAY' },
        { key: 'agents', label: 'Agents' },
        { key: 'amount', label: 'Amount' },
        { key: 'isf', label: 'ISF' },
        { key: 'status', label: 'Status' },
        { key: 'account', label: 'Account' },
      ],
      DATE_FORMAT,
      HOUR_FORMAT,
      TICKET_TYPE_AES,
      TICKET_TYPE_FREIGHT,
    }
  },
  computed: {
    ...mapState('auth', { alibabaId: 'alibabaId', freightosId: 'freightosId' }),
  },
  methods: {
    getTicketNumberFromId,
    getTicketBadge,
    additinalCharges(item) {
      const m1 = getAdditionalCharges(item, 0)
      if (!(m1 > 0)) return 'N/A'
      const m2 = getAdditionalCharges(item, 1)
      const unpaid = getAdditionalCharges(item, 2)
      if (m1 === m2) return '(paid)'
      return ` (unpaid $${Number(unpaid).toLocaleString('en-US')})`
    },
  },
}
</script>
