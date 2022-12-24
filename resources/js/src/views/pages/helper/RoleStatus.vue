<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <span class="h2 align-middle mr-1">Role - Status Set Up</span>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="roleStatusForm">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group label="Role" label-cols-md="3" content-cols-md="6">
                <validation-provider #default="{ errors }" name="Role" vid="isfcName" rules="required">
                  <b-form-select v-model="form.role" :options="roles" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group>
                <validation-provider #default="{ errors }">
                  <b-form-checkbox-group v-model="form.permissions" stacked :options="ticketStatus2" @change="submit" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid"> Save Changes </b-button>
              <b-button variant="outline-success" :to="{ name: 'helper-administration' }"> Cancel </b-button> -->
            </b-form>
          </validation-observer>
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
  //  BButton,
  BForm,
  BFormSelect,
  BFormCheckboxGroup,
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapState } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import { required } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'
import { mapBitMaskValue } from '@/utils/permissions'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    // BButton,
    BFormGroup,
    ValidationProvider,
    ValidationObserver,
    BForm,
    BFormSelect,
    BFormCheckboxGroup,
  },
  data() {
    return {
      required,
      form: { role: null, permissions: [] },
    }
  },
  computed: {
    ...mapState('role', { userRoles: 'roles' }),
    ...mapGetters('auth', { ticketStatus2: 'ticketStatus2' }),
    roles() {
      return this.userRoles.map(role => ({ ...role, value: role.id, text: role.name }))
    },
    role() {
      return this.roles.find(role => role.id === this.form.role)
    },
  },
  watch: {
    // eslint-disable-next-line object-shorthand
    'form.role'() {
      this.form.permissions = mapBitMaskValue(
        this.ticketStatus2.map(t => t.bitmaskValue),
        this.role?.bitmaskValue || 0,
      )
    },
  },
  async mounted() {
    await this.getUserRole()
    await this.getTicketStatus2()
  },
  methods: {
    ...mapActions('role', {
      getUserRole: 'all',
    }),
    ...mapActions('auth', {
      getTicketStatus2: 'getTicketStatus2',
      updateUserRolePermissions: 'updateUserRolePermissions',
    }),
    async submit() {
      if (this.role) {
        try {
          const data = await this.updateUserRolePermissions(this.form)
          this.$toast({
            component: ToastificationContent,
            props: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: data.message,
            },
          })
        } catch (err) {
          apiResponseHandler(this, err, {})
        }
      }
    },
  },
}
</script>
