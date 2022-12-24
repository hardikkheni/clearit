<template>
  <b-nav-item-dropdown class="dropdown-notification mr-25" menu-class="dropdown-menu-media" right>
    <template #button-content>
      <feather-icon badge="6" badge-classes="bg-danger" class="text-body" icon="BellIcon" size="21" />
    </template>

    <!-- Header -->
    <li class="dropdown-menu-header">
      <div class="dropdown-header d-flex">
        <h4 class="notification-title mb-0 mr-auto">Notifications</h4>
        <b-badge pill variant="light-primary"> 6 New </b-badge>
      </div>
    </li>

    <!-- Notifications -->
    <vue-perfect-scrollbar v-once :settings="perfectScrollbarSettings" class="scrollable-container media-list scroll-area" tagname="li">
      <!-- Account Notification -->
      <!-- <b-link v-for="notification in notifications" :key="notification.subtitle">
        <b-media>
          <template #aside>
            <b-avatar size="32" :src="notification.avatar" :text="notification.avatar" :variant="notification.type" />
          </template>
          <p class="media-heading">
            <span class="font-weight-bolder">
              {{ notification.title }}
            </span>
          </p>
          <small class="notification-text">{{ notification.subtitle }}</small>
        </b-media>
      </b-link> -->

      <!-- System Notification Toggler -->
      <!-- <div class="media d-flex align-items-center">
        <h6 class="font-weight-bolder mr-auto mb-0">System Notifications</h6>
        <b-form-checkbox :checked="true" switch />
      </div> -->

      <!-- System Notifications -->
      <b-link v-for="(noti, i) of systemNotifications" :key="i" :to="noti.to || {}">
        <b-media>
          <template #aside>
            <!-- <b-avatar  size="32" :variant="notification.type"> -->
            <feather-icon class="text-body" size="21" :badge-classes="noti.type" :badge="noti.badge" :icon="noti.icon" />
            <!-- </b-avatar> -->
          </template>
          <p class="media-heading">
            <span class="font-weight-bolder">
              {{ noti.title }}
            </span>
          </p>
          <!-- <small class="notification-text">{{ notification.subtitle }}</small> -->
        </b-media>
      </b-link>
    </vue-perfect-scrollbar>

    <!-- Cart Footer -->
    <!-- <li class="dropdown-menu-footer">
      <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" block>Read all notifications</b-button>
    </li> -->
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown,
  BBadge,
  BMedia,
  BLink,
  // BButton
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import Ripple from 'vue-ripple-directive'

export default {
  components: {
    BNavItemDropdown,
    BBadge,
    BMedia,
    BLink,
    VuePerfectScrollbar,
    // BButton,
  },
  directives: {
    Ripple,
  },
  setup() {
    /* eslint-disable global-require */
    // const notifications = [
    //   {
    //     title: 'Congratulation Sam 🎉',
    //     avatar: require('@/assets/images/avatars/6-small.png'),
    //     subtitle: 'Won the monthly best seller badge',
    //     type: 'light-success',
    //   },
    //   {
    //     title: 'New message received',
    //     avatar: require('@/assets/images/avatars/9-small.png'),
    //     subtitle: 'You have 10 unread messages',
    //     type: 'light-info',
    //   },
    //   {
    //     title: 'Revised Order 👋',
    //     avatar: 'MD',
    //     subtitle: 'MD Inc. order updated',
    //     type: 'light-danger',
    //   },
    // ]
    /* eslint-disable global-require */

    const systemNotifications = [
      {
        title: 'Reminders',
        subtitle: null,
        type: 'bg-light-danger',
        icon: 'AlertTriangleIcon',
        badge: 28,
      },
      {
        title: 'Affiliates',
        subtitle: 'Last month sales report generated',
        type: 'bg-light-danger',
        icon: 'AlertTriangleIcon',
        badge: 33,
      },
      {
        title: 'Messages',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-info',
        icon: 'MessageCircleIcon',
        badge: 15,
      },
      {
        title: 'FF Msg',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-info',
        icon: 'MessageCircleIcon',
        badge: 3,
      },
      {
        title: 'New',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-danger',
        icon: 'FileIcon',
        badge: 167,
      },
      {
        title: 'Unvarified',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-success',
        icon: 'UserIcon',
        badge: 65,
        to: {
          name: 'helper-customer-unverified-with-poa-list',
        },
      },
      {
        title: 'Replies',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-success',
        icon: 'UserIcon',
        badge: 85,
        to: {
          name: 'helper-customer-request-list',
        },
      },
      {
        title: 'Notifications',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-danger',
        icon: 'AlertTriangleIcon',
        badge: 85,
        to: {
          name: 'helper-notification-list',
        },
      },
      {
        title: 'Broker Review',
        subtitle: 'BLR Server using high memory',
        type: 'bg-light-warning',
        icon: 'FileIcon',
        badge: 7,
      },
    ]

    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
    }

    return {
      // notifications,
      systemNotifications,
      perfectScrollbarSettings,
    }
  },
}
</script>

<style>
</style>
