<template>
  <b-modal v-model="vModel" size="lg" centered hide-footer>
    <template #modal-title> <feather-icon size="50" icon="EditIcon" /><span class="h3 ml-1">New Reminder</span></template>
    <validation-observer ref="reminderForm" #default="{ invalid }">
      <b-form @submit.prevent="submit">
        <b-row>
          <b-col col>
            <b-form-group label="Assigned to:">
              <validation-provider #default="{ errors }" name="Agent" vid="assignedToUserId" rules="required">
                <b-form-select v-model="form.assignedToUserId" name="assignedToUserId" :options="agentOptions">
                  <template #first>
                    <b-form-select-option :value="null">Select Agent</b-form-select-option>
                  </template>
                </b-form-select>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12" md="6">
            <b-form-group label="Due on:">
              <validation-provider #default="{ errors }" name="Due on" vid="dueOnDate" rules="required">
                <b-input-group>
                  <flat-pickr v-model="form.dueOnDate" :config="dateConfig" />
                  <b-input-group-append is-text>
                    <feather-icon icon="CalendarIcon" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12" md="6">
            <b-form-group label="At:">
              <validation-provider #default="{ errors }" name="At" vid="dueOnTime">
                <b-input-group>
                  <flat-pickr
                    v-model="form.dueOnTime"
                    class="form-control bg-white"
                    :config="{ ...timeConfig, allowInput: true, allowInvalidPreload: true }"
                  />
                  <b-input-group-append is-text>
                    <feather-icon icon="ClockIcon" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col col>
            <b-form-group label="Message">
              <validation-provider #default="{ errors }" name="Message" vid="message" rules="required">
                <b-form-textarea v-model="form.message" name="Message" />
                <small class="text-danger">{{ errors[0] }}</small>
                <small v-if="info" class="text-primary">{{ info }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
        </b-row>
        <b-button type="submit" class="float-right" variant="primary" :disabled="invalid">Save</b-button>
        <b-button v-if="data" class="float-right mr-1" variant="danger" @click.prevent="handleDelete">Delete</b-button>
      </b-form>
    </validation-observer>
  </b-modal>
</template>

<script>
import {
  BModal,
  BForm,
  BButton,
  BFormGroup,
  BFormSelect,
  BFormSelectOption,
  BRow,
  BCol,
  BFormTextarea,
  BInputGroup,
  BInputGroupAppend,
} from 'bootstrap-vue'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import FlatPickr from 'vue-flatpickr-component'
import { mapActions } from 'vuex'
import dayjs from 'dayjs'

import { dateConfig, timeConfig } from '@/utils/config'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BModal,
    ValidationObserver,
    ValidationProvider,
    BForm,
    BButton,
    BFormGroup,
    BFormSelect,
    BFormSelectOption,
    BRow,
    BCol,
    BFormTextarea,
    FlatPickr,
    BInputGroup,
    BInputGroupAppend,
  },
  props: {
    value: {
      type: Boolean,
      default: false,
    },
    agents: {
      type: Array,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      id: null,
      data: null,
      form: { assignedToUserId: null, message: null, dueOnDate: null, dueOnTime: null },
      dateConfig,
      timeConfig,
    }
  },
  computed: {
    vModel: {
      get() {
        return this.value
      },
      set(value) {
        this.$emit('input', value)
      },
    },
    agentOptions() {
      return this.agents.map(i => ({ ...i, text: `${i.firstname} ${i.lastname}`, value: i.id }))
    },
    info() {
      if (!this.data) return ''
      return `Modified ${this.data?.dueon_format_date} by ${this.data?.assigned_to}`
    },
  },
  methods: {
    ...mapActions('reminders', { createReminder: 'create', deleteReminder: 'delete' }),
    async submit(e) {
      try {
        const res = await this.createReminder({
          ...this.form,
          ticketId: this.ticket.id,
        })
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
        e.target.reset()
        this.$emit('saved')
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.reminderForm)
      }
    },
    async handleDelete() {
      if (!this.data?.id) return
      try {
        const res = await this.deleteReminder(this.data?.id)
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
        this.$emit('deleted')
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
    setReminder(data) {
      this.data = data
      const dueOn = dayjs(data.dueOn)
      this.form.assignedToUserId = data.assignedToUserId
      this.form.dueOnDate = dueOn.format(dateConfig.dayjsFormat)
      this.form.dueOnTime = dueOn.format(timeConfig.dayjsFormat)
      this.form.message = data.message
    },
  },
}
</script>
