<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add/Edit Agents</h2>
            <router-link :to="{ name: 'helper-agent-list' }" class="float-right btn btn-outline-success">Back to list</router-link>
            <!-- <b-button variant="outline-success"> Reset </b-button> -->
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="agentForm" #default="{ invalid }">
            <b-form class="auth-login-form mt-2" @submit.prevent="submit">
              <!-- email -->
              <b-form-group label="Username/Login" label-for="login-Username">
                <validation-provider #default="{ errors }" name="Username" vid="login" rules="required">
                  <b-form-input v-model="agent.login" :state="errors.length > 0 ? false : null" name="login-Username" placeholder="john" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Firstname" label-for="firstname">
                <validation-provider #default="{ errors }" name="Firstname" vid="firstname" rules="required">
                  <b-form-input
                    v-model="agent.firstname"
                    :state="errors.length > 0 ? false : null"
                    name="login-Firstname"
                    placeholder="john"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Lastname" label-for="lastname">
                <validation-provider #default="{ errors }" name="Lastname" vid="lastname" rules="required">
                  <b-form-input v-model="agent.lastname" :state="errors.length > 0 ? false : null" name="lastname" placeholder="doe" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Email" label-for="email">
                <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
                  <b-form-input
                    v-model="agent.email"
                    :state="errors.length > 0 ? false : null"
                    name="email"
                    placeholder="jondoe@gmail.com"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label-for="isMaster">
                <b-form-checkbox v-model="agent.isMaster" name="isMaster"> Admin Agent </b-form-checkbox>
              </b-form-group>

              <b-form-group label="City" label-for="city">
                <validation-provider #default="{ errors }" name="City" vid="city" rules="required">
                  <b-form-input v-model="agent.city" :state="errors.length > 0 ? false : null" name="city" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Country" label-for="country">
                <validation-provider #default="{ errors }" name="Country" vid="country" rules="required">
                  <b-form-select ref="country" v-model="agent.country" :options="countries" name="country">
                    <template #first>
                      <b-form-select-option :value="null">Please select an option </b-form-select-option>
                    </template>
                  </b-form-select>
                  <!-- <b-form-input v-model="agent.city" :state="errors.length > 0 ? false : null" name="city" /> -->
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Province/State" label-for="state">
                <validation-provider #default="{ errors }" name="State" vid="state" rules="required">
                  <b-form-input v-model="agent.state" :state="errors.length > 0 ? false : null" name="state" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Active" label-for="isActive">
                <b-form-select v-model="agent.isActive" name="isActive">
                  <b-form-select-option :value="false">No</b-form-select-option>
                  <b-form-select-option :value="true">Yes</b-form-select-option>
                </b-form-select>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="agent.displayinternally" name="displayinternally"> Display Internally </b-form-checkbox>
              </b-form-group>

              <b-form-group label="Assign Role(s) : must select atleast one">
                <validation-provider #default="{ errors }" name="Roles" vid="roles">
                  <b-form-checkbox-group v-model="agent.permissions" stacked :options="roles" name="roles" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group label="Password">
                <!-- <div class="d-flex justify-content-between">
                <label for="login-password">Password</label>
                <b-link :to="{ name: 'auth-forgot-password' }">
                  <small>Forgot Password?</small>
                </b-link>
              </div> -->
                <validation-provider #default="{ errors }" name="Password" vid="password">
                  <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                    <b-form-input
                      id="login-password"
                      v-model="agent.password"
                      :state="errors.length > 0 ? false : null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="login-password"
                      placeholder="Password"
                    />
                    <b-input-group-append is-text>
                      <feather-icon class="cursor-pointer" :icon="passwordToggleIcon" @click="togglePasswordVisibility" />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- checkbox -->
              <!-- <b-form-group>
                <b-form-checkbox id="remember-me" v-model="status" name="checkbox-1"> Remember Me </b-form-checkbox>
              </b-form-group> -->

              <!-- submit buttons -->
              <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid"> Save Changes </b-button>
            </b-form>
          </validation-observer>
        </b-col>
      </b-row>
    </b-container>
  </b-card>
</template>

<script>
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import {
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  // BFormCheckbox,
  BForm,
  BButton,
  BCard,
  BRow,
  BCol,
  BFormCheckbox,
  BFormCheckboxGroup,
  BFormSelect,
  BFormSelectOption,
  BContainer,
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import { required, email } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    ValidationProvider,
    ValidationObserver,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormSelect,
    BFormSelectOption,
    BFormCheckboxGroup,
    // BAlert,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      required,
      email,
      countries: [],
    }
  },
  computed: {
    ...mapGetters('role', { roles: 'userRoles' }),
    ...mapState('agent', { agent: 'agent', defaultAgent: 'defaultAgent' }),
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  async mounted() {
    const t = await this.getCountries()
    this.countries = t
    await this.getUserRole()
    try {
      await this.findAgentById(this.$route.params.id)
    } catch (err) {
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({ name: 'helper-agent-list' })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'agent', value: { ...this.defaultAgent } })
  },
  methods: {
    ...mapActions('role', { getUserRole: 'all' }),
    ...mapActions('app', { getCountries: 'getCountries' }),
    ...mapActions('agent', { findAgentById: 'findById', editAgent: 'edit' }),
    ...mapMutations('agent', { setState: 'setState' }),
    async submit() {
      try {
        const t = await this.editAgent({ id: this.$route.params.id, data: this.agent })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({ name: 'helper-agent-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.agentForm)
      }
    },
  },
}
</script>
