<template>
  <div class="navbar-container d-flex content align-items-center">
    <!-- Nav Menu Toggler -->
    <!-- <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link class="nav-link" @click="toggleVerticalMenuActive">
          <feather-icon icon="MenuIcon" size="21" />
        </b-link>
      </li>
    </ul> -->

    <div class="bookmark-wrapper align-items-center flex-grow-1">
      <router-link :to="{ name: 'dashboard' }">
        <b-img :src="require('@/assets/images/logo/logo-backend.png')" alt="logo" />
      </router-link>
    </div>

    <!-- Left Col -->
    <!-- <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex">
      <bookmarks />
    </div> -->

    <!-- Right Col -->
    <b-navbar-nav class="nav align-items-center ml-auto">
      <!-- <locale /> -->
      <!-- <dark-Toggler class="d-none d-lg-block" /> -->
      <!-- <search-bar /> -->
      <!-- <cart-dropdown /> -->
      <notification-dropdown />
      <!-- <ul class="nav navbar-nav">
        <li class="nav-item">
          <b-link class="nav-link">
            <feather-icon icon="MenuIcon" size="21" />
          </b-link>
        </li>
      </ul> -->
      <dashboard-dropdown />
      <user-dropdown />
    </b-navbar-nav>
  </div>
</template>

<script>
import {
  // BLink,
  BNavbarNav,
  BImg,
} from 'bootstrap-vue'
// import Bookmarks from './components/Bookmarks.vue'
// import Locale from './components/Locale.vue'
// import DarkToggler from './components/DarkToggler.vue'
// import SearchBar from './components/SearchBar.vue'
// import CartDropdown from './components/CartDropdown.vue'
import NotificationDropdown from '@/components/navbar/NotificationDropdown.vue'
import DashboardDropdown from '@/components/navbar/DashboardDropdown.vue'
import UserDropdown from './components/UserDropdown.vue'

export default {
  components: {
    // BLink,

    // Navbar Components
    BNavbarNav,
    // Bookmarks,
    // Locale,
    // DarkToggler,
    // SearchBar,
    // CartDropdown,
    BImg,
    NotificationDropdown,
    UserDropdown,
    DashboardDropdown,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
}
</script>
