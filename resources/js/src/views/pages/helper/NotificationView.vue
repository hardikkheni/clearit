<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col align-self="start">
            <span class="h2 align-middle mr-1">Notifications</span>
          </b-col>
        </b-row>
        <hr />
        <b-row v-if="notificationCount">
          <b-col align-self="start">
            <b-button class="d-inline-flex" variant="primary" @click.prevent="markViewedOnTheScreen">Clear this screen</b-button>
            <b-button class="d-inline-flex" variant="primary" @click.prevent="markViewedAll">Clear ALL notifications</b-button>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <template v-if="notificationCount">
        <b-row>
          <b-col align-self="start" cols md="6">
            <b-card v-for="(noti, i) of notifications" :key="i" border-variant="success" bg-variant="transparent" class="shadow-none">
              <template #header>
                <b-card-title>
                  <feather-icon size="25" icon="InfoIcon" />
                  <span v-if="noti.type == AFFILIATE_DOCUMENT_UPLOAD">
                    {{ noti.title }} <span class="text-success">({{ noti.affiliate_company_name }})</span>
                  </span>
                  <span v-else>
                    {{ noti.title }}
                  </span>
                </b-card-title>
                <div class="heading-elements float-right">
                  <ul class="list-inline mb-0">
                    <li>
                      <a data-action="collapse" @click.prevent="markViewedOne(noti.id)">
                        <feather-icon icon="XIcon" size="16" />
                      </a>
                    </li>
                  </ul>
                </div>
              </template>
              <b-card-text>
                <span>{{ noti.message }}</span>
                <br />
                <span>{{ noti.user_data && noti.user_data.email }}</span>
                <br />
                <small>{{ noti.createdOn }}</small>
              </b-card-text>
            </b-card>
          </b-col>
        </b-row>
        <b-row>
          <b-col align-self="start">
            <b-button class="d-inline-flex" variant="primary" @click.prevent="markViewedOnTheScreen">Clear this screen</b-button>
            <b-button class="d-inline-flex" variant="primary" @click.prevent="markViewedAll">Clear ALL notifications</b-button>
          </b-col>
        </b-row>
      </template>
      <p v-else>No notifications are available</p>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BCardText, BCardTitle, BRow, BCol, BContainer, BButton } from 'bootstrap-vue'
import { mapActions, mapState } from 'vuex'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

import { NOTIFICATION_TYPE_AFFILIATE_DOCUMENT_UPLOAD } from '@/utils/notification'

export default {
  components: {
    BCard,
    BCardText,
    BCardTitle,
    BRow,
    BCol,
    BContainer,
    BButton,
  },
  data() {
    return {
      AFFILIATE_DOCUMENT_UPLOAD: NOTIFICATION_TYPE_AFFILIATE_DOCUMENT_UPLOAD,
    }
  },
  computed: {
    ...mapState('notification', { notifications: 'notifications', notificationCount: 'count' }),
  },
  mounted() {
    this.fetch()
  },
  methods: {
    ...mapActions('notification', { getNotification: 'get', markViewedNotification: 'markViewed' }),
    async markViewedOnTheScreen() {
      await this.markViewed({ ids: this.notifications.map(i => i.id), is_affiliate: this.$route.query.is_affiliate, all: false })
    },
    async markViewedAll() {
      await this.markViewed({ ids: [], is_affiliate: this.$route.query.is_affiliate, all: true })
    },
    async markViewedOne(id) {
      await this.markViewed({ ids: [id], is_affiliate: this.$route.query.is_affiliate, all: false })
    },
    async markViewed(payload) {
      try {
        const t = await this.markViewedNotification(payload)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.message,
          },
        })
        this.fetch()
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
    async fetch() {
      await this.getNotification(this.$route.query.is_affiliate)
    },
  },
}
</script>
