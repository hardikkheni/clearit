<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="4" align-self="start">
            <span class="h2 align-middle mr-1">My Reminders</span>
          </b-col>
          <b-col class="text-md-right" sm="12" md="8" align-self="end">
            <label class="h5 mr-1">Filter: </label>
            <b-form-select
              v-model="filter"
              class="mb-2 mr-1 mb-sm-0 col-md-2"
              :options="filters"
              @change="paramChanges({ filter: $event })"
            />
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-table responsive :fields="columns" :items="list" thead-class="d-none">
        <template #cell(id)="{ item }">
          <b-form-checkbox v-model="completeRMIDs" inline name="cm-rm-checklist" :value="item.rem_id">
            <strong>
              <span v-if="item.is_past_due" class="text-danger">
                Past Due({{ item.dueon_format_date_y_m_d }})
                <span v-if="user.id == item.createdBy && user.id != item.assignedToUserId">({{ item.assigned_to }})</span>
              </span>
              <span v-else-if="item.is_today_due" class="text-danger">
                Today <span v-if="item.dueon_format_time != 'Anytime'">@{{ item.dueon_format_time }}</span>
                <span v-if="user.id == item.createdBy && user.id != item.assignedToUserId">({{ item.assigned_to }})</span>
              </span>
              <span v-else-if="item.is_tomorrow_due" class="text-warning">
                Tomorrow <span v-if="user.id == item.createdBy && user.id != item.assignedToUserId">({{ item.assigned_to }})</span>
              </span>
              <span v-else>
                {{ item.dueon_format_date }} <span v-if="item.dueon_format_time != 'Anytime'">@{{ item.dueon_format_time }}</span>
                <span v-if="item.id == item.createdBy && user.id != item.assignedToUserId">({{ item.assigned_to }})</span>
              </span>
            </strong>
          </b-form-checkbox>
        </template>
        <template #cell(description)="{ item }">
          <router-link v-if="item.displayTicketId" :to="{}"> #{{ item.displayTicketId }} </router-link>
          {{ item.busname }} / {{ item.firstname }} {{ item.lastname }}
          <br />
          {{ item.message }}
          <span v-if="user.id != item.createdBy && user.id == item.assignedToUserId">({{ item.created_by }})</span>
        </template>
        <template #cell(action)="{ item }">
          <b-button variant="flat-success" @click.prevent="showReminder(item)">
            <feather-icon class="float-right mr-1" icon="EditIcon" />
          </b-button>
        </template>
      </b-table>
      <b-row class="mb-2">
        <b-col md="12" class="text-right">
          <b-button size="md" variant="success" @click="completeReminders()" :disabled="completeRMIDs.length == 0">
            Update Reminders
          </b-button>
        </b-col>
      </b-row>
    </b-container>
    <b-modal ref="show-contacts" centered size="lg" title="Contacts" hide-header>
      <b-container fluid>
        <validation-observer ref="editReminder">
          <b-row class="mb-2">
            <b-col sm="12" md="4" align-self="start">
              <span class="h2 align-middle mr-1">New Reminder</span>
            </b-col>
            <b-col class="text-md-right" sm="12" md="8" align-self="end">
              <label class="h5 mr-1">Assigned to: </label>
              <b-form-select v-model="currentReminder.assignedToUserId" class="mb-2 mr-1 mb-sm-0 col-md-8" :options="listAgents" />
            </b-col>
          </b-row>
          <b-row>
            <b-col md="6">
              <b-form-group label-cols="4" label="Due on:" label-for="example-datepicker" label-align="right">
                <b-form-datepicker id="example-datepicker" v-model="currentReminder.dueon_format_date_y_m_d" />
              </b-form-group>
            </b-col>
            <b-col md="6">
              <b-form-group label-cols="4" label="at" label-for="example-timepicker" label-align="right">
                <b-form-timepicker id="example-timepicker" v-model="dueAt" hour12 no-close-button />
              </b-form-group>
            </b-col>
          </b-row>
          <b-row>
            <b-col md="12">
              <validation-provider name="message" vid="message" rules="required" #default="{ errors }">
                <b-form-textarea
                  id="textarea"
                  v-model="currentReminder.message"
                  placeholder="Enter something..."
                  rows="3"
                  max-rows="6"
                  name="message"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-col>
          </b-row>
        </validation-observer>
        Modified on <span>{{ currentReminder.modifiedon }}</span> by <span>{{ currentReminder.modified_by }}</span>
      </b-container>
      <template #modal-footer>
        <b-button size="md" variant="danger" @click="removeReminder()"> Delete </b-button>
        <b-button size="md" variant="success" @click="updateReminder()"> Update </b-button>
      </template>
    </b-modal>
  </b-card>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BContainer,
  BFormSelect,
  BTable,
  BFormCheckbox,
  BModal,
  BButton,
  BFormDatepicker,
  BFormTimepicker,
  BFormGroup,
  BFormTextarea,
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import dayjs from 'dayjs'
import { required } from '@validations'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BFormSelect,
    BTable,
    BFormCheckbox,
    BModal,
    BButton,
    BFormDatepicker,
    BFormTimepicker,
    BFormGroup,
    BFormTextarea,
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      filter: this.$route.params.filter || 'my',
      filters: [
        { value: 'my', text: 'Assigned to me' },
        { value: 'others', text: 'Assigned to other agents' },
        { value: 'all', text: 'View all' },
      ],
      currentReminder: [],
      value: '13:00:00',
      completeRMIDs: [],
      required,
    }
  },
  watch: {
    $route() {
      this.filter = this.$route.params.filter || 'my'
      this.reload()
    },
  },
  async mounted() {
    await this.reload()
  },
  computed: {
    ...mapState('reminders', { list: 'list', agents: 'agents' }),
    ...mapGetters('auth', { user: 'currentUser' }),
    ...mapGetters('reminders', { listAgents: 'listAgents' }),
    columns() {
      return [
        { key: 'id', label: 'Rem ID' },
        { key: 'description', label: 'Description' },
        { key: 'action', label: 'Action' },
      ]
    },
    dueAt: {
      get() {
        return dayjs(this.currentReminder.dueOn).format('HH:mm:ss')
      },
      set(val) {
        this.currentReminder.dueon_format_time = val
      },
    },
  },
  methods: {
    ...mapActions('reminders', {
      getReminders: 'reminders',
      editReminder: 'edit',
      deleteReminder: 'delete',
      completeRMs: 'completeReminders',
    }),
    async reload() {
      const route = this.$route
      if (!route.params.filter) {
        route.params.filter = 'my'
      }
      await this.getReminders({ ...route.params })
    },
    paramChanges(params = {}) {
      const newR = { ...this.$route.params }
      if (!newR.filter) {
        newR.filter = 'my'
      }
      this.$router.push({
        name: 'helper-reminders',
        params: {
          ...newR,
          ...params,
        },
      })
    },
    showReminder(reminder) {
      this.currentReminder = reminder
      this.$nextTick(() => {
        this.$refs['show-contacts'].show()
      })
    },
    async updateReminder() {
      this.$refs.editReminder.validate().then(async success => {
        if (success) {
          try {
            const res = await this.editReminder({ id: this.currentReminder.rem_id, data: this.currentReminder })
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: res.message,
              },
            })
            this.$refs['show-contacts'].hide()
            await this.reload()
          } catch (err) {
            // apiResponseHandler(this, err, {}, this.$refs.agentForm)
          }
        }
      })
    },
    async removeReminder() {
      try {
        const res = await this.deleteReminder(this.currentReminder.rem_id)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: res.message,
          },
        })
        this.$refs['show-contacts'].hide()
        await this.reload()
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.agentForm)
      }
    },
    async completeReminders() {
      const res = await this.completeRMs(this.completeRMIDs)
      this.$toast({
        component: ToastificationContent,
        props: {
          title: 'Success',
          icon: 'CheckIcon',
          variant: 'success',
          text: res.message,
        },
      })
      await this.reload()
    },
  },
}
</script>
