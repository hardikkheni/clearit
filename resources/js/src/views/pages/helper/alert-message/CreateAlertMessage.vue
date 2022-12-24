<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add/Edit Alert Message</h2>
            <router-link :to="{ name: 'helper-alert-message-list' }" class="float-right btn btn-outline-success">Back to list</router-link>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="alertMessageForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group label="Subject">
                <validation-provider #default="{ errors }" name="Subject" vid="subject" rules="required">
                  <b-form-input v-model="alertMessage.subject" :state="errors.length > 0 ? false : null" name="Subject" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Message Body">
                <validation-provider #default="{ errors }" name="Message Body" vid="messageBody">
                  <b-form-textarea
                    v-model="alertMessage.messageBody"
                    :state="errors.length > 0 ? false : null"
                    name="messageBody"
                    rows="3"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Active" label-for="isActive">
                <b-form-select v-model="alertMessage.isActive" name="isActive">
                  <b-form-select-option :value="false">No</b-form-select-option>
                  <b-form-select-option :value="true">Yes</b-form-select-option>
                </b-form-select>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="alertMessage.acknowledgementRequired" name="acknowledgementRequired">
                  Requires Acknowledgement
                </b-form-checkbox>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="alertMessage.showNewAgent" name="showNewAgent"> Show To New Agents </b-form-checkbox>
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
  BForm,
  BButton,
  BCard,
  BRow,
  BCol,
  BFormCheckbox,
  BFormSelect,
  BFormSelectOption,
  BContainer,
  BFormTextarea,
} from 'bootstrap-vue'
import { mapActions, mapMutations, mapState } from 'vuex'
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
    BFormCheckbox,
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormSelect,
    BFormSelectOption,
    BFormTextarea,
  },
  data() {
    return {
      required,
    }
  },
  computed: {
    ...mapState('alert-message', { alertMessage: 'alertMessage', defaultAlertMessage: 'defaultAlertMessage' }),
  },
  mounted() {},
  beforeDestroy() {
    this.setState({ key: 'alertMessage', value: { ...this.defaultAlertMessage } })
  },
  methods: {
    ...mapActions('alert-message', { createAlertMessage: 'create' }),
    ...mapMutations('alert-message', { setState: 'setState' }),
    async submit() {
      try {
        const t = await this.createAlertMessage(this.alertMessage)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({ name: 'helper-alert-message-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.alertMessageForm)
      }
    },
  },
}
</script>
