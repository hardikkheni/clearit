<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add Freight Forwarder</h2>
            <router-link :to="{ name: 'helper-freight-forwarder-list' }" class="float-right btn btn-outline-success">
              Back to list
            </router-link>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="freightForwarderForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group label="Name">
                <validation-provider #default="{ errors }" name="Name" vid="isfcName" rules="required">
                  <b-form-input v-model="freightForwarder.isfcName" :state="errors.length > 0 ? false : null" name="Name" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <b-form-group label="Address Line 1">
                <validation-provider #default="{ errors }" name="Address Line 1" vid="isfcAddress1" rules="required">
                  <b-form-input v-model="freightForwarder.isfcAddress1" :state="errors.length > 0 ? false : null" name="Address Line 1" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <b-form-group label="Address Line 2">
                <validation-provider #default="{ errors }" name="Address Line 2" vid="isfcAddress2">
                  <b-form-input v-model="freightForwarder.isfcAddress2" :state="errors.length > 0 ? false : null" name="Address Line 2" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Country">
                <validation-provider #default="{ errors }" name="Country" vid="isfcCountry" rules="required">
                  <b-form-select v-model="freightForwarder.isfcCountry" :options="countries" name="Country">
                    <template #first>
                      <b-form-select-option :value="null">Please select an option </b-form-select-option>
                    </template>
                  </b-form-select>
                  <!-- <b-form-input v-model="freightForwarder.city" :state="errors.length > 0 ? false : null" name="city" /> -->
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="City">
                <validation-provider #default="{ errors }" name="City" vid="isfcCity" rules="required">
                  <b-form-input v-model="freightForwarder.isfcCity" :state="errors.length > 0 ? false : null" name="City" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Province/State">
                <validation-provider #default="{ errors }" name="State" vid="isfcState">
                  <b-form-input v-model="freightForwarder.isfcState" :state="errors.length > 0 ? false : null" name="Province/State" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Username/Login">
                <validation-provider #default="{ errors }" name="Username" vid="login" rules="required">
                  <b-form-input
                    v-model="freightForwarder.login"
                    :state="errors.length > 0 ? false : null"
                    name="Username"
                    placeholder="john"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Postal Code">
                <validation-provider #default="{ errors }" name="Postal Code" vid="isfcZip" rules="required">
                  <b-form-input v-model="freightForwarder.isfcZip" :state="errors.length > 0 ? false : null" name="Postal Code" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Business Phone">
                <validation-provider #default="{ errors }" name="Business Phone" vid="isfcBusinessPhone" rules="required">
                  <b-form-input
                    v-model="freightForwarder.isfcBusinessPhone"
                    :state="errors.length > 0 ? false : null"
                    name="Business Phone"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group label="Password">
                <validation-provider #default="{ errors }" name="Password" vid="password" rules="required">
                  <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                    <b-form-input
                      v-model="freightForwarder.password"
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
import { required } from '@validations'
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
      countries: [],
    }
  },
  computed: {
    ...mapState('freight-forwarder', { freightForwarder: 'freightForwarder', defaultFreightForwarder: 'defaultFreightForwarder' }),
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  async mounted() {
    const t = await this.getCountries()
    this.countries = t
  },
  beforeDestroy() {
    this.setState({ key: 'freight-forwarder', value: { ...this.defaultFreightForwarder } })
  },
  methods: {
    ...mapActions('app', { getCountries: 'getCountries' }),
    ...mapActions('freight-forwarder', { createFreightForwarder: 'create' }),
    ...mapMutations('freight-forwarder', { setState: 'setState' }),
    async submit() {
      try {
        const t = await this.createFreightForwarder(this.freightForwarder)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({ name: 'helper-freight-forwarder-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.freightForwarderForm)
      }
    },
  },
}
</script>
