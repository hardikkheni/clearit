<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Ticket Actions">
        <template #header>
          <span class="lead collapse-title">Ticket Actions</span>
        </template>
        <b-list-group class="mt-1">
          <template v-if="info.length">
            <b-list-group-item v-for="(row, i) of info" :key="i">
              <div class="clearfix">
                <div class="float-left">
                  <span v-html="row.key" />
                </div>
                <div class="float-right">
                  <span>{{ row.value }}</span>
                </div>
              </div>
            </b-list-group-item>
          </template>
          <b-list-group-item v-else> No actions </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BListGroup, BListGroupItem } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { NOTIFICATION_TYPE_AFFILIATE_DOCUMENT_UPLOAD } from '@/utils/notification'
import { DATETIME_FORMAT } from '@/utils/config'
import { dateFormat } from '@/utils/filters'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
  },
  filters: {},
  props: {
    notifications: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {}
  },
  computed: {
    info() {
      return this.notifications.map(i => {
        let key = ''
        let value = ''
        switch (i.type) {
          case NOTIFICATION_TYPE_AFFILIATE_DOCUMENT_UPLOAD:
          case 'upload':
            key += `Document upload - ${i.description}`
            break
          case 'payment_card':
            key += `Payment of $ ${i.description} processed by card`
            break
          default:
            key += i.description
            break
        }
        if (i.createdOn) {
          value += `${dateFormat(i.createdOn, DATETIME_FORMAT)}`
        }
        return { key, value }
      })
    },
  },
  mounted() {},
  methods: {},
}
</script>
