<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" :title="`Reminders (${getFullTicket.openReminders})`">
        <template #header>
          <span class="lead collapse-title">Reminders ({{ getFullTicket.openReminders }})</span>
          <b-button size="sm" variant="outline-success" @click.prevent="addReminer">Add reminer</b-button>
        </template>
        <template v-if="(getFullTicket.reminder_items_data || []).length">
          <b-list-group>
            <b-list-group-item v-for="(reminder, i) of getFullTicket.reminder_items_data" :key="i">
              <div class="align-top">
                <b-form-checkbox inline class="reminder" name="reminder" :value="reminder.reminderId">
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
                  <b-button size="sm" variant="flat-success" class="btn-icon">
                    <feather-icon icon="EditIcon" />
                  </b-button>
                </div>
              </div>
            </b-list-group-item>
          </b-list-group>
          <hr />
        </template>
        <b-list-group>
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
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BButton, BListGroup, BListGroupItem, BFormCheckbox } from 'bootstrap-vue'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { dateFormat } from '@/utils/filters'
import { DATE_FORMAT } from '@/utils/config'

export default {
  components: {
    BButton,
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BFormCheckbox,
  },
  filters: {
    dateFormat,
  },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
    checkedReminders: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
    }
  },
  methods: {
    addReminer(e) {
      e.stopPropagation()
    },
  },
}
</script>
