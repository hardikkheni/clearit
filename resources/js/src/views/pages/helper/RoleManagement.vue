<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">Role Management</span>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="roleForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group label="Role" label-cols-md="3" content-cols-md="6">
                <validation-provider #default="{ errors }" name="Role" vid="roleId" rules="required">
                  <b-form-select v-model="form.roleId" :options="roles" @change="handleRoleChanges" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Agent" label-cols-md="3" content-cols-md="6">
                <validation-provider #default="{ errors }" name="Agent" vid="agentId" rules="required">
                  <b-form-select v-model="form.agentId" :options="allInternalAgents" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid"> Save Changes </b-button>
            </b-form>
          </validation-observer>
          <hr />
        </b-col>
      </b-row>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton-table :rows="10" :columns="2" />
            </template>
            <b-table-simple small hover responsive>
              <b-thead>
                <b-tr>
                  <b-th colspan="2">AGENT NAME</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <template v-if="!!(agents || []).length">
                  <b-tr v-for="(agent, i) of agents" :key="i">
                    <b-td>{{ agent.firstname }} {{ agent.lastname }}</b-td>
                    <b-td>
                      <b-button size="sm" variant="flat-danger" class="btn-icon" @click.prevent="remove(agent.id)">
                        <feather-icon icon="Trash2Icon" />
                      </b-button>
                    </b-td>
                  </b-tr>
                </template>
                <template v-else>
                  <b-td class="text-center" colspan="2">No data found!.</b-td>
                </template>
              </b-tbody>
            </b-table-simple>
          </b-skeleton-wrapper>
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BFormGroup,
  BContainer,
  BButton,
  BForm,
  BFormSelect,
  BTableSimple,
  BTr,
  BTh,
  BTd,
  BThead,
  BTbody,
  BSkeletonTable,
  BSkeletonWrapper,
  // BFormCheckboxGroup,
} from 'bootstrap-vue'
import { mapActions, mapState } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import { required } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BFormGroup,
    ValidationProvider,
    ValidationObserver,
    BForm,
    BFormSelect,
    BButton,
    BTableSimple,
    BTr,
    BTh,
    BTd,
    BThead,
    BTbody,
    BSkeletonTable,
    BSkeletonWrapper,
  },
  data() {
    return {
      required,
      form: { roleId: null, agentId: null },
      agents: [],
      allInternalAgents: [],
      loading: false,
    }
  },
  computed: {
    ...mapState('role', { userRoles: 'roles' }),
    roles() {
      return this.userRoles.map(role => ({ ...role, value: role.id, text: role.name }))
    },
    role() {
      return this.roles.find(role => role.id === this.form.role)
    },
  },
  async mounted() {
    await this.getUserRole()
  },
  methods: {
    ...mapActions('role', {
      getUserRole: 'all',
      getRoleAgents: 'getRoleAgents',
      grantRevokeAgentFromRole: 'grantRevokeAgentFromRole',
    }),
    async submit() {
      try {
        const data = await this.grantRevokeAgentFromRole(this.form)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: data.message,
          },
        })
        await this.fetchRoleAgents()
        this.form.agentId = null
        this.$refs.roleForm.reset()
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
    async fetchRoleAgents() {
      try {
        this.loading = true
        const agents = (await this.getRoleAgents(this.form.roleId)).data
        this.loading = false
        const group = agents.reduce(
          (col, i) => {
            if (parseFloat(i.inRole) > 0) {
              col.agents.push(i)
            } else {
              col.allInternalAgents.push({ ...i, value: i.id, text: [i.firstname, i.lastname].join(' ') })
            }
            return col
          },
          { agents: [], allInternalAgents: [] },
        )
        this.agents = group.agents
        this.allInternalAgents = group.allInternalAgents
      } catch (err) {
        this.loading = false
      }
    },
    async handleRoleChanges() {
      await this.fetchRoleAgents()
    },
    remove(agentId) {
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
          try {
            const res = await this.grantRevokeAgentFromRole({
              roleId: this.form.roleId,
              agentId,
            })
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: res.message,
              },
            })
            await this.fetchRoleAgents()
            this.$refs.roleForm.reset()
            this.form.agentId = null
          } catch (err) {
            console.log(err)
            apiResponseHandler(this, err, {}, this.$refs.roleForm)
          }
        }
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
