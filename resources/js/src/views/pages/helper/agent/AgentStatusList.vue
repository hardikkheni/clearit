<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <h2>Manage agent statuses</h2>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col md="9" xs="12">
          <validation-observer ref="agentStatusForm" #default="{ invalid }">
            <b-form class="mt-2" inline @submit.prevent="addNewStatus">
              <b-form-group class="mr-1" label="Ticket Type">
                <validation-provider #default="{ errors }" name="Ticket Type" vid="type" rules="required">
                  <b-form-select v-model="form.type" :options="ticketTypeOptions" @change="paramChanges({ type: $event })" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <b-form-group class="mr-1" label="Role">
                <validation-provider #default="{ errors }" name="Role" vid="role" rules="required">
                  <b-form-select v-model="form.role" :options="roles" @change="paramChanges({ role: $event })">
                    <template #first>
                      <b-form-select-option :value="null">Please select an option </b-form-select-option>
                    </template>
                  </b-form-select>
                  <small class="d-block text-danger">{{ errors[0] }}</small>
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
              :list="agentStatuses"
              tag="ul"
              group="agentStatuses"
              class="list-group list-group-flush cursor-move"
              @start="drag = true"
              @end="dragEnd"
            >
              <transition-group type="transition" :name="!drag ? 'flip-list' : null">
                <template v-for="(agentStatus, i) of agentStatusList">
                  <li :key="i" class="btn btn-outline-secondary bg-light-secondary btn-block text-left">
                    {{ agentStatus.statusName }}
                    <feather-icon class="float-right" icon="XIcon" @click.prevent="remove(i)" />
                    <feather-icon v-b-toggle:[`status-${i}`] class="float-right mr-1" icon="EditIcon" />
                    <b-collapse :id="`status-${i}`" class="mt-1">
                      <validation-observer ref="statusForm">
                        <b-form class="mt-2" @submit.prevent="submit(i)">
                          <!-- email -->
                          <b-form-group>
                            <validation-provider #default="{ errors }" name="Status Name" vid="statusName" rules="required">
                              <b-form-input
                                v-model="agentStatus.statusName"
                                :state="errors.length > 0 ? false : null"
                                name="Status Name"
                                @change="submit(i)"
                              />
                              <small class="text-danger">{{ errors[0] }}</small>
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
import {
  BCard,
  BContainer,
  BRow,
  BCol,
  BForm,
  BFormGroup,
  BFormSelect,
  BButton,
  BFormInput,
  VBToggle,
  BCollapse,
  BFormSelectOption,
} from 'bootstrap-vue'
import Draggable from 'vuedraggable'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { required } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { ticketTypeOptions, TICKET_TYPE_CLEARANCE } from '@/utils/ticket'

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
    BFormSelectOption,
    Draggable,
  },
  directives: {
    'b-toggle': VBToggle,
  },
  data() {
    const { params } = this.$route
    const type = params.type || ticketTypeOptions[0].value
    const role = parseInt(params.role) || null
    return {
      required,
      drag: false,
      form: {
        type,
        role,
      },
      ticketTypeOptions,
      TICKET_TYPE_CLEARANCE,
    }
  },
  computed: {
    ...mapState('agent', { agentStatuses: 'agentStatusList', defaultAgentStatus: 'defaultAgentStatus' }),
    ...mapGetters('agent', { agentStatusList: 'agentStatusList' }),
    ...mapGetters('role', { roles: 'userRoleOptionsByIds' }),
  },
  watch: {
    $route() {
      const r = this.$route
      const p = r.params
      this.form = {
        type: p.type || this.ticketTypeOptions[0].value,
        role: parseInt(p.role) || null,
      }

      // reload page
      this.$nextTick(() => {
        this.reload()
      })
    },
  },
  async mounted() {
    await this.getUserRole()
    if (!this.$route.params.type) {
      this.paramChanges({ type: this.ticketTypeOptions[0].value })
    } else {
      this.reload()
    }
  },
  methods: {
    ...mapActions('role', { getUserRole: 'all' }),
    ...mapActions('agent', {
      getAgentStatusList: 'getAgentStatusList',
      upsertAgentStatus: 'upsertAgentStatus',
      deleteAgentStatus: 'deleteAgentStatus',
      reOrderAgentStatus: 'reOrderAgentStatus',
    }),
    ...mapMutations('agent', { setState: 'setState' }),
    async dragEnd(e) {
      this.drag = false
      if (e.oldIndex === e.newIndex) return
      try {
        const t = await this.reOrderAgentStatus({
          ...this.form,
          list: this.agentStatusList.map(i => ({ id: i.id, statusName: i.statusName })),
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
        apiResponseHandler(this, err, {}, this.$refs.agentStatusForm)
      }
    },
    async reload() {
      await this.getAgentStatusList(this.form)
    },
    async addNewStatus() {
      this.setState({ key: 'agentStatusList', value: [this.defaultAgentStatus, ...this.agentStatuses] })
    },
    async submit(i) {
      const status = this.agentStatusList[i]
      try {
        const t = await this.upsertAgentStatus({
          ...this.form,
          id: status.id,
          statusName: status.statusName,
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
        apiResponseHandler(this, err, {}, this.$refs.todoForm[i])
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
          const agentStatus = this.agentStatusList[i]
          if (agentStatus.id) {
            try {
              const t = await this.deleteAgentStatus(agentStatus.id)
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
              key: 'agentStatusList',
              value: [...this.agentStatuses.filter((_, e) => e !== i)],
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
