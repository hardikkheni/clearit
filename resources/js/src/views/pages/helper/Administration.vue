<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <h2>Admin Menu</h2>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <template v-for="(link, i) of links">
        <router-link v-if="checkPermission(link)" :key="i" :to="link.to">
          <h4 class="text-blue">
            <u>{{ link.title }} </u>
          </h4>
        </router-link>
      </template>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BContainer } from 'bootstrap-vue'

import {
  PERMISSION_BITMASK_AGENTS,
  PERMISSION_BITMASK_AFFILIATES,
  PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES,
  PERMISSION_BITMASK_DOC_UPLOAD_TYPES,
  PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES,
  permissionMixin,
} from '@/utils/permissions'

export default {
  components: {
    BCard,
    BContainer,
  },
  mixins: [permissionMixin],
  data() {
    return {
      links: [
        {
          title: 'Manage Affiliates',
          to: {
            name: 'helper-affiliate-list',
          },
          permissions: [PERMISSION_BITMASK_AFFILIATES],
        },
        {
          title: 'Manage Agents',
          to: {
            name: 'helper-agent-list',
          },
          permissions: [PERMISSION_BITMASK_AGENTS],
        },
        {
          title: 'Manage Agents Permissions',
          to: {
            name: 'edit-helper-agent-permission-manager',
          },
          needToBeMaster: true,
        },
        {
          title: 'Manage Alert Messages',
          to: {
            name: 'helper-alert-message-list',
          },
          permissions: [PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES],
        },
        {
          title: 'Manage API Users',
          to: {
            name: 'helper-api-user-list',
          },
          needToBeMaster: true,
        },
        {
          title: 'Manage Document Upload Types',
          to: {
            name: 'helper-document-upload-type-list',
          },
          permissions: [PERMISSION_BITMASK_DOC_UPLOAD_TYPES],
        },
        {
          title: 'Manage Freight Forwarder',
          to: {
            name: 'helper-freight-forwarder-list',
          },
          permissions: [PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES],
        },
        {
          title: 'Role and Status Setup',
          to: {
            name: 'helper-role-status',
          },
          needToBeMaster: true,
        },
        {
          title: 'Role Management',
          to: {
            name: 'helper-role-managment',
          },
          needToBeMaster: true,
        },
        {
          title: 'Manage Ticket Status Dependencies',
          to: {
            name: 'helper-ticket-status-depends',
          },
          permissions: [PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES],
        },
        {
          title: 'FreightOS Billing',
          to: {
            name: 'helper-freightos-billing',
          },
        },
      ],
    }
  },
}
</script>

<style lang="scss" scoped>
.list-item {
  text-decoration: underline;
}
</style>
