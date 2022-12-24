<template>
  <b-modal v-model="vModel" :title="title" size="lg" centered hide-footer>
    <validation-observer ref="customerRequest" #default="{ invalid }">
      <b-form @submit.prevent="addReq">
        <b-row>
          <b-col cols="12" md="8">
            <b-form-group :label="docLabel">
              <b-form-select v-model="form.documentTypeId" name="docUploadTypeId" :options="docUploadTypeOptions">
                <template #first>
                  <b-form-select-option :value="null">Select a document</b-form-select-option>
                </template>
              </b-form-select>
            </b-form-group>
          </b-col>
          <b-col v-if="showIncludeBlankForm" cols="12" md="4">
            <label class="mt-1">&nbsp;</label><br />
            <b-form-checkbox v-model="form.includeBlankForm">Include blank form</b-form-checkbox>
          </b-col>
        </b-row>
        <b-row>
          <b-col col>
            <b-form-group>
              <b-form-textarea v-model="form.description" rows="5" />
            </b-form-group>
          </b-col>
        </b-row>
        <div class="clearfix">
          <b-button type="submit" class="float-right" variant="primary" :disabled="invalid">Add Item</b-button>
        </div>
      </b-form>
    </validation-observer>

    <template v-if="customer_requests.length">
      <hr />
      <b-table-simple>
        <b-thead>
          <b-tr>
            <b-th>Requested documents</b-th>
            <b-th>&nbsp;</b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <b-tr v-for="(cr, i) of customer_requests" :key="i">
            <b-td>
              <div class="clearfix">
                <span v-if="cr.document_type">
                  <feather-icon class="mr-1 text-primary" icon="FileTextIcon" />
                  {{ cr.document_type }}
                </span>
                <span v-if="cr.includeBlankForm" class="float-right">
                  <feather-icon class="text-success" icon="PaperclipIcon" />
                  Form attached
                </span>
                <div v-if="cr.description" :class="{ 'pl-2': cr.document_type }">
                  <feather-icon v-if="!cr.document_type" class="text-warning mr-1" icon="InfoIcon" />
                  {{ cr.description }}
                </div>
              </div>
            </b-td>
            <b-td>
              <b-button size="sm" variant="danger" class="btn-icon" @click.prevent="handleDelete(i)">
                <feather-icon icon="Trash2Icon" />
              </b-button>
            </b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
      <b-button class="float-right" variant="primary" @click.prevent="submit">Save</b-button>
      <b-button class="float-right mr-1" variant="danger" @click.prevent="cancel">Cancel</b-button>
    </template>
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
  BFormCheckbox,
  BTableSimple,
  BThead,
  BTbody,
  BTr,
  BTd,
  BTh,
} from 'bootstrap-vue'
import { ValidationObserver } from 'vee-validate'
import { mapActions } from 'vuex'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BModal,
    ValidationObserver,
    BForm,
    BButton,
    BFormGroup,
    BFormSelect,
    BFormSelectOption,
    BRow,
    BCol,
    BFormTextarea,
    BFormCheckbox,
    BTableSimple,
    BThead,
    BTbody,
    BTr,
    BTd,
    BTh,
  },
  props: {
    value: {
      type: Boolean,
      default: false,
    },
    ticket: {
      type: Object,
      required: true,
    },
    client: {
      type: Object,
      required: true,
    },
    docUploadTypes: {
      type: Array,
      required: true,
    },
  },

  data() {
    const emptyForm = { documentTypeId: null, includeBlankForm: false, description: null }
    return {
      emptyForm,
      id: null,
      data: null,
      form: { ...emptyForm },
      customer_requests: [],
    }
  },
  computed: {
    title() {
      return `Information requested for Clearit ticket #${this.ticket.id}`
    },
    docLabel() {
      return `To: ${this.client.firstname} ${this.client.lastname} - ${this.client.email}`
    },
    vModel: {
      get() {
        return this.value
      },
      set(value) {
        this.$emit('input', value)
      },
    },
    docUploadTypeOptions() {
      return this.docUploadTypes.map(i => ({ ...i, text: i.document_type, value: i.id }))
    },
    docUploadType() {
      return this.docUploadTypeOptions.find(i => i.id === this.form.documentTypeId)
    },
    showIncludeBlankForm() {
      return !!this.docUploadType?.sampleDocumentURL
    },
  },
  watch: {
    // eslint-disable-next-line object-shorthand
    'form.documentTypeId'() {
      this.form.description = this.docUploadType?.document_description
    },
  },
  methods: {
    ...mapActions('customer-request', { bulkInsert: 'bulkInsert' }),
    addReq(e) {
      if (!this.form.documentTypeId && !this.form.description) {
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Error',
            icon: 'LockIcon',
            variant: 'danger',
            text: 'Please select document type or enter description!.',
          },
        })
        return
      }
      e.target.reset()
      this.customer_requests.push({
        documentTypeId: this.form.documentTypeId,
        description: this.form.description,
        document_type: this.docUploadType?.document_type,
        includeBlankForm: this.showIncludeBlankForm && this.form.includeBlankForm,
        sampleDocumentURL: this.showIncludeBlankForm && this.form.includeBlankForm ? this.docUploadType?.sampleDocumentURL : null,
      })
      this.form = { ...this.emptyForm }
    },
    async submit() {
      try {
        const res = await this.bulkInsert({
          ticketId: this.ticket.id,
          client_guid: this.client.guid,
          client_requests: this.customer_requests,
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
        this.vModel = false
        this.$emit('saved')
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
    async handleDelete(i) {
      this.customer_requests.splice(i, 1)
    },
    cancel() {
      this.customer_requests = []
      this.vModel = false
    },
  },
}
</script>
