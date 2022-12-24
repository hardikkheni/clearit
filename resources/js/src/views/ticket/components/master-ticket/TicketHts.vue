<template>
  <div class="ticket-collapse">
    <app-collapse :id="collapseId" accordion type="margin">
      <app-collapse-item class="outline-secondary" title="Commodities" :is-visible="isAccordianVisible">
        <template #header>
          <span class="lead collapse-title">Commodities</span>
        </template>
        <b-list-group>
          <b-list-group-item>
            <validation-observer ref="ticketHtsForm" #default="{ invalid }">
              <b-form @submit.prevent="submit">
                <b-row>
                  <b-col sm="12" md="4">
                    <b-form-group>
                      <validation-provider #default="{ errors }" name="HTS Code" vid="code">
                        <label>HTS Code <span class="text-danger">*</span></label>
                        <b-input-group>
                          <b-form-input v-model="form.code" />
                          <template #append>
                            <b-button v-tooltip:title="`Search`" v-b-modal.user-hts-search-modal variant="primary" class="btn-icon">
                              <feather-icon icon="SearchIcon" />
                            </b-button>
                          </template>
                        </b-input-group>
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="4">
                    <b-form-group label="Agent Description">
                      <validation-provider #default="{ errors }" name="Agent Description" vid="description">
                        <b-form-input v-model="form.description" />
                        <small class="text-danger">{{ errors[0] }}</small>
                      </validation-provider>
                    </b-form-group>
                  </b-col>
                  <b-col sm="12" md="4">
                    <span class="d-none d-md-block"><label>&nbsp;</label><br /></span>
                    <b-button type="submit" variant="primary" class="btn-sm-block" :disabled="invalid">
                      <feather-icon icon="PlusIcon" />Add
                    </b-button>
                  </b-col>
                </b-row>
              </b-form>
            </validation-observer>
          </b-list-group-item>
          <b-list-group-item>
            <span class="text-success d-block">Commodities for this Shipment </span>
            <template v-if="commodities.length">
              <b-table-simple class="mt-1" small>
                <b-thead>
                  <b-tr>
                    <b-th>HTS Code</b-th>
                    <b-th>Description</b-th>
                    <b-th>&nbsp;</b-th>
                  </b-tr>
                </b-thead>
                <b-tbody>
                  <b-tr v-for="(comm, i) of commodities" :key="i">
                    <b-td>
                      {{ comm.code }}<sup v-if="comm.USTR301 == 'Y'" class="text-danger">*</sup>
                      <div v-if="comm.BasicDutyRateString">{{ comm.BasicDutyRateString }}</div>
                    </b-td>
                    <b-td>
                      <template v-if="!isInEditMode(comm.tuhId)">
                        <div v-if="comm.description">{{ comm.description }}</div>
                        <div v-if="comm.mergedDescription">{{ comm.mergedDescription }}</div>
                      </template>
                      <template v-else>
                        <b-form-group>
                          <b-form-input v-model="comm.description" />
                        </b-form-group>
                      </template>
                    </b-td>
                    <b-td>
                      <span class="d-flex">
                        <template v-if="!isInEditMode(comm.tuhId)">
                          <b-button size="sm" variant="flat-success" @click.prevent="pushInEditMode(comm.tuhId)">Edit</b-button>|
                          <b-button size="sm" variant="flat-danger" @click.prevent="remove(comm.tuhId)">Delete</b-button>
                        </template>
                        <template v-else>
                          <b-button size="sm" variant="flat-success" @click.prevent="save(comm)">Save</b-button>|
                          <b-button size="sm" variant="flat-danger" @click.prevent="removeFromEditMode(comm.tuhId)">Cancel</b-button>
                        </template>
                      </span>
                    </b-td>
                  </b-tr>
                </b-tbody>
              </b-table-simple>

              <div class="clearfix mt-1">
                <div class="float-left">
                  <small> <sup class="text-danger">*</sup> Indicates that this commodity may be subject to additional duty </small>
                  <br />
                  <small v-if="ticket.tariffCodeEmailSent">
                    Confirmation email sent to user on {{ ticket.tariffCodeEmailSent | dateFormat(DATE_FORMAT, '/') }} @
                    {{ ticket.tariffCodeEmailSent | dateFormat('hh:mm A') }}
                  </small>
                </div>
                <div v-if="!ticket.tariffCodeEmailSent" class="float-right">
                  <b-button size="sm" variant="primary" @click.prevent="notifyUser">Notify User</b-button>
                </div>
              </div>
            </template>
            <span v-else class="mt-1"> No tariff codes have yet been associated with this ticket. </span>
          </b-list-group-item>
          <b-list-group-item>
            <span class="text-success d-block">Previously used codes </span>
            <b-table-simple v-if="htsCodes.length" class="mt-1" small>
              <b-thead>
                <b-tr>
                  <b-th>HTS Code</b-th>
                  <b-th>Product Description</b-th>
                  <b-th>&nbsp;</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <b-tr v-for="(hts, i) of htsCodes" :key="i">
                  <b-td>
                    {{ hts.code }}
                  </b-td>
                  <b-td>
                    {{ hts.description }}
                  </b-td>
                  <b-td>
                    <b-button v-if="!commodityIds.includes(hts.id)" size="sm" variant="flat-primary" @click.prevent="addToTicket(hts.id)">
                      Add to ticket
                    </b-button>
                  </b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
            <span v-else class="mt-1"> No tariff codes have yet been associated with this ticket. </span>
          </b-list-group-item>
        </b-list-group>
      </app-collapse-item>
    </app-collapse>
    <b-modal id="user-hts-search-modal" size="lg" hide-footer>
      <b-form>
        <b-row>
          <b-col cols="12" md="6">
            <b-form-group label="HTS code">
              <b-form-input />
            </b-form-group>
          </b-col>
          <b-col cols="12" md="6">
            <b-form-group label="Agent Description">
              <b-form-input />
            </b-form-group>
          </b-col>
        </b-row>
      </b-form>
      <b-row>
        <b-col col>
          <b-table-simple class="mt-1">
            <b-thead>
              <b-tr>
                <b-th>HTS NUMBER</b-th>
                <b-th>PRODUCT DESCRIPTION</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <tr>
                <td colspan="2" class="text-center">No product selected, please select an HTS number to continue.</td>
              </tr>
            </b-tbody>
          </b-table-simple>
        </b-col>
      </b-row>
    </b-modal>
  </div>
