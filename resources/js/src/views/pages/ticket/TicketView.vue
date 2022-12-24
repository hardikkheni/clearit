<template>
  <div>
    <b-alert v-height-fade :show="!!alert.variant" :variant="alert.variant" dismissible fade>
      <div class="alert-body">
        <span>{{ alert.message }}</span>
      </div>
    </b-alert>
    <b-skeleton-wrapper :loading="loading">
      <template #loading>
        <b-row>
          <b-col cols md="7" sm="12">
            <b-skeleton height="800px" />
          </b-col>
          <b-col cols md="5" sm="12">
            <b-skeleton v-if="role == 'master'" height="100px" />
            <b-skeleton height="400px" />
            <b-skeleton height="400px" />
          </b-col>
        </b-row>
      </template>
      <master-ticket v-if="role == 'master'" :ticket-view="ticketView" @scroll-to="scrollTo" />
      <role-ticket v-else :ticket-view="ticketView" />
    </b-skeleton-wrapper>
  </div>
</template>

<script>
import { BRow, BCol, BSkeletonWrapper, BSkeleton, BAlert } from 'bootstrap-vue'
import { mapActions, mapState } from 'vuex'

import { heightFade } from '@core/directives/animations'
import RoleTicket from '@/views/ticket/RoleTicket.vue'
import MasterTicket from '@/views/ticket/MasterTicket.vue'

export default {
  components: {
    BRow,
    BCol,
    BSkeletonWrapper,
    BSkeleton,
    RoleTicket,
    MasterTicket,
    BAlert,
  },
  directives: {
    heightFade,
  },
  data() {
    return {}
  },
  computed: {
    ...mapState('ticket', { loading: 'loading', ticketView: 'ticketView', viewTicketAlert: 'viewTicketAlert' }),
    role() {
      return this.$route.params.role
    },
    alert() {
      return this.viewTicketAlert || {}
    },
    messages() {
      return (this.ticketView?.messages || []).map(i => ({ ...i, userId: i.userid }))
    },
  },
  async mounted() {
    await this.getTicket({
      ...this.$route.params,
    })
  },
  methods: {
    ...mapActions('ticket', { getTicket: 'getTicket' }),
    scrollTo(id) {
      if (this.$route.hash === `#${id}`) return
      this.$router
        .push({
          name: this.$route.name,
          params: this.$route.params,
          ...(id ? { hash: `#${id}` } : {}),
        })
        .catch(console.warn)
    },
  },
}
</script>
