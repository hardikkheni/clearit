<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <h2 class="float-left">Add/Edit Affiliate</h2>
            <b-button class="float-right" @click.prevent="remove" variant="danger">Delete</b-button>
            <router-link :to="{ name: 'helper-affiliate-list' }" class="float-right btn btn-outline-success mr-2">Back to list</router-link>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col lg="5" md="6" xs="12">
          <validation-observer ref="affiliateForm" #default="{ invalid }">
            <b-form class="auth-login-form mt-2" @submit.prevent="submit">
              <!-- email -->
              <b-form-group label="Company Name">
                <validation-provider #default="{ errors }" name="Company Name" vid="companyname" rules="required">
                  <b-form-input v-model="affiliate.companyname" :state="errors.length > 0 ? false : null" name="Company Name" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Referral Code">
                <validation-provider #default="{ errors }" name="Referral Code" vid="affiliateCode" rules="required">
                  <b-form-input v-model="affiliate.affiliateCode" :state="errors.length > 0 ? false : null" name="affiliateCode" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group>
                <h3>Referral Link</h3>
                <router-link :to="`${APP_URL}/brokerage/`">{{ `${APP_URL}/brokerage/${affiliate.affiliateCode}` }}</router-link>
              </b-form-group>

              <b-form-group label="Contact Code">
                <b-row>
                  <b-col col md="6">
                    <validation-provider #default="{ errors }" name="Contact First Name" vid="contactfirstname">
                      <b-form-input
                        v-model="affiliate.contactfirstname"
                        :state="errors.length > 0 ? false : null"
                        name="contactfirstname"
                      />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                  <b-col col md="6">
                    <validation-provider #default="{ errors }" name="Contact Last Name" vid="contactlastname">
                      <b-form-input v-model="affiliate.contactlastname" :state="errors.length > 0 ? false : null" name="contactlastname" />
                      <small class="text-danger">{{ errors[0] }}</small>
                    </validation-provider>
                  </b-col>
                </b-row>
              </b-form-group>

              <b-form-group label="Phone">
                <validation-provider #default="{ errors }" name="Phone" vid="phone">
                  <b-form-input v-model="affiliate.phone" :state="errors.length > 0 ? false : null" name="phone" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group v-if="!affiliate.logo_url" label="Logo">
                <b-form-file
                  v-model="affiliate.logofilename"
                  placeholder="Choose a file or drop it here..."
                  drop-placeholder="Drop file here..."
                  accept="image/*"
                />
                <small class="text-secondary">Only JPG, PNG or GIF files allowed </small>

                <div v-if="affiliate.logofilename">
                  <b-button size="sm" variant="flat-danger" @click.prevent="affiliate.logofilename = null">
                    <feather-icon icon="XIcon" />
                    Remove
                  </b-button>
                </div>
              </b-form-group>
              <b-form-group v-else label="Logo">
                <div>
                  <b-img class="mb-1 mb-2" width="300" :src="affiliate.logo_url" />
                  <b-button variant="danger" @click.prevent="removeLogo"> Remove Current Logo </b-button>
                </div>
              </b-form-group>

              <b-form-group label="Accent Color">
                <validation-provider #default="{ errors }" name="Accent Color" vid="accent_color">
                  <b-form-input v-model="affiliate.accent_color" name="accent_color" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="affiliate.expressEnabled" name="expressEnabled"> Enable Express Signup </b-form-checkbox>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="affiliate.isPaymentProfileRequired" name="isPaymentProfileRequired">
                  Require payment profile
                </b-form-checkbox>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="affiliate.isCreditAccount" name="isCreditAccount"> Credit account </b-form-checkbox>
              </b-form-group>

              <b-form-group>
                <b-form-checkbox v-model="affiliate.disableChatEmails" name="disableChatEmails"> Disable chat emails </b-form-checkbox>
              </b-form-group>

              <b-form-group label="Notification Email">
                <validation-provider #default="{ errors }" name="Notification Email" vid="notificationEmail" rules="required|email">
                  <b-form-input v-model="affiliate.notificationEmail" :state="errors.length > 0 ? false : null" name="notificationEmail" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Website">
                <validation-provider #default="{ errors }" name="Website">
                  <b-form-input v-model="affiliate.website" name="website" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Mail List">
                <validation-provider #default="{ errors }" name="Mail List" vid="mail_list_id">
                  <b-form-select ref="mail_list_id" v-model="affiliate.mail_list_id" name="mail_list_id">
                    <template #first>
                      <b-form-select-option :value="null">NO List </b-form-select-option>
                    </template>
                    <b-form-select-option :value="1"> ClearitUSA </b-form-select-option>
                    <b-form-select-option :value="2"> FreightOS </b-form-select-option>
                  </b-form-select>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="PoA Verbiage">
                <validation-provider #default="{ errors }" name="PoA Verbiage" vid="poaVerbiage">
                  <b-form-textarea v-model="affiliate.poaVerbiage" name="poaVerbiage" rows="3" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="PoA Company Info">
                <validation-provider #default="{ errors }" name="PoA Company Info" vid="poaCompanyInfo">
                  <b-form-input v-model="affiliate.poaCompanyInfo" name="poaCompanyInfo" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="PoA Thank you URL">
                <validation-provider #default="{ errors }" name="PoA Thank you URL" vid="poaThankyouUrl">
                  <b-form-input v-model="affiliate.poaThankyouUrl" name="poaThankyouUrl" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Active" label-for="isActive">
                <b-form-select v-model="affiliate.isActive" name="isActive">
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
  BFormFile,
  BImg,
} from 'bootstrap-vue'
import { mapActions, mapMutations, mapState } from 'vuex'
import { required, email } from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BFormFile,
    BCard,
    ValidationProvider,
    ValidationObserver,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormCheckbox,
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormSelect,
    BFormSelectOption,
    BImg,
  },
  data() {
    return {
      required,
      email,
      APP_URL: process.env.APP_URL,
    }
  },
  computed: {
    ...mapState('affiliate', { affiliate: 'affiliate', defaultAffiliate: 'defaultAffiliate' }),
  },
  async mounted() {
    try {
      await this.findAffiliateById(this.$route.params.id)
    } catch (err) {
      apiResponseHandler(this, err, {
        toast: {
          title: 'Error',
          icon: 'AlertCircleIcon',
          variant: 'danger',
          text: err.response.data.message,
        },
      })
      this.$router.push({ name: 'helper-affiliate-list' })
    }
  },
  beforeDestroy() {
    this.setState({ key: 'affiliate', value: { ...this.defaultAffiliate } })
  },
  methods: {
    ...mapActions('affiliate', { findAffiliateById: 'findById', editAffiliate: 'edit', deleteAffiliate: 'delete' }),
    ...mapMutations('affiliate', { setState: 'setState' }),
    removeLogo() {
      this.affiliate.logo_url = null
      this.affiliate.remove_logo = true
    },
    async submit() {
      try {
        const t = await this.editAffiliate({ id: this.$route.params.id, data: this.affiliate })
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        this.$router.push({ name: 'helper-affiliate-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.affiliateForm)
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
            const res = await this.deleteAffiliate(this.$route.params.id)
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Success',
                icon: 'CheckIcon',
                variant: 'success',
                text: res.data.message,
              },
            })
            this.$router.push({ name: 'helper-affiliate-list' })
          } catch (err) {
            apiResponseHandler(this, err, {}, this.$refs.affiliateForm)
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
