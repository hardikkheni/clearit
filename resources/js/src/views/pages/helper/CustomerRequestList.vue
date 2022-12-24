<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col align-self="start">
            <span class="h2 align-middle mr-1">Customer Responses</span>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <template v-if="replyCount">
        <b-row>
          <b-col align-self="start" cols md="6">
            <b-card
              v-for="(custRequest, i) of customerRequests"
              :key="i"
              border-variant="success"
              bg-variant="transparent"
              class="shadow-none"
            >
              <template #header>
                <b-card-title>
                  <template v-if="custRequest.subHead == 'Document received'">
                    <feather-icon class="text-success" size="25" icon="FileIcon" />
                  </template>
                  <template v-else>
                    <feather-icon size="25" :style="{ color: 'orange' }" icon="InfoIcon" />
                  </template>
                  {{ custRequest.clientName }} {{ custRequest.ticketNumber ? '#' + custRequest.ticketNumber : '' }}
                  <span v-if="custRequest.affiliateName" class="text-success">({{ custRequest.affiliateName }})</span>
                </b-card-title>
                <div class="heading-elements float-right">
                  <ul class="list-inline mb-0">
                    <li>
                      <a data-action="collapse" @click.prevent="markViewed(custRequest.id)">
                        <feather-icon icon="XIcon" size="16" />
                      </a>
                    </li>
                  </ul>
                </div>
              </template>
              <b-card-text>
                <span>{{ custRequest.subHead }}</span>
                <br />
                <span v-if="custRequest.subHead == 'Document received'">{{ custRequest.documentType }}</span>
                <span v-else>{{ custRequest.question }}</span>
                <br />
                <small>{{ custRequest.receivedOn }}</small>
              </b-card-text>
            </b-card>
          </b-col>
        </b-row>
      </template>
      <p v-else>No notifications are available</p>
    </b-container>
  </b-card>
</template>

<script>
import { BCard, BCardText, BCardTitle, BRow, BCol, BContainer } from 'bootstrap-vue'
import { mapActions, mapState } from 'vuex'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BCardText,
    BCardTitle,
    BRow,
    BCol,
    BContainer,
  },
  data() {
    return {}
  },
  computed: {
    ...mapState('customer-request', { customerRequests: 'customerRequests', replyCount: 'count' }),
  },
  mounted() {
    this.fetch()
  },
  methods: {
    ...mapActions('customer-request', { getCustomerRequests: 'get', markViewedRequest: 'markViewed' }),
    async markViewed(id) {
      try {
        const t = await this.markViewedRequest(id)
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
      await this.getCustomerRequests()
    },
  },
}
</script>
