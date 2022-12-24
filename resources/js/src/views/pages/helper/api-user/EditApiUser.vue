<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add/Edit Api User</h2>
            <router-link :to="{ name: 'helper-api-user-list' }" class="float-right btn btn-outline-success">Back to list</router-link>
            <!-- <b-button variant="outline-success"> Reset </b-button> -->
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="apiUserForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <!-- email -->
              <b-form-group label="Company">
                <validation-provider #default="{ errors }" name="Company" vid="company" rules="required">
                  <b-form-input v-model="apiUser.company" :state="errors.length > 0 ? false : null" name="Company" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Firstname" label-for="firstname">
                <validation-provider #default="{ errors }" name="Firstname" vid="firstname" rules="required">
                  <b-form-input v-model="apiUser.firstname" :state="errors.length > 0 ? false : null" name="Firstname" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Lastname" label-for="lastname">
                <validation-provider #default="{ errors }" name="Lastname" vid="lastname" rules="required">
                  <b-form-input v-model="apiUser.lastname" :state="errors.length > 0 ? false : null" name="lastname" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Email" label-for="email">
                <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
                  <b-form-input
                    v-model="apiUser.email"
                    :state="errors.length > 0 ? false : null"
                    name="email"
                    placeholder="jondoe@gmail.com"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Token">
                <validation-provider #default="{ errors }" name="Token" vid="token">
                  <b-form-input v-model="apiUser.token" :state="errors.length > 0 ? false : null" name="token" />
                  <b-button class="mt-1" variant="success" @click.prevent="generateToken">Generate</b-button>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group label="Password">
                <validation-provider #default="{ errors }" name="Password" vid="password">
                  <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                    <b-form-input
                      v-model="apiUser.password"
                      :state="errors.length > 0 ? false : null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="password"
                      placeholder="Password"
                    />
                    <b-input-group-append is-text>
                      <feather-icon class="cursor-pointer" :icon="passwordToggleIcon" @click="togglePasswordVisibility" />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Active" label-for="isActive">
                <b-form-select v-model="apiUser.isActive" name="isActive">
                  <b-form-select-option :value="false">No</b-form-select-option>
                  <b-form-select-option :value="true">Yes</b-form-select-option>
                </b-form-select>
              </b-form-group>

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
  BFormSelect,
  BFormSelectOption,
  BContainer,
} from 'bootstrap-vue'
import { mapActions, mapMutations, mapState } from 'vuex'
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
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormSelect,
    BFormSelectOption,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      required,
      email,
    }
  },
  computed: {
    ...mapState('api-user', { apiUser: 'apiUser', defaultApiUser: 'defaultApiUser' }),
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  async mounted() {
    try {
      await this.findApiUserById(this.$route.params.id)
    } catch (err) {
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({ name: 'helper-api-user-list' })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'api-user', value: { ...this.defaultApiUser } })
  },
  methods: {
    ...mapActions('api-user', { findApiUserById: 'findById', editApiUser: 'edit' }),
    ...mapMutations('api-user', { setState: 'setState' }),
    generateToken() {
      const rand = () => Math.random(0).toString(36).substr(2)
      const token = length => (rand() + rand() + rand() + rand()).substr(0, length)
      this.apiUser.token = token(40)
    },
    async submit() {
      try {
        const t = await this.editApiUser({ id: this.$route.params.id, data: this.apiUser })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({ name: 'helper-api-user-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.apiUserForm)
      }
    },
  },
}
</script>
