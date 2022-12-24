<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <h2>Manage ticket statuses</h2>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col md="9" xs="12">
          <validation-observer ref="ticketStatusForm" #default="{ invalid }">
            <b-form class="mt-2" inline @submit.prevent="addNewStatus">
              <b-form-group class="mr-1" label="Ticket Type">
                <validation-provider #default="{ errors }" name="Ticket Type" vid="type" rules="required">
                  <b-form-select v-model="form.type" :options="ticketTypeOptions" @change="paramChanges({ type: $event })" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <b-form-group v-if="form.type == TICKET_TYPE_CLEARANCE" class="mr-1" label="Transport">
                <validation-provider #default="{ errors }" name="Transport" vid="transport">
                  <b-form-select v-model="form.transport" :options="transportModeOptions" @change="paramChanges({ transport: $event })" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <fieldset class="form-group">
                <legend class="bv-no-focus-ring col-form-label pt-0">&nbsp;</legend>
                <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid">
                  <feather-icon icon="PlusIcon" /> Add New
                </b-button>
              </fieldset>
            </b-form>
          </validation-observer>
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col col md="9" sm="12">
          <span>Move elements below to change their order:</span>
          <div class="mt-2">
            <draggable
              :list="ticketStatuses"
              tag="ul"
              group="ticketStatuses"
              class="list-group list-group-flush cursor-move"
              @start="drag = true"
              @end="dragEnd"
            >
              <transition-group type="transition" :name="!drag ? 'flip-list' : null">
                <template v-for="(ticketStatus, i) of ticketStatusList">
                  <li :key="i" class="btn btn-outline-secondary bg-light-secondary btn-block text-left">
                    {{ ticketStatus.statusName }}
                    <b-button
                      size="sm"
                      class="ml-1 btn-icon custom-button"
                      :style="{ backgroundColor: `#${ticketStatus.hexColor} !important`, padding: '0.3rem' }"
                    >
                      <feather-icon size="15" :fill="`#${ticketStatus.textHexColor}`" stroke-width="0" icon="CircleIcon" />
                    </b-button>
                    <b-button
                      size="sm"
                      class="ml-1 btn-icon custom-button"
                      :style="{
                        backgroundColor: `#${ticketStatus.hexColor} !important`,
                        padding: '0.6rem',
                        color: `#${ticketStatus.textHexColor} !important`,
                      }"
                    >
                      {{ ticketStatus.statusName }}
                    </b-button>
                    <feather-icon class="float-right" icon="XIcon" @click.prevent="remove(i)" />
                    <feather-icon v-b-toggle:[`status-${i}`] class="float-right mr-1" icon="EditIcon" />
                    <b-collapse :id="`status-${i}`" class="mt-1">
                      <validation-observer ref="statusForm">
                        <b-form class="mt-2" @submit.prevent="submit(i)">
                          <!-- email -->
                          <b-form-group label="Status Name">
                            <validation-provider #default="{ errors }" name="Item Name" vid="statusName" rules="required">
                              <b-form-input
                                v-model="ticketStatus.statusName"
                                :state="errors.length > 0 ? false : null"
                                name="Status Name"
                                placeholder="Status Name"
                                @change="submit(i)"
                              />
                              <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                          </b-form-group>
                          <b-form-group label="Sub Status">
                            <validation-provider #default="{ errors }" name="Item Name" vid="substatus">
                              <b-form-input
                                v-model="ticketStatus.substatus"
                                :state="errors.length > 0 ? false : null"
                                name="Sub Status"
                                placeholder="Sub Status"
                                @change="submit(i)"
                              />
                              <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                          </b-form-group>
                          <b-form-group label="Hex Color">
                            <validation-provider #default="{ errors }" name="Item Name" vid="hexColor" rules="required">
                              <b-form-input
                                v-model="ticketStatus.hexColor"
                                :style="{
                                  backgroundColor: `#${ticketStatus.hexColor} !important`,
                                }"
                                :state="errors.length > 0 ? false : null"
                                name="Hex Color"
                                placeholder="Hex Color"
                                @change="submit(i)"
                                @click="toggleColorPicker('showHexColorPicker', i, true, $event)"
                              />
                              <chrome-picker
                                v-if="ticketStatus.showHexColorPicker"
                                v-click-out-side="handleClickOutSide('showHexColorPicker', i)"
                                class="position-absolute"
                                :value="computedColor(i)"
                                @input="pickColor($event, 'hexColor', i)"
                              />
                              <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                          </b-form-group>
                          <b-form-group label="Text Hex Color">
                            <validation-provider #default="{ errors }" name="Item Name" vid="textHexColor" rules="required">
                              <b-form-input
                                v-model="ticketStatus.textHexColor"
                                :style="{ backgroundColor: `#${ticketStatus.textHexColor} !important` }"
                                :state="errors.length > 0 ? false : null"
                                name="Text Hex Color"
                                readonly
                                placeholder="Text Hex Color"
                                @change="submit(i)"
                                @click="toggleColorPicker('showTextHexColorPicker', i, true, $event)"
                              />
                              <small class="text-danger">{{ errors[0] }}</small>
                              <chrome-picker
                                v-if="ticketStatus.showTextHexColorPicker"
                                v-click-out-side="handleClickOutSide('showTextHexColorPicker', i)"
                                class="position-absolute"
                                :value="computedColor(i)"
                                @input="pickColor($event, 'textHexColor', i)"
                              />
                            </validation-provider>
                          </b-form-group>
                        </b-form>
                      </validation-observer>
                    </b-collapse>
                  </li>
                </template>
              </transition-group>
            </draggable>
          </div>
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
/* eslint-disable radix */
import { BCard, BContainer, BRow, BCol, BForm, BFormGroup, BFormSelect, BButton, BFormInput, VBToggle, BCollapse } from 'bootstrap-vue'
import Draggable from 'vuedraggable'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { Chrome as ChromePicker } from 'vue-color'

