<template>
  <div class="ticket-collapse">
    <app-collapse accordion type="margin">
      <app-collapse-item class="outline-secondary" title="PGA Documents">
        <template #header>
          <span class="lead collapse-title">PGA Documents</span>
        </template>
        <b-list-group>
          <b-list-group-item>
            <validation-observer ref="pgaForm" #default="{ invalid }">
              <b-form @submit.prevent="submit">
                <b-row>
                  <b-col sm="12" md="6">
                    <b-form-group label="Requested Documents">
                      <validation-provider #default="{ errors }" name="Requested Document" vid="pgaId" rules="required">
                        <b-form-select v-model="form.pgaId" :options="computedPgaDocOptions" />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="4">
                    <b-form-group label="Description">
                      <validation-provider #default="{ errors }" name="Description" vid="note">
                        <b-form-input v-model="form.note" />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="2">
                    <div class="d-none d-md-block"><label>&nbsp;</label><br /></div>
                    <b-button type="submit" variant="primary" class="btn-sm-block" :disabled="invalid">Submit</b-button>
                  </b-col>
                </b-row>
              </b-form>
            </validation-observer>
          </b-list-group-item>
          <b-list-group-item>
            <b-table-simple small outlined hover responsive>
              <b-thead>
                <b-th>Document name</b-th>
                <b-th>Requested On</b-th>
                <b-th>Status</b-th>
                <b-th>&nbsp;</b-th>
              </b-thead>
              <b-tbody>
                <template v-if="pgaReqs.length">
                  <b-tr v-for="(req, i) of pgaReqs" :key="i">
                    <b-td> {{ req.document_name }} </b-td>
                    <b-td>
                      <template v-if="req.createdOn">{{ req.createdOn | dateFormat(DATE_FORMAT) }}</template>
                    </b-td>
                    <b-td>
                      {{ req.status }}
                      <template v-if="req.status == 'Complete' && req.signDate">
                        {{ req.signDate | dateFormat(DATE_FORMAT) }}
                      </template>
                    </b-td>
                    <b-td>
                      <template v-if="req.status == 'Complete'"><b-button variant="flat-primary" size="sm">View</b-button> | </template>
                      <b-button variant="flat-danger" size="sm" @click.prevent="deleteClickHandler(req.id)">Delete</b-button>
                    </b-td>
                  </b-tr>
                </template>
                <b-tr v-else>
                  <b-td colspan="4" class="text-center"> No documents requested </b-td>
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
  BButton,
  BListGroup,
  BListGroupItem,
  BFormGroup,
  BRow,
  BCol,
  BFormSelect,
  BFormInput,
  BTableSimple,
  BThead,
  BTh,
  BTr,
  BTd,
  BTbody,
  BForm,
} from 'bootstrap-vue'
import { mapActions } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'
import { dateFormat } from '@/utils/filters'
import { DATE_FORMAT } from '@/utils/config'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    AppCollapse,
    AppCollapseItem,
    BButton,
    BListGroup,
    BListGroupItem,
    BFormGroup,
    BRow,
    BCol,
    BFormSelect,
    BFormInput,
    BTableSimple,
    BThead,
    BTh,
    BTr,
    BTbody,
    BTd,
    ValidationProvider,
    ValidationObserver,
    BForm,
  },
  filters: {
    dateFormat,
  },
  props: {
    pgaDocs: {
      type: Array,
      required: true,
    },
    pgaReqs: {
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
      DATE_FORMAT,
      form: {
        pgaId: null,
        note: null,
      },
    }
  },
  computed: {
    computedPgaDocOptions() {
      return this.pgaDocs.map(i => ({ ...i, value: i.id, text: i.name }))
    },
  },
  mounted() {},
  methods: {
    ...mapActions('ticket', { addPgaRequest: 'addPgaRequest' }),
    ...mapActions('pga-request', { deletePgaRequest: 'delete' }),
    async submit() {
      try {
        const res = await this.addPgaRequest({ id: this.ticket.id, data: this.form })
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
        apiResponseHandler(this, err, {}, this.$refs.pgaForm)
      }
    },
    deleteClickHandler(id) {
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
            const res = await this.deletePgaRequest(id)
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
  },
}
</script>

<style lang="scss" scoped>
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
