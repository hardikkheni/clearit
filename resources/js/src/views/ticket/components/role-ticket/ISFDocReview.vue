<template>
  <div id="ticket-isf-doc-review-collapse-card" class="ticket-collapse-card" :class="className">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> ISF Document Review </b-card-title>
      <hr />
      <b-form>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Container #:" label-cols-md="4">
              <b-form-input placeholder="Container #:" class="col-md-6" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Master BOL:" label-cols-md="4">
              <b-form-input placeholder="Master BOL:" class="col-md-6" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="House BOL:" label-cols-md="4">
              <b-form-input placeholder="House BOL:" class="col-md-6" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Estimated date of departure:" label-cols-md="4">
              <flat-pickr v-model="form.departureDate" :config="dateConfig" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Estimated Time of Arrival:" label-cols-md="4">
              <flat-pickr v-model="form.eta" :config="dateConfig" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Comments:" label-cols-md="4">
              <b-form-textarea v-model="form.etacomment" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-row>
              <b-col cols="4"> </b-col>
              <b-col cols="6">
                <b-form-checkbox v-model="form.disableEtaEmails">Disable customer emails</b-form-checkbox>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
        <hr />
        <b-button class="float-right" size="sm" variant="primary">Update</b-button>
      </b-form>
    </b-card>
  </div>
</template>

<script>
/* eslint-disable no-nested-ternary */
/* eslint-disable indent */
/* eslint-disable no-else-return */
/* eslint-disable operator-linebreak */

import { BCard, BCardTitle, BForm, BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BFormCheckbox, BButton } from 'bootstrap-vue'

import FlatPickr from 'vue-flatpickr-component'
import { dateFormat } from '@/utils/filters'

export default {
  components: {
    BCard,
    BCardTitle,
    BForm,
    BCol,
    BFormGroup,
    BFormInput,
    BRow,
    FlatPickr,
    BFormTextarea,
    BFormCheckbox,
    BButton,
  },
  filters: {
    dateFormat,
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
    ticket: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dateConfig: {
        altFormat: 'm-d-Y',
        dateFormat: 'Y-m-d',
        altInput: true,
      },
      form: {
        containerNumber: this.getFullTicket.containerNumber,
        mBOL: this.getFullTicket.mBOL,
        hBOL: this.getFullTicket.hBOL,
        eta: this.getFullTicket.eta,
        departureDate: this.getFullTicket.departureDate,
        etacomment: this.ticket.etacomment,
        disableEtaEmails: !!this.ticket.disableetaemails,
      },
    }
  },
  computed: {
    className() {
      if (
        this.getFullTicket.containerNumber &&
        this.getFullTicket.mBOL &&
        this.getFullTicket.hBOL &&
        this.getFullTicket.eta &&
        this.getFullTicket.departureDate
      ) {
        return 'ticket-isf-doc-review-collapse-card-success'
      } else if (
        this.getFullTicket.containerNumber ||
        this.getFullTicket.mBOL ||
        this.getFullTicket.hBOL ||
        this.getFullTicket.eta ||
        this.getFullTicket.departureDate
      ) {
        return 'ticket-isf-doc-review-collapse-card-warning'
      }
      return 'ticket-isf-doc-review-collapse-card-danger'
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/base/bootstrap-extended/include';
@import '@core/scss/vue/libs/vue-flatpicker.scss';

#ticket-isf-doc-review-collapse-card {
  &.ticket-isf-doc-review-collapse-card-danger {
    background-color: rgba($danger, 0.3);
  }
  &.ticket-isf-doc-review-collapse-card-warning {
    background-color: rgba($yellow, 0.3);
  }
  &.ticket-isf-doc-review-collapse-card-success {
    background-color: rgba($success, 0.3);
  }
  .flatpickr-input ~ .form-control {
    background-color: $white;
  }
}
</style>
