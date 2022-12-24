<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" :title="title">
        <template #header>
          <span class="lead collapse-title" v-html="title" />
        </template>
        <validation-observer ref="billingForm" #default="{ invalid }">
          <b-form @submit.prevent="submit">
            <b-list-group class="mt-1">
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">Invoice Number:</b-col>
                  <b-col col>
                    <validation-provider #default="{ errors }" name="Invoice Number" vid="transactionNumber">
                      <b-form-input v-model="form.transactionNumber" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-list-group-item>
              <b-list-group-item>
                <b-row>
                  <b-col sm="12" md="3">
                    <b-form-group label="Amount to charge:">
                      <b-form-input v-model="form.amount" />
                    </b-form-group>
                    <b-form-checkbox v-model="form.doNotCharge">Do not charge card on file</b-form-checkbox>
                  </b-col>
                  <b-col sm="12" md="3">
                    <b-form-group label="Description">
                      <b-form-input v-model="form.specialNotes" />
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="3">
                    <label>&nbsp;</label><br />
                    <b-form-file ref="fileInput" v-model="form.invoice" type="hidden" plain />
                    <b-button size="sm" variant="outline-primary" @click.prevent="triggerFileInput">Browse</b-button>
                    <br />
                    <span>{{ fileName }}</span>
                  </b-col>
                  <b-col col>
                    <label>&nbsp;</label><br />
                    <b-button type="submit" size="sm" variant="primary" :disabled="invalid">Add Charge</b-button>
                  </b-col>
                </b-row>
              </b-list-group-item>
            </b-list-group>
          </b-form>
        </validation-observer>
        <hr />
        <b-list-group>
          <b-list-group-item v-for="(payment, i) of info.paymentAmounts" :key="i">
            <b-row>
              <b-col cols>
                <strong>{{ payment | currencyFormat }}</strong>
              </b-col>
              <b-col cols>{{ info.paymentItems[i] }}</b-col>
              <b-col cols>
                <template v-if="info.paymentFiles[i]">
                  <b-button size="sm" variant="flat-info">Download</b-button> | <b-button size="sm" variant="flat-info">View</b-button>
                </template>
                <span v-else>N/A</span>
                <b-button
                  v-if="info.paymentStatuses[i] == '2'"
                  size="sm"
                  variant="flat-danger"
                  class="btn-icon"
                  @click.prevent="removePaymentHandler(i + 1)"
                >
                  <feather-icon icon="Trash2Icon" />
                </b-button>
              </b-col>
              <b-col cols>
                <template v-if="info.paymentStatuses[i] != '2'">
                  <b-button size="sm" variant="flat-danger" class="btn-icon"><feather-icon icon="ClockIcon" /> </b-button> Unpaid
                  <b-button size="sm" variant="flat-info" @click.prevent="markPaidHandler(i + 1)">(Mark Paid)</b-button>
                </template>
                <template v-else>
                  <span v-if="payment < 0"> Refund </span>
                  <template v-else>
                    <template v-if="info.paymentStatuses[i] == '2'">
                      <feather-icon icon="DollarSignIcon" class="text-success" />
                      Paid
                    </template>
                    <template v-else>
                      <b-button size="sm" variant="flat-danger" class="btn-icon"><feather-icon icon="ClockIcon" /> </b-button> Unpaid
                      <b-button size="sm" variant="flat-info" @click.prevent="markPaidHandler(i + 1)">(Mark Paid)</b-button>
                    </template>
                  </template>
                </template>
              </b-col>
            </b-row>
          </b-list-group-item>
          <b-list-group-item>
            Total: <strong>{{ info.total | currencyFormat }}</strong>
          </b-list-group-item>
          <hts-bond :count-charge="countCharge" :client="client" :wrapper="'b-list-group-item'" />
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import { BListGroup, BListGroupItem, BRow, BCol, BFormInput, BFormGroup, BFormCheckbox, BButton, BFormFile, BForm } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { mapActions } from 'vuex'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import HtsBond from '@/views/ticket/components/HtsBond.vue'