</template>

<script>
import {
  BButton,
  BListGroup,
  BListGroupItem,
  BRow,
  BCol,
  BFormInput,
  BFormGroup,
  BInputGroup,
  VBTooltip,
  BTableSimple,
  BThead,
  BTbody,
  BTh,
  BTr,
  BTd,
  BForm,
  BModal,
  VBModal,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import AppCollapse from '@core/components/app-collapse/AppCollapse.vue'
import AppCollapseItem from '@core/components/app-collapse/AppCollapseItem.vue'

import { dateFormat } from '@/utils/filters'
import { DATE_FORMAT } from '@/utils/config'

import { apiResponseWrapper } from '@/libs/api.handler'
import showSweatAlert, { DELETE_PRESET } from '@/utils/sweatAlert'

import editModeMixin from '@/mixins/editMode.mixin'
import { mapActions } from 'vuex'

export default {
  components: {
    BButton,
    AppCollapse,
    AppCollapseItem,
    BListGroup,
    BListGroupItem,
    BFormInput,
    BRow,
    BCol,
    BFormGroup,
    BInputGroup,
    BTableSimple,
    BThead,
    BTbody,
    BTh,
    BTr,
    BTd,
    ValidationProvider,
    ValidationObserver,
    BForm,
    BModal,
  },
  filters: { dateFormat },
  directives: {
    tooltip: VBTooltip,
    'b-modal': VBModal,
  },
  mixins: [editModeMixin()],
  props: {
    commodities: {
      type: Array,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
    client: {
      type: Object,
      required: true,
    },
    htsCodes: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
      form: {
        code: '',
        description: '',
        sku: null,
      },
      collapseId: 'ticket-hts-accord',
    }
  },
  computed: {
    commodityIds() {
      return this.commodities.map(i => i.tuhId)
    },
    isAccordianVisible() {
      return this.$route.hash === `#${this.collapseId}`
    },
  },
  mounted() {},
  methods: {
    ...mapActions('ticket-user-hts', { createTicketUserHts: 'create', deleteTicketUserHts: 'delete', updateTicketUserHts: 'update' }),
    ...mapActions('ticket', {
      attachUserHts: 'attachUserHts',
      notifyTariffCodeEmail: 'notifyTariffCodeEmail',
      updateNotifyTariffCode: 'updateNotifyTariffCode',
    }),
    submit() {
      apiResponseWrapper(
        this,
        async () => {
          const res = await this.createTicketUserHts({
            ...this.form,
            ticketId: this.ticket.id,
            guid: this.client.guid,
          })
          this.$emit('reload', this.collapseId)
          this.updateTariffCode()
          return res
        },
        this.$refs.ticketHtsForm,
      )
    },
    save(comm) {
      apiResponseWrapper(this, async () => {
        const res = await this.updateTicketUserHts({
          id: comm.tuhId,
          data: {
            guid: this.client.guid,
            ticketId: this.ticket.id,
            uhtsId: comm.id,
            code: comm.code,
            description: comm.description,
            sku: null,
            role: null,
          },
        })
        this.$emit('reload', this.collapseId)
        this.removeFromEditMode(comm.tuhId)
        return res
      })
    },
    remove(id) {
      showSweatAlert(
        this,
        async result => {
          if (result.isConfirmed) {
            await apiResponseWrapper(this, async () => {
              const res = await this.deleteTicketUserHts(id)
              this.$emit('reload', this.collapseId)
              this.updateTariffCode()
              return res
            })
          }
        },
        DELETE_PRESET,
      )
    },
    addToTicket(uhtsId) {
      apiResponseWrapper(this, async () => {
        const res = await this.attachUserHts({
          ticketId: this.ticket.id,
          uhtsId,
        })
        this.$emit('reload', this.collapseId)
        this.updateTariffCode()
        return res
      })
    },
    notifyUser() {
      apiResponseWrapper(this, async () => {
        const res = await this.notifyTariffCodeEmail({ id: this.ticket.id, data: { guid: this.client.guid, role: null } })
        this.$emit('reload', this.collapseId)
        return res
      })
    },
    updateTariffCode() {
      apiResponseWrapper(
        this,
        async () => {
          const res = await this.updateNotifyTariffCode({ id: this.ticket.id, data: { role: null } })
          this.$emit('reload', this.collapseId)
          return res
        },
        null,
        false,
      )
    },
  },
}
</script>

<style lang="scss" scoped>
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
