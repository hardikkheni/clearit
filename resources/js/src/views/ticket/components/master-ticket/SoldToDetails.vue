<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" :title="`Sold to details`">
        <template #header>
          <span class="lead collapse-title">Sold to details </span>
        </template>
        <b-list-group class="mt-1">
          <b-list-group-item v-for="(col, i) of info" :key="i">
            <b-row>
              <b-col cols="6">{{ col.key }}:</b-col>
              <b-col cols="6">{{ col.value }}</b-col>
            </b-row>
          </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BListGroup, BListGroupItem, BRow, BCol } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { formatPhone } from '@/utils/filters'
import { getCountryFullName, getStateFullName } from '@/utils/config'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BRow,
    BCol,
  },
  filters: {},
  props: {
    soldTo: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {}
  },
  computed: {
    info() {
      return [
        { key: 'First Name', value: this.soldTo.firstname },
        { key: 'Last Name', value: this.soldTo.lastname },
        ...(this.soldTo.busname ? [{ key: 'Legal Business Name', value: this.soldTo.busname }] : []),
        { key: 'Address', value: this.soldTo.address },
        { key: 'City', value: this.soldTo.city },
        { key: 'Country', value: getCountryFullName(this.soldTo.country) },
        { key: 'Province', value: getStateFullName(this.soldTo.country, this.soldTo.state) },
        { key: 'Fax', value: formatPhone(this.soldTo.fax, this.soldTo.state) },
        ...(this.soldTo.email ? [{ key: 'Email', value: this.soldTo.email }] : []),
        ...(this.soldTo.taxid ? [{ key: 'Tax ID #', value: this.soldTo.taxid }] : []),
      ]
    },
  },
  mounted() {},
  methods: {},
}
</script>