import { countTicketAccountMoney } from '@/utils/ticket'
import { currencyFormat } from '@/utils/filters'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BRow,
    BCol,
    BFormInput,
    BFormGroup,
    BFormCheckbox,
    BButton,
    HtsBond,
    BFormFile,
    ValidationProvider,
    ValidationObserver,
    BForm,
  },
  filters: {
    currencyFormat,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    client: {
      type: Object,
      required: true,
    },
    countCharge: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      form: {
        amount: null,
        transactionNumber: null,
        specialNotes: null,
        invoice: null,
        doNotCharge: false,
      },
    }
  },
  computed: {
    title() {
      let title = 'Billing'
      const ticketAmount = countTicketAccountMoney(this.ticket, false, 0)
      // eslint-disable-next-line eqeqeq
      if (this.ticket.amount && (this.ticket.isPaid != '1' || this.ticket.isPaidAdditional == '1' || ticketAmount > 0)) {
        title += `<span class="text-danger text-xs"> $${ticketAmount.toLocaleString('en-US')} due</span>`
      }
      return title
    },
    fileName() {
      return this.form.invoice ? this.form.invoice.name : 'No file selected.'
    },
    info() {
      const delim = ';'
      let paymentAmounts = []
      let paymentItems = []
      let paymentStatuses = []
      const paymentFiles = (this.ticket.paymentFile || null)?.split(delim) || []
      if (this.ticket.paymentAmount) {
        paymentAmounts = this.ticket.paymentAmount.split(delim).map(i => parseFloat(i || 0))
      }
      if (this.ticket.paymentItem) {
        paymentItems = this.ticket.paymentItem.split(delim)
      }
      if (this.ticket.paymentStatus) {
        paymentStatuses = this.ticket.paymentStatus.split(delim)
      }
      if (this.ticket.amount) {
        paymentAmounts.unshift(parseFloat(this.ticket.amount || 0))
        paymentItems.unshift(this.ticket.specialNotes)
        // eslint-disable-next-line eqeqeq
        if (this.ticket.isPaid == '1') {
          paymentStatuses.unshift(2)
        } else {
          paymentStatuses.unshift(1)
        }
        paymentFiles.unshift(this.ticket.fl_in)
      }
      return { paymentAmounts, paymentItems, paymentStatuses, paymentFiles, total: paymentAmounts.reduce((accu, i) => accu + i, 0) }
    },
  },
  mounted() {},
  methods: {
    ...mapActions('ticket', { addBilling: 'addBilling', markAsPaid: 'markAsPaid', removePayment: 'removePayment' }),
    newRequest(e) {
      e.stopPropagation()
    },
    triggerFileInput() {
      this.$refs.fileInput.$el.click()
    },
    markPaidHandler(i) {
      this.$swal({
        title: 'Are you sure?',
        text: 'You want to mark as paid this!',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as paid!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
      }).then(async result => {
        if (result.isConfirmed) {
          try {
            const res = await this.markAsPaid({ id: this.ticket.id, payId: i })
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
            this.$emit('saved')
          } catch (err) {
            apiResponseHandler(this, err, {})
          }
        }
      })
    },
    removePaymentHandler(i) {
      this.$swal({
        title: 'Are you sure?',
        text: 'You want to remove this!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        customClass: {
          confirmButton: 'btn btn-outline-danger',
          cancelButton: 'btn btn-primary ml-1',
        },
        buttonsStyling: false,
      }).then(async result => {
        if (result.isConfirmed) {
          try {
            const res = await this.removePayment({ id: this.ticket.id, payId: i })
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
            this.$emit('saved')
          } catch (err) {
            apiResponseHandler(this, err, {})
          }
        }
      })
    },
    async submit() {
      try {
        const res = await this.addBilling({ id: this.ticket.id, data: this.form })
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
        this.$emit('saved')
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.billingForm)
      }
    },
  },
}
</script>

 <style lang="scss" scoped>
.ticket-collapse ::v-deep {
  .form-control-file {
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    z-index: -1;
  }
}
</style>

<style lang="scss" scoped>
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
