<template>
  <div id="ticket-isf-filling-collapse-card" class="ticket-collapse-card" :class="className">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> ISF Filling </b-card-title>
      <hr />
      <b-form>
        <b-row class="mt-1" align-v="center">
          <b-col cols="2">
            <label> ISF Status: </label>
          </b-col>
          <b-col cols="2">
            <b-form-checkbox v-model="form.isfFiled" class="ml-1">ISF filed</b-form-checkbox>
          </b-col>
          <b-col cols="3">
            <b-input-group class="ml-1">
              <flat-pickr v-model="form.isfFiledOn" :config="dateTimeConfig" />
              <b-input-group-append is-text>
                <feather-icon icon="CalendarIcon" />
              </b-input-group-append>
            </b-input-group>
          </b-col>
        </b-row>
        <b-row class="mt-1" align-v="center">
          <b-col cols="2"> &nbsp; </b-col>
          <b-col cols="2">
            <b-form-checkbox v-model="form.isfBillMatch" class="ml-1">Bill Match</b-form-checkbox>
          </b-col>
          <template v-if="!form.isfBillMatch">
            <b-col cols="2"> Resubmit On: </b-col>
            <b-col cols="3">
              <b-input-group class="ml-1">
                <flat-pickr v-model="form.isfFiledOn" :config="dateConfig" />
                <b-input-group-append is-text>
                  <feather-icon icon="CalendarIcon" />
                </b-input-group-append>
              </b-input-group>
            </b-col>
            <b-col cols="2">
              <feather-icon class="text-danger" icon="AlertTriangleIcon" />
              Warning: No Bill Match
            </b-col>
          </template>
          <template v-else>
            <b-col cols="3">
              <b-form-file ref="fileInput" v-model="file" type="hidden" plain />
              <b-button size="sm" variant="flat-success" class="btn-icon" @click.prevent="triggerFileInput">
                <feather-icon icon="UploadIcon" />
              </b-button>
              {{
                file
                  ? file.name
                  : getLatestDoc && getLatestDoc.file
                  ? `${getLatestDoc.file} (${dayjs(getLatestDoc.createdOn).format('MM/DD/YYYY')})`
                  : 'Upload ISF Certification'
              }}
            </b-col>
          </template>
        </b-row>
        <hr />
        <b-button size="sm" class="float-right" variant="primary" @click.prevent>Save</b-button>
      </b-form>
    </b-card>
  </div>
</template>

<script>
/* eslint-disable no-else-return */
/* eslint-disable operator-linebreak */
import { BCard, BCardTitle, BRow, BCol, BForm, BFormCheckbox, BInputGroup, BInputGroupAppend, BButton, BFormFile } from 'bootstrap-vue'
import FlatPickr from 'vue-flatpickr-component'

import dayjs from 'dayjs'

export default {
  components: {
    BCard,
    BCardTitle,
    BRow,
    BCol,
    BForm,
    BFormCheckbox,
    FlatPickr,
    BInputGroup,
    BInputGroupAppend,
    BButton,
    BFormFile,
  },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
    role: {
      type: Object,
      required: true,
    },
    getLatestDoc: {
      type: Object,
      required: true,
    },
    currentRole: {
      type: Object,
      required: true,
    },
  },
  data() {
    const isfFiled = this.getFullTicket.isffiled == 1
    const isfBillMatch = this.getFullTicket.isfBillMatch == 1
    return {
      form: {
        guid: this.getFullTicket.ticketGUID,
        role: this.currentRole.internalKey,
        document_desc: 'ISF Certificate',
        isffiled_hidden: isfFiled,
        isffiled: isfFiled,
        isfBillMatch,
        isfBillMatch_hidden: isfBillMatch,
        isfFiledOn: null,
      },
      dateConfig: {
        altFormat: 'm-d-Y',
        dateFormat: 'Y-m-d',
        altInput: true,
      },
      dateTimeConfig: {
        enableTime: true,
        altFormat: 'm-d-Y H:i',
        dateFormat: 'Y-m-d H:i:00',
        altInput: true,
      },
      file: null,
      dayjs,
    }
  },
  computed: {
    className() {
      if (this.getFullTicket.isffiled > 0 && !this.getFullTicket.isfFiledOn && this.getFullTicket.isfBillMatch > 0 && this.getLatestDoc) {
        return 'ticket-isf-filling-collapse-card-success'
      } else if ((this.getFullTicket.isffiled > 0 && !this.getLatestDoc) || (this.getLatestDoc && this.getFullTicket.isfBillMatch > 0)) {
        return 'ticket-isf-filling-collapse-card-warning'
      }
      return 'ticket-isf-filling-collapse-card-danger'
    },
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.$el.click()
    },
  },
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';

#ticket-isf-filling-collapse-card {
  &.ticket-isf-filling-collapse-card-danger {
    background-color: rgba($danger, 0.3);
  }
  &.ticket-isf-filling-collapse-card-warning {
    background-color: rgba($yellow, 0.3);
  }
  &.ticket-isf-filling-collapse-card-success {
    background-color: rgba($success, 0.3);
  }
  .flatpickr-input ~ .form-control {
    background-color: $white;
  }

  .form-control-file {
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    z-index: -1;
  }
}
</style>
