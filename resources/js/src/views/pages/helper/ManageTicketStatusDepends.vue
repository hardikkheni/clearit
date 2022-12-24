<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">Manage Ticket Status Dependencies</span>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="ticketStatusDependsForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group label="Ticket Type" label-cols-md="3" content-cols-md="6">
                <validation-provider #default="{ errors }" name="Ticket Type" vid="type" rules="required">
                  <b-form-select v-model="form.type" :options="ticketTypeOptions" @change="paramChanges({ type: $event })" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group v-if="form.type == TICKET_TYPE_CLEARANCE" label="Transport Mode" label-cols-md="3" content-cols-md="6">
                <validation-provider #default="{ errors }" name="Transport Mode" vid="transport" rules="required">
                  <b-form-select v-model="form.transport" :options="transportModeOptions" @change="paramChanges({ transport: $event })" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-skeleton-wrapper :loading="loading">
                <template #loading>
                  <b-skeleton type="input" />
                  <b-skeleton class="mt-1" animation="throb" width="85%" />
                  <b-skeleton animation="throb" width="55%" />
                  <b-skeleton animation="throb" width="70%" />
                  <b-skeleton animation="throb" width="80%" />
                  <b-skeleton animation="throb" width="60%" />
                </template>
                <b-form-group label="Ticket Status" label-cols-md="3" content-cols-md="6">
                  <validation-provider #default="{ errors }" name="Ticket Status" vid="status" rules="required">
                    <b-form-select v-model="form.status" :options="ticketStatusOptions" @change="paramChanges({ status: $event })" />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>

                <b-form-group v-if="!!(toDoTicketItemOptions || []).length">
                  <validation-provider #default="{ errors }" name="Roles" vid="toDoItemDependencyIds">
                    <b-form-checkbox-group v-model="form.toDoItemDependencyIds" stacked :options="toDoTicketItemOptions" name="roles" />
                    <small class="text-danger">{{ errors[0] }}</small>
                  </validation-provider>
                </b-form-group>
              </b-skeleton-wrapper>

              <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid || loading"> Save Changes </b-button>
              <b-button variant="outline-success" :to="{ name: 'helper-administration' }"> Cancel </b-button>
            </b-form>
          </validation-observer>
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
/* eslint-disable radix */
import {
  BCard,
  BRow,
  BCol,
  BContainer,
  BButton,
  BForm,
  BFormSelect,
  BFormGroup,
  BFormCheckboxGroup,
  BSkeletonWrapper,
  BSkeleton,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { mapActions, mapGetters, mapState } from 'vuex'

import { required } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { apiResponseHandler } from '@/libs/api.handler'
import { ticketTypeOptions, transportModeOptions, TICKET_TYPE_CLEARANCE } from '@/utils/ticket'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BButton,
    BForm,
    BFormSelect,
    BFormGroup,
    ValidationProvider,
    ValidationObserver,
    BFormCheckboxGroup,
    BSkeletonWrapper,
    BSkeleton,
  },
  data() {
    const { params } = this.$route
    const type = params.type || ticketTypeOptions[0].value
    const transport = parseInt(params.transport || transportModeOptions[0].value)
    return {
      required,
      ticketTypeOptions,
      transportModeOptions,
      form: {
        type,
        transport,
        status: parseInt(params.status || 0),
        toDoItemDependencyIds: [],
      },
      TICKET_TYPE_CLEARANCE,
    }
  },
  computed: {
    ...mapState('ticket', { loading: 'loading' }),
    ...mapGetters('ticket', { ticketStatusOptions: 'ticketStatusList', toDoTicketItemOptions: 'toDoTicketItemList' }),
  },
  watch: {
    $route() {
      const r = this.$route
      const p = r.params
      this.form = {
        type: p.type || this.ticketTypeOptions[0].value,
        transport: parseInt(p.transport || transportModeOptions[0].value),
        status: parseInt(p.status || 0),
      }

      // reload page
      this.$nextTick(() => {
        this.reload()
      })
    },
  },
  mounted() {
    if (!this.$route.params.type) {
      this.paramChanges({ type: this.ticketTypeOptions[0].value })
    } else {
      this.reload()
    }
  },
  methods: {
    ...mapActions('ticket', {
      getTicketStatusDependencies: 'getTicketStatusDependencies',
      putTicketStatusDependencies: 'putTicketStatusDependencies',
    }),
    async reload() {
      const { type, transport, status, toDoItemDependencyIds } = await this.getTicketStatusDependencies(this.form)
      this.form = {
        type,
        transport,
        status,
        toDoItemDependencyIds,
      }
    },
    paramChanges(params = {}) {
      const newR = { ...this.$route.params }
      if (!newR.type) {
        newR.type = this.ticketTypeOptions[0].value
      }

      if (!newR.transport) {
        newR.transport = this.transportModeOptions[0].value
      }

      this.$router.push({
        name: this.$route.name,
        params: {
          ...newR,
          ...params,
        },
      })
    },
    async submit() {
      try {
        const t = await this.putTicketStatusDependencies(this.form)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.message,
          },
        })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.ticketStatusDependsForm)
      }
    },
  },
}
</script>
