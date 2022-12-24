<template>
  <div class="ticket-collapse upload-documents-card">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Upload Documents">
        <template #header>
          <span class="lead collapse-title">
            Upload Documents <span v-if="missingDocCountText" class="text-danger">{{ missingDocCountText }}</span>
          </span>
        </template>
        <b-list-group>
          <b-list-group-item>
            <b-row v-if="missingDocs.length" class="mb-1">
              <b-col col>
                <span class="text-danger">Missing:</span>
                <b-badge v-for="(mDoc, i) of missingDocs" :key="i" variant="primary" class="missing-docs">{{ mDoc }}</b-badge>
              </b-col>
            </b-row>
            <validation-observer ref="udForm" #default="{ invalid }">
              <b-form @submit.prevent="submit">
                <b-row>
                  <b-col sm="12" md="4">
                    <b-form-group label="Document Type:">
                      <validation-provider #default="{ errors }" name="Document Type" vid="documentUploadTypeId">
                        <b-form-select v-model="form.documentUploadTypeId" :options="computedDocTypeOptions">
                          <template #first>
                            <b-form-select-option :value="null">Select a doc type</b-form-select-option>
                          </template>
                        </b-form-select>
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="4">
                    <b-form-group label="Description:"> <b-form-input v-model="form.file_description" /></b-form-group>
                  </b-col>
                  <b-col sm="12" md="4">
                    <validation-provider #default="{ errors }" name="Upload Document" vid="document" rules="required">
                      <b-form-file ref="fileInput" v-model="form.document" type="hidden" plain />
                      <span class="d-none d-md-block"><label>&nbsp;</label><br /></span>
                      <b-button variant="primary" class="btn-sm-block mb-1 mb-md-0" @click.prevent="triggerFileInput">Browse</b-button>
                      <b-button type="submit" variant="outline-primary" :disabled="invalid" class="btn-sm-block">Upload</b-button>
                      <br />
                      <span class="text-primary">{{ fileName }}</span>
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-form>
            </validation-observer>
          </b-list-group-item>
          <b-list-group-item>
            <span>UPLOADED DOCUMENTS</span>
            <b-table-simple class="mt-1" small>
              <b-thead>
                <b-tr>
                  <b-th>Document type:</b-th>
                  <b-th>Description:</b-th>
                  <b-th>&nbsp;</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <template v-if="docs.length">
                  <b-tr v-for="(tr, i) of docs" :key="i">
                    <b-td>
                      <span v-if="!isInEditMode(tr.id)">
                        {{ tr.description }}
                      </span>
                      <template v-else>
                        <label>&nbsp;</label><br />
                        <b-form-group>
                          <b-form-select v-model="tr.documentUploadTypeId" :options="computedDocTypeOptions">
                            <template #first>
                              <b-form-select-option :value="null">Select a doc type</b-form-select-option>
                            </template>
                          </b-form-select>
                        </b-form-group>
                      </template>
                    </b-td>
                    <b-td>
                      <!-- {{ tr }} -->
                      {{ tr.file_description }}
                      <template v-if="tr.description == DOCUMENT_TYPE_COMMERCIAL_INVOICE">
                        <br />
                        <strong>Invoice #:</strong> {{ tr.invoiceNumber }} <br />
                        <strong>Supplier name:</strong> {{ tr.supplierName }}
                      </template>
                    </b-td>
                    <b-td>
                      <template v-if="!isInEditMode(tr.id)">
                        <b-button size="sm" variant="flat-primary">View</b-button> |
                        <b-button size="sm" variant="flat-primary" @click.prevent="pushInEditMode(tr.id)">Edit</b-button> |
                        <b-button size="sm" variant="flat-danger" @click.prevent="removeUploadDoc(tr.id)"> Delete </b-button>
                      </template>
                      <template v-else>
                        <b-button size="sm" variant="flat-primary" @click.prevent="save(tr)">Save</b-button> |
                        <b-button size="sm" variant="flat-primary" @click.prevent="removeFromEditMode(tr.id)">Cancel</b-button>
                      </template>
                    </b-td>
                  </b-tr>
                </template>
                <b-tr v-else>
                  <b-td colspan="3" class="text-center"> No documents uploaded </b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
  </div>
</template>

<script>
import {
  BListGroup,
  BListGroupItem,
  BBadge,
  BRow,
  BCol,
  BFormInput,
  BFormGroup,
  BFormSelect,
  BFormSelectOption,
  BButton,
  BFormFile,
  BTableSimple,
  BThead,
  BTbody,
  BTh,
  BTr,
  BTd,
  BForm,
} from 'bootstrap-vue'
import { mapActions } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import { DOCUMENT_TYPE_COMMERCIAL_INVOICE } from '@/utils/documentType'
import { apiResponseHandler } from '@/libs/api.handler'
import showSweatAlert, { DELETE_PRESET } from '@/utils/sweatAlert'

import editModeMixin from '@/mixins/editMode.mixin'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BBadge,
    BRow,
    BCol,
    BFormInput,
    BFormGroup,
    BFormSelect,
    BFormSelectOption,
    BButton,
    BFormFile,
    BTableSimple,
    BThead,
    BTbody,
    BTh,
    BTr,
    BTd,
    BForm,
    ValidationProvider,
    ValidationObserver,
  },
  filters: {},
  mixins: [editModeMixin()],
  props: {
    docs: {
      type: Array,
      required: true,
    },
    missingDocs: {
      type: Array,
      required: true,
    },
    docUploadTypes: {
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
      DOCUMENT_TYPE_COMMERCIAL_INVOICE,
      form: {
        ticketId: this.ticket.id,
        documentUploadTypeId: null,
        file_description: null,
        document: null,
      },
    }
  },
  computed: {
    missingDocCountText() {
      if (this.missingDocs.length) {
        return `( ${this.missingDocs.length} missing)`
      }
      return ''
    },
    computedDocTypeOptions() {
      return this.docUploadTypes.map(i => ({ ...i, text: i.document_type, value: i.id }))
    },
    fileName() {
      return this.form.document ? this.form.document.name : ''
    },
  },
  mounted() {},
  methods: {
    ...mapActions('ticket-document', {
      deleteTicketDocument: 'delete',
      updateDocUploadType: 'updateDocUploadType',
      createTicketDocument: 'create',
    }),
    triggerFileInput() {
      this.$refs.fileInput.$el.click()
    },
    async removeUploadDoc(id) {
      showSweatAlert(
        this,
        async result => {
          if (result.isConfirmed) {
            try {
              const res = await this.deleteTicketDocument(id)
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
              this.$emit('removed')
            } catch (err) {
              apiResponseHandler(this, err, {})
            }
          }
        },
        DELETE_PRESET,
      )
    },
    async save(item) {
      try {
        const res = await this.updateDocUploadType({ id: item.id, data: { documentUploadTypeId: item.documentUploadTypeId } })
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
        this.removeFromEditMode(item.id)
        this.$emit('saved')
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
    async submit() {
      try {
        const res = await this.createTicketDocument(this.form)
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
    },
  },
}
</script>

<style lang="scss" scoped>
.missing-docs {
  margin-right: 0.1rem;
}
.upload-documents-card ::v-deep {
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
