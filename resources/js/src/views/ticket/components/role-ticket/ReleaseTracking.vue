<template>
  <div id="ticket-release-tracking-collapse-card" class="ticket-collapse-card" :class="className">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> Release Tracking </b-card-title>
      <hr />
      <b-form>
        <!-- <b-row>
          <b-col cols="12">
            <b-form-group label="Container #:" label-cols-md="4">
              <b-form-input placeholder="Container #:" class="col-md-6" />
            </b-form-group>
          </b-col>
        </b-row> -->
        <b-row>
          <b-col cols="12">
            <b-form-group label="Estimated Date of Arrival:" label-cols-md="3" content-cols-md="3">
              <flat-pickr v-model="form.eta_arrival_date" :config="dateConfig" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Transmission Date:" label-cols-md="3" content-cols-md="3">
              <flat-pickr v-model="form.transmission_date" :config="dateConfig" />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-form-group label="IT Bond Required?" label-cols-md="3">
              <b-form-radio v-model="form.it_bond_radio" name="it_bond_radio" class="d-inline-flex" value="Y">Yes</b-form-radio>
              <b-form-radio v-model="form.it_bond_radio" name="it_bond_radio" class="d-inline-flex" value="N">No</b-form-radio>
              <template v-if="form.it_bond_radio == 'Y'">
                <label class="ml-3">IT Bond #:</label>
                <b-form-input v-model="form.itBondNumber" class="d-inline-flex col-md-3 ml-2" />
                <b-form-select v-model="form.onFile" :options="onFileOptions" class="d-inline-flex col-md-3 ml-2">
                  <template #first>
                    <b-form-select-option :value="null">Not on file</b-form-select-option>
                  </template>
                </b-form-select>
              </template>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-row>
              <b-col cols="3">&nbsp;</b-col>
              <b-col cols="6">
                <b-button size="sm" variant="primary">Update</b-button>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </b-form>
    </b-card>
  </div>
</template>

<script>
/* eslint-disable no-nested-ternary */
/* eslint-disable indent */
/* eslint-disable no-else-return */
/* eslint-disable operator-linebreak */

import {
  BCard,
  BCardTitle,
  BButton,
  BForm,
  BRow,
  BCol,
  BFormGroup,
  BFormRadio,
  BFormInput,
  BFormSelect,
  BFormSelectOption,
} from 'bootstrap-vue'

import FlatPickr from 'vue-flatpickr-component'

export default {
  components: {
    BCard,
    BCardTitle,
    BButton,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    FlatPickr,
    BFormRadio,
    BFormInput,
    BFormSelect,
    BFormSelectOption,
  },
  props: {
    getFullTicket: {
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
        eta_arrival_date: null,
        transmission_date: null,
        it_bond_radio: 'Y',
        itBondNumber: null,
        onFile: null,
      },
      onFileOptions: [
        { text: 'On File', value: 1 },
        { text: 'Enroute', value: 2 },
        { text: 'Arrived', value: 3 },
      ],
    }
  },
  computed: {
    className() {
      if (this.getFullTicket.itBondRequired) {
        if (!(this.getFullTicket.itBondNumber == 'Y' && this.getFullTicket.itBondNumber && this.getFullTicket.onfile)) {
          return 'ticket-release-tracking-collapse-card-warning'
        }
        return 'ticket-release-tracking-collapse-card-success'
      }
      return 'ticket-release-tracking-collapse-card-danger'
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/base/bootstrap-extended/include';

#ticket-release-tracking-collapse-card {
  &.ticket-release-tracking-collapse-card-danger {
    background-color: rgba($danger, 0.3);
  }
  &.ticket-release-tracking-collapse-card-warning {
    background-color: rgba($yellow, 0.3);
  }
  &.ticket-release-tracking-collapse-card-success {
    background-color: rgba($success, 0.3);
  }

  .flatpickr-input ~ .form-control {
    background-color: $white;
  }
}
</style>
