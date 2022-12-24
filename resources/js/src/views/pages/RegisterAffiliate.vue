<template>
  <b-card>
    <template #header>
      <b-container>
        <b-row>
          <b-col class="clearfix" col lg="5" md="6" xs="12">
            <img :src="affiliate.logo_url" height="84px">
          </b-col>
          <b-col col lg="5" md="6" xs="12">
            <span class="text-right fixed-bottom position-absolute"><span class="text-danger">*</span>Required fields</span>
          </b-col>
        </b-row>
      </b-container>
    </template>
    <b-container>
      <b-row>
        <b-col col md="12">
          Please enter your customer's name, email address and upload the commercial invoice and arrival notice
        </b-col>
      </b-row>
      <b-row>
        <b-col col md="12">
          <validation-observer ref="affiliateForm" #default="{ invalid }">
            <b-form class="auth-login-form mt-2" @submit.prevent="submit">
              <!-- email -->
              <b-form-group label="Email Address" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <validation-provider #default="{ errors }" name="Email Address" vid="email" rules="required|email">
                  <b-form-input v-model="form.email" :state="errors.length > 0 ? false : null" name="email" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="First Name" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <b-form-input v-model="form.firstname" name="fname" />
              </b-form-group>

              <b-form-group label="Last Name" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <b-form-input v-model="form.lastname" name="lname" />
              </b-form-group>

              <b-form-group label="Business Key" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <b-form-input v-model="form.business_key" name="businessKey" />
              </b-form-group>

              <b-form-group label="Shipment Number" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <b-form-input v-model="form.shipment_number" name="shipmentnumber" />
              </b-form-group>

              <b-form-group label="Bond Information" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <validation-provider #default="{ errors }" name="Bond Information" vid="bond" rules="required">
                  <b-form-radio-group v-model="form.bond" :options="bondOptions" name="bond" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <b-form-group label="Mode of transport" label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <validation-provider #default="{ errors }" name="Mode of transport" vid="transport" rules="required">
                    <template v-for="(option, i) in tranportOptions">
                      <b-form-radio-group :key="i" v-model="form.transport" name="transport" buttons button-variant="outline-primary">
                        <b-form-radio :value="i" class="mr-1">
                          <b-icon icon="option.icon" />
                          {{ option.label }}
                        </b-form-radio>
                      </b-form-radio-group>
                    </template>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <b-form-group label-cols-sm="4"
                label-cols-lg="3"
                content-cols-sm
                content-cols-lg="7"
              >
                <b-row class="mb-1">
                  <b-col md="12">
                    <span>Commercial Invoice Upload</span>
                    <b-form-file
                      v-model="form.doc_invoice_input"
                      placeholder="Choose a file or drop it here..."
                      drop-placeholder="Drop file here..."
                      accept="image/*"
                    />
                  </b-col>
                </b-row>
                <b-row class="mb-1">
                  <b-col md="12">
                    <span>Bill of Lading</span>
                    <b-form-file
                      v-model="form.doc_bill_of_lading_input"
                      placeholder="Choose a file or drop it here..."
                      drop-placeholder="Drop file here..."
                      accept="image/*"
                    />
                  </b-col>
                </b-row>
                <b-row v-if="form.transport == 2">
                  <b-col md="12">
                    <span>ISF</span>
                    <b-form-file
                      v-model="form.doc_isf_input"
                      placeholder="Choose a file or drop it here..."
                      drop-placeholder="Drop file here..."
                      accept="image/*"
                    />
                  </b-col>
                </b-row>
                <b-row v-else-if="form.transport == 1">
                  <b-col md="12">
                    <span>PAPs</span>
                    <b-form-file
                      v-model="form.doc_paps_input"
                      placeholder="Choose a file or drop it here..."
                      drop-placeholder="Drop file here..."
                      accept="image/*"
                    />
                  </b-col>
                </b-row>

              </b-form-group>
              <!-- submit buttons -->
              <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid">
                Save Changes
              </b-button>
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
  BContainer,
  BFormFile,
  BFormRadioGroup,
  BIcon,
  BFormRadio,
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
    BForm,
    BButton,
    BRow,
    BCol,
    BContainer,
    BFormRadioGroup,
    BIcon,
    BFormRadio,
  },
  data() {
    return {
      required,
      email,
      APP_URL: process.env.APP_URL,
      bondOptions: {
        1: 'Customer already has a bond',
        2: 'Annual bond',
        3: 'Single entry bond',
      },
      tranportOptions: {
        1: {
          label: 'Truck',
          icon: 'truck',
        },
        2: {
          label: 'Ocean',
          icon: 'ocean',
        },
        3: {
          label: 'Air',
          icon: 'air',
        },
      },
      form: {
        email: null,
        firstname: null,
        lastname: null,
        business_key: null,
        shipment_number: null,
        bond: null,
        transport: null,
        doc_invoice_input: null,
        doc_bill_of_lading_input: null,
        doc_isf_input: null,
        doc_paps_input: null,
        affiliate_id: null,
      },
    }
  },
  computed: {
    ...mapState('affiliate', { affiliate: 'affiliate', defaultAffiliate: 'defaultAffiliate' }),
  },
  mounted() {
    this.getAffiliate(this.$route.params.affiliateId)
    console.log(this.affiliate)
    this.form.affiliate_id = this.affiliate.id
  },
  beforeDestroy() {
    this.setState({ key: 'affiliate', value: { ...this.defaultAffiliate } })
  },
  methods: {
    ...mapMutations('affiliate', { setState: 'setState' }),
    ...mapActions('affiliate', { getAffiliate: 'findById', registerAffiliate: 'registerAffiliate' }),
    async submit() {
      this.form.affiliate_id = this.affiliate.id
      try {
        const t = await this.registerAffiliate(this.form)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        // this.$router.push({ name: 'helper-affiliate-list' })
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.affiliateForm)
      }
    },
  },
}
</script>
