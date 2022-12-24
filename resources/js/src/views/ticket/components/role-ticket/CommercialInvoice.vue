<template>
  <div class="ticket-collapse-card">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> Documents </b-card-title>
      <hr />
      <b-list-group v-if="(getFullTicket.ticket_documents || []).length">
        <template v-for="(doc, i) of getFullTicket.ticket_documents || []">
          <b-list-group-item v-if="hasBitMaskValue(doc.roleBitmask, role.roleBitmask)" :key="i">
            <router-link :to="{}">{{ doc.description }}</router-link>
            <span class="float-right">
              {{ doc.createdOn | dateFormat('MM/DD/YYYY') }} @ {{ doc.createdOn | dateFormat('hh:mm a') }}
              <template v-if="doc.uploadedBy"> by {{ doc.uploadedBy }} </template>
            </span>
          </b-list-group-item>
        </template>
      </b-list-group>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle, BListGroupItem, BListGroup } from 'bootstrap-vue'
import { hasBitMaskValue } from '@/utils/permissions'
import { dateFormat } from '@/utils/filters'

export default {
  components: {
    BCard,
    BCardTitle,
    BListGroupItem,
    BListGroup,
  },
  filters: {
    dateFormat,
  },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
    role: {
      type: Object,
      required: true,
    },
  },
  methods: {
    hasBitMaskValue,
  },
}
</script>