import { required } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import clickOutSide from '@core/directives/click-out-side'
import { ticketTypeOptions, transportModeOptions, TICKET_TYPE_CLEARANCE } from '@/utils/ticket'
import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BContainer,
    BRow,
    BCol,
    BForm,
    BFormGroup,
    BFormSelect,
    ValidationProvider,
    ValidationObserver,
    BButton,
    BFormInput,
    BCollapse,
    Draggable,
    ChromePicker,
  },
  directives: {
    'b-toggle': VBToggle,
    clickOutSide,
  },

  data() {
    const { params } = this.$route
    const type = params.type || ticketTypeOptions[0].value
    const transport = parseInt(params.transport || transportModeOptions[0].value)
    return {
      required,
      drag: false,
      form: {
        type,
        transport,
      },
      ticketTypeOptions,
      transportModeOptions,
      TICKET_TYPE_CLEARANCE,
      timeOut: null,
    }
  },
  computed: {
    ...mapState('ticket', { ticketStatuses: 'ticketStatusList', defaultTicketStatus: 'defaultTicketStatus' }),
    ...mapGetters('ticket', { ticketStatusList: 'ticketStatusList' }),
    computedColor() {
      return i => `#${this.ticketStatusList[i].hexColor}`
    },
  },
  watch: {
    $route() {
      const r = this.$route
      const p = r.params
      this.form = {
        type: p.type || this.ticketTypeOptions[0].value,
        transport: parseInt(p.transport || transportModeOptions[0].value),
      }

      // reload page
      this.$nextTick(() => {
        this.reload()
      })
    },
  },
  async mounted() {
    if (!this.$route.params.type) {
      this.paramChanges({ type: this.ticketTypeOptions[0].value })
    } else {
      this.reload()
    }
  },
  methods: {
    ...mapActions('ticket', {
      getTicketStatusList: 'getTicketStatusList',
      upsertTicketStatus: 'upsertTicketStatus',
      deleteTicketStatus: 'deleteTicketStatus',
      reOrderTicketStatus: 'reOrderTicketStatus',
    }),
    ...mapMutations('ticket', { setState: 'setState' }),
    toggleColorPicker(key, i, status, $event) {
      if ($event) $event.stopPropagation()
      const t = [...(this.ticketStatuses || [])]
      t[i][key] = status
      this.setState({
        key: 'ticketStatusList',
        value: [...t],
      })
    },
    handleClickOutSide(key, i) {
      return () => {
        const t = [...(this.ticketStatuses || [])][i]
        if (t[key]) {
          this.toggleColorPicker(key, i, false)
        }
      }
    },
    pickColor(e, key, i) {
      const t = [...(this.ticketStatuses || [])]
      t[i][key] = e.hex.substr(1)
      this.setState({
        key: 'ticketStatusList',
        value: [...t],
      })
      const callable = () => {
        if (this.timeOut) {
          clearTimeout(this.timeOut)
        }
        this.timeOut = setTimeout(() => this.submit(i), 500)
      }
      callable()
    },
    async dragEnd(e) {
      this.drag = false
      if (e.oldIndex === e.newIndex) return
      try {
        const t = await this.reOrderTicketStatus({
          ...this.form,
          list: this.ticketStatuses.map(i => ({ id: i.id, statusName: i.statusName })),
        })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.message,
          },
        })
        this.reload()
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.ticketStatusForm)
      }
    },
    async reload() {
      await this.getTicketStatusList(this.form)
    },
    async addNewStatus() {
      this.setState({ key: 'ticketStatusList', value: [this.defaultTicketStatus, ...this.ticketStatuses] })
    },
    async submit(i) {
      const { id, statusName, substatus, hexColor, textHexColor } = this.ticketStatusList[i]
      try {
        const t = await this.upsertTicketStatus({
          ...this.form,
          id,
          statusName,
          substatus,
          hexColor,
          textHexColor,
          displayOrder: i + 1,
        })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.message,
          },
        })
        this.reload()
        // eslint-disable-next-line no-empty
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.statusForm[i])
      }
    },
    async remove(i) {
      this.$swal({
        title: 'Are you sure?',
        text: 'You want to delete this!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-outline-danger',
          cancelButton: 'btn btn-primary ml-1',
        },
        buttonsStyling: false,
      }).then(async result => {
        if (result.value) {
          const ticketStatus = this.ticketStatusList[i]
          if (ticketStatus.id) {
            try {
              const t = await this.deleteTicketStatus(ticketStatus.id)
              this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Success',
                  icon: 'CheckIcon',
                  variant: 'success',
                  text: t.message,
                },
              })
              this.reload()
            } catch (err) {
              apiResponseHandler(this, err, {})
            }
          } else {
            this.setState({
              key: 'ticketStatusList',
              value: [...this.ticketStatuses.filter((_, e) => e !== i)],
            })
          }
        }
      })
    },
    paramChanges(params = {}) {
      const newR = { ...this.$route.params }
      if (!newR.type) {
        newR.type = this.ticketTypeOptions[0].value
      }
      if (!newR.transport) {
        newR.transport = this.transportModeOptions[0].value
      }
      this.$router.push({
        name: this.$route.name,
        params: {
          ...newR,
          ...params,
        },
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
