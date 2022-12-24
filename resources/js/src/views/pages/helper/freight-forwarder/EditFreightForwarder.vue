<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Edit Freight Forwarder</h2>
            <b-button @click.prevent="remove" class="float-right" variant="danger"> Delete </b-button>
            <router-link :to="{ name: 'helper-freight-forwarder-list' }" class="float-right btn btn-outline-success mr-2">
              Back to list
            </router-link>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row class="mb-2">
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
                <validation-provider #default="{ errors }" name="Password" vid="password">
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
      <hr />
    </b-container>
    <b-container class="mt-2" fluid>
      <b-row>
        <b-col sm="12" md="6" align-self="start">
          <span class="h2 align-middle mr-1">Contacts</span>
          <router-link
            :to="{
              name: 'create-helper-freight-contact',
              params: {
                ffId: $route.params.id,
              },
            }"
            class="d-inline-block btn btn-outline-success"
          >
            <feather-icon icon="PlusIcon" />
            Add New
          </router-link>
        </b-col>
      </b-row>
      <b-row class="mt-2">
        <b-col cols>
          <b-table show-empty :fields="columns" empty-text="No Conatct Found!." :items="contacts" @row-clicked="rowClicked">
            <template #cell(isDefault)="{ item }">
              <feather-icon
                :class="`text-${item.isDefault ? 'success' : 'danger'}`"
                :icon="`${item.isDefault ? 'Check' : 'X'}Icon`"
                size="18"
              />
            </template>
          </b-table>
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
  BForm,
  BButton,
  BCard,
  BRow,
  BCol,
  BFormSelect,
  BFormSelectOption,
  BContainer,
  BTable,
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
    BTable,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      required,
      countries: [],
      columns: [
        { key: 'isfcName', label: 'CONTACT NAME' },
        { key: 'isfcEmailAddress', label: 'Email' },
        { key: 'isfcBusinessPhone', label: 'BUSINESS PHONE' },
        { key: 'isfcMobilePhone', label: 'MOBILE PHONE' },
        { key: 'isDefault', label: 'DEFAULT' },
      ],
    }
  },
  computed: {
    ...mapState('freight-forwarder', { freightForwarder: 'freightForwarder', defaultFreightForwarder: 'defaultFreightForwarder' }),
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    contacts() {
      return (this.freightForwarder.contacts || []).filter(i => !i.deletedOn)
    },
  },
  async mounted() {
    const t = await this.getCountries()
    this.countries = t

    try {
      await this.findFreightForwarderById(this.$route.params.id)
    } catch (err) {
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({ name: 'helper-freight-forwarder-list' })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'agent', value: { ...this.defaultAgent } })
  },
  methods: {
    ...mapActions('app', { getCountries: 'getCountries' }),
    ...mapActions('freight-forwarder', { findFreightForwarderById: 'findById', editFreightForwarder: 'edit', deleteFF: 'delete' }),
    ...mapMutations('freight-forwarder', { setState: 'setState' }),
    async submit() {
      try {
        const t = await this.editFreightForwarder({ id: this.$route.params.id, data: this.freightForwarder })
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
    remove() {
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
            const t = await this.deleteFF(this.$route.params.id)
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: t.message,
              },
            })
            this.$router.push({ name: 'helper-freight-forwarder-list' })
            // eslint-disable-next-line no-empty
          } catch (err) {
            apiResponseHandler(this, err, {})
          }
        }
      })
    },
    rowClicked(item) {
      this.$router.push({
        name: 'edit-helper-freight-contact',
        params: {
          ffId: this.$route.params.id,
          id: item.id,
        },
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/vue-sweetalert.scss';
</style>
