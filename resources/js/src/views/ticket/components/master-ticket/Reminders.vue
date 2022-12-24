<template>
  <div class="ticket-collapse">
    <app-collapse :id="collapseId" accordion type="margin">
      <app-collapse-item
        ref="reminder"
        class="outline-secondary"
        :title="`Reminders (${reminders.length})`"
        :is-visible="isAccordianVisible"
      >
        <template #header>
          <span class="lead collapse-title">Reminders ({{ reminders.length }})</span>
          <b-button size="sm" variant="outline-success" @click.prevent="addReminer($event)">Add reminer</b-button>
        </template>
        <template>
          <span>Pending:</span>
          <div v-if="reminders.length">
            <b-list-group class="mt-1">
              <b-list-group-item v-for="(reminder, i) of reminders" :key="i">
                <div class="align-top">
                  <b-form-checkbox v-model="ids" inline class="reminder" name="reminder" :value="reminder.id">
                    <strong v-if="reminder.is_past_due" class="text-danger">Past Due {{ reminder.dueOn | dateFormat(DATE_FORMAT) }}</strong>
                    <strong v-else-if="reminder.is_today_due" class="text-danger">
                      Today
                      <template v-if="reminder.dueon_format_time != 'Anytime'"> @{{ reminder.dueon_format_time }} </template>
                    </strong>
                    <strong v-else-if="reminder.is_tomorrow_due" class="text-danger">Tomorrow</strong>
                    <strong v-else class="text-danger">
                      {{ reminder.dueon_format_date }}
                      {{ reminder.dueon_format_time != 'Anytime' ? `@${reminderdueon_format_time}` : '' }}
                    </strong>
                    -
                    <span>
                      {{ reminder.reminderDescription }}
                      <template v-if="reminder.assigned_to">({{ reminder.assigned_to }})</template>
                    </span>
                  </b-form-checkbox>
                  <div class="float-right">
                    <span>
                      Created {{ reminder.createdOn | dateFormat('MM/DD/YYYY') }} @ {{ reminder.createdOn | dateFormat('hh:mm a') }}
                    </span>
                    <b-button size="sm" variant="flat-success" class="btn-icon" @click.prevent="editReminder(reminder)">
                      <feather-icon icon="EditIcon" />
                    </b-button>
                  </div>
                </div>
              </b-list-group-item>
            </b-list-group>
            <div class="clearfix">
              <b-button class="mt-1 float-right" size="sm" variant="primary" @click.prevent="markCompleted">Update</b-button>
            </div>
          </div>
          <span v-else> <br />The list is empty </span>
        </template>
        <hr />
        <span>
          Done:
          <feather-icon
            v-b-tooltip="showDoneReminder ? `Hide` : `Show`"
            class="ml-1 text-primary"
            :icon="showDoneReminder ? `EyeOffIcon` : `EyeIcon`"
            @click="showDoneReminder = !showDoneReminder"
          />
        </span>
        <b-list-group v-if="checkedReminders.length" v-show="showDoneReminder" class="mt-1">
          <b-list-group-item v-for="(reminder, i) of checkedReminders" :key="i">
            <b-button size="sm" variant="flat-success" class="btn-icon">
              <feather-icon icon="CheckIcon" />
            </b-button>
            <strong>
              {{ reminder.dueon_format_date }}
              <template v-if="reminder.dueon_format_time != 'Anytime'"> @{{ reminder.dueon_format_time }} </template>
            </strong>
            -
            <span>{{ reminder.message }}</span>
            <span class="text-success">(computed {{ reminder.completedon_format }}, {{ reminder.completed_by_user }})</span>
          </b-list-group-item>
        </b-list-group>
        <span v-else v-show="showDoneReminder"> <br />The list is empty </span>
      </app-collapse-item>
    </app-collapse>
    <reminder-modal
      ref="reminderModal"
      v-model="showReminderModal"
      :agents="agents"
      :ticket="ticket"
      @saved="$emit('reload', collapseId)"
      @deleted="$emit('reload', collapseId)"
    />
  </div>
</template>

<script>
import { BButton, BListGroup, BListGroupItem, BFormCheckbox, VBTooltip } from 'bootstrap-vue'
import { mapActions } from 'vuex'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import ReminderModal from '@/views/ticket/components/ReminderModal.vue'

import { dateFormat } from '@/utils/filters'
import { DATE_FORMAT } from '@/utils/config'
import { apiResponseWrapper } from '@/libs/api.handler'

export default {
  components: {
    BButton,
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BFormCheckbox,
    ReminderModal,
  },
  filters: {
    dateFormat,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    reminders: {
      type: Array,
      required: true,
    },
    checkedReminders: {
      type: Array,
      required: true,
    },
    agents: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
      showDoneReminder: false,
      showReminderModal: false,
      ids: [],
      collapseId: 'reminder-accord',
    }
  },
  computed: {
    isAccordianVisible() {
      return this.$route.hash === `#${this.collapseId}`
    },
  },
  methods: {
    ...mapActions('reminders', { completeReminders: 'completeReminders' }),
    addReminer($event) {
      if (this.$refs.reminder.visible) {
        $event.stopPropagation()
      }
      this.showReminderModal = true
    },
    editReminder(reminder) {
      this.$refs.reminderModal.setReminder(reminder)
      this.showReminderModal = true
    },
    async markCompleted() {
      if (!this.ids.length) return
      apiResponseWrapper(this, async () => {
        const res = await this.completeReminders(this.ids)
        this.$emit('reload', this.collapseId)
        return res
      })
    },
  },
}
</script>
