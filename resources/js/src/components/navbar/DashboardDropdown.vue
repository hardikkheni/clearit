<template>
  <b-nav-item-dropdown class="dropdown-notification" menu-class="dropdown-menu-media" right>
    <template #button-content>
      <feather-icon class="text-body" icon="MenuIcon" size="21" />
    </template>
    <!-- Notifications -->
    <vue-perfect-scrollbar v-once :settings="perfectScrollbarSettings" class="scrollable-container media-list scroll-area" tagname="li">
      <!-- System Notifications -->
      <template v-for="(not, i) of computedNotifications">
        <b-dropdown-item
          :key="i"
          v-bind="{
            ...(not.to ? { to: not.to } : { href: not.url }),
          }"
        >
          <template #aside>
            <feather-icon class="text-body" size="21" :badge-classes="not.type" :badge="not.badge" :icon="not.icon" />
          </template>
          <span class="font-weight-bolder"> {{ not.title }} </span>
        </b-dropdown-item>
      </template>
    </vue-perfect-scrollbar>
  </b-nav-item-dropdown>
</template>

<script>
import { BNavItemDropdown, BDropdownItem } from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import Ripple from 'vue-ripple-directive'

import {
  PERMISSION_BITMASK_TO_DO_CHECKLISTS,
  PERMISSION_BITMASK_AGENT_STATUSES,
  PERMISSION_BITMASK_TICKET_STATUSES,
  permissionMixin,
} from '@/utils/permissions'
import {mapState} from "vuex";

export default {
  components: {
    BNavItemDropdown,
    VuePerfectScrollbar,
    BDropdownItem,
  },
  directives: {
    Ripple,
  },
  mixins: [permissionMixin],
  data() {
    return {
      notifications: [
        {
          title: 'Dashboard',
          to: {
            name: 'dashboard',
          },
          // for reference only
          // permissions: ['*'],
          // needToBeMaster: false
        },
        {
          title: 'My reminders',
          to: {
            name: 'helper-reminders',
          },
        },
        {
          title: 'My requests',
          to: {
            name: 'helper-daily-client-requests',
          },
        },
        {
          title: 'Customers',
          to: {
            name: 'helper-customer',
          },
        },
        {
          title: 'Agent Statuses',
          to: {
            name: 'helper-agent-status-list',
          },
          permissions: [PERMISSION_BITMASK_AGENT_STATUSES],
        },
        {
          title: 'Ticket Statuses',
          to: {
            name: 'helper-ticket-status-list',
          },
          permissions: [PERMISSION_BITMASK_TICKET_STATUSES],
        },
        {
          title: 'To Do Checklist',
          to: {
            name: 'helper-to-do-ticket-item-list',
          },
          permissions: [PERMISSION_BITMASK_TO_DO_CHECKLISTS],
        },
        {
          title: 'Daily Reports',
          to: { name: 'helper-daily-reports' },
        },
        {
          title: 'Administration',
          to: { name: 'helper-administration' },
          needToBeMaster: true,
        },
      ],
      perfectScrollbarSettings: {
        // maxScrollbarLength: 60,
        wheelPropagation: false,
      },
    }
  },
  computed: {
    computedNotifications() {
      return this.notifications.filter(i => this.checkPermission(i))
    },
  },
}
</script>

<style>
</style>
