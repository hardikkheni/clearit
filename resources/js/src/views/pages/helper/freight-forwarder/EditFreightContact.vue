<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Edit Contact</h2>
            <b-button @click.prevent="remove" class="float-right" variant="danger"> Delete </b-button>
            <router-link
              :to="{ name: 'edit-helper-freight-forwarder', params: { id: $route.params.ffId } }"
              class="float-right btn btn-outline-success mr-2"
            >
              Back to list
            </router-link>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row class="mb-2">
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="freightContactForm" #default="{ invalid }">
            <b-form class="mt-2" @submit.prevent="submit">
              <b-form-group>
                <span class="h2">Company: </span>
                <router-link
                  :to="{
                    name: 'edit-helper-freight-forwarder',
                    params: { id: $route.params.ffId },
                  }"
                >
                  <span class="ml-3 h3 text-success"> {{ freightForwarder.isfcName }} </span>
                </router-link>
              </b-form-group>

              <b-form-group label="Company Name">
                <validation-provider #default="{ errors }" name="Company Name" vid="isfcName" rules="required">
                  <b-form-input v-model="freightContact.isfcName" :state="errors.length > 0 ? false : null" name="Company Name" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Office Phone">
                <validation-provider #default="{ errors }" name="Office Phone" vid="isfcBusinessPhone">
                  <b-form-input v-model="freightContact.isfcBusinessPhone" :state="errors.length > 0 ? false : null" name="Office Phone" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Mobile Phone">
                <validation-provider #default="{ errors }" name="Mobile Phone" vid="isfcMobilePhone">
                  <b-form-input v-model="freightContact.isfcMobilePhone" :state="errors.length > 0 ? false : null" name="Mobile Phone" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Email address">
                <validation-provider #default="{ errors }" name="Email address" vid="isfcEmailAddress" rules="required">
                  <b-form-input v-model="freightContact.isfcEmailAddress" :state="errors.length > 0 ? false : null" name="Email address" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="freightContact.isDefault" name="isDefault"> Default contact </b-form-checkbox>
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
import { BFormGroup, BFormInput, BForm, BButton, BCard, BRow, BCol, BContainer, BFormCheckbox } from 'bootstrap-vue'
import { mapActions, mapMutations, mapState } from 'vuex'
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
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormCheckbox,
  },
  data() {
    return {
      required,
      email,
    }
  },
  computed: {
    ...mapState('freight-forwarder', {
      freightContact: 'freightContact',
      freightForwarder: 'freightForwarder',
      defaultFreightContact: 'defaultFreightContact',
    }),
  },
  async mounted() {
    try {
      await this.findFreightForwarderById(this.$route.params.ffId)
      await this.findContactById(this.$route.params.id)
    } catch (err) {
      console.log(err)
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({
        name: 'edit-helper-freight-forwarder',
        params: { id: this.$route.params.ffId },
      })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'freight-forwarder', value: { ...this.defaultFreightContact } })
  },
  methods: {
    ...mapActions('freight-forwarder', {
      findFreightForwarderById: 'findById',
      editFreightContact: 'editContact',
      deleteFC: 'deleteContact',
      findContactById: 'findContactById',
    }),
    ...mapMutations('freight-forwarder', { setState: 'setState' }),
    async submit() {
      try {
        const { ffId, id } = this.$route.params
        const t = await this.editFreightContact({ ffId, id, data: this.freightContact })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({
          name: 'edit-helper-freight-forwarder',
          params: { id: ffId },
        })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.freightContactForm)
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
            const t = await this.deleteFC(this.$route.params.id)
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: t.data.message,
              },
            })
            this.$router.push({
              name: 'edit-helper-freight-forwarder',
              params: { id: this.$route.params.ffId },
            })
            // eslint-disable-next-line no-empty
          } catch (err) {
            apiResponseHandler(this, err, {})
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
