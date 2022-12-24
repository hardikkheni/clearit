<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Personal Identification Upload">
        <template #header>
          <span class="lead collapse-title">Personal Identification Upload</span>
        </template>
        <span class="h5 mt-1">UPLOADED DOCUMENTS </span>
        <b-list-group class="mt-1">
          <template v-if="upds.length">
            <b-table-simple>
              <b-thead>
                <b-tr>
                  <b-th>Document type</b-th>
                  <b-th>Description</b-th>
                  <b-th>&nbsp;</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <b-tr v-for="(upd, i) of upds" :key="i">
                  <b-td>{{ getPersonalUserDocumentType(upd.documentType) }}</b-td>
                  <b-td>{{ upd.description }}</b-td>
                  <b-td><b-button size="sm" variant="primary">View</b-button></b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </template>
          <b-list-group-item v-else> No documents uploaded </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BListGroup, BListGroupItem, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BButton } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { getPersonalUserDocumentType } from '@/utils/documentType'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BTableSimple,
    BThead,
    BTbody,
    BTr,
    BTh,
    BTd,
    BButton,
  },
  filters: {},
  props: {
    upds: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {}
  },
  computed: {},
  mounted() {},
  methods: {
    getPersonalUserDocumentType,
  },
}
</script>
