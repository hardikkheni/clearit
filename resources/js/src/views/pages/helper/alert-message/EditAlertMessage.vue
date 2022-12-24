<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add/Edit Alert Message</h2>
            <b-button class="float-md-right" variant="danger" @click.prevent="remove">Delete</b-button>
            <b-button v-b-modal.show-preview class="float-md-right mr-1" variant="success">Preview</b-button>
            <router-link :to="{ name: 'helper-alert-message-list' }" class="float-right btn btn-outline-success mr-1">
              Back to list
            </router-link>
            <!-- <b-button variant="outline-success"> Reset </b-button> -->
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

    <b-modal id="show-preview" centered :title="alertMessage.subject" ok-only ok-title="Done">
      <b-card-text>
        {{ alertMessage.messageBody }}
      </b-card-text>
    </b-modal>
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
  VBModal,
  BFormTextarea,
  BCardText,
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
    BCardText,
    // BAlert,
  },
  directives: {
    'b-modal': VBModal,
  },
  data() {
    return {
      required,
    }
  },
  computed: {
    ...mapState('alert-message', { alertMessage: 'alertMessage', defaultAlertMessage: 'defaultAlertMessage' }),
  },
  async mounted() {
    try {
      await this.findAlertMessageById(this.$route.params.id)
    } catch (err) {
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({ name: 'helper-alert-message-list' })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'alertMessage', value: { ...this.defaultAlertMessage } })
  },
  methods: {
    ...mapActions('alert-message', { findAlertMessageById: 'findById', editAlertMessage: 'edit', deleteAlertMessage: 'delete' }),
    ...mapMutations('alert-message', { setState: 'setState' }),
    async submit() {
      try {
        const t = await this.editAlertMessage({ id: this.$route.params.id, data: this.alertMessage })
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
            const res = await this.deleteAlertMessage(this.$route.params.id)
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: res.data.message,
              },
            })
            this.$router.push({ name: 'helper-alert-message-list' })
          } catch (err) {
            apiResponseHandler(this, err, {}, this.$refs.alertMessageForm)
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
