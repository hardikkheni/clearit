<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col sm="12" md="6" align-self="start">
            <h2 class="d-inline-block mr-1">Manage document upload types</h2>
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col col md="9" sm="12">
          <b-row>
            <b-col sm="12" md="6" align-self="start">
              <b-form-group label="Mode of transport:" label-cols-md="3">
                <b-form-select v-model="modeOfTranportId" :options="modeOfTranports" name="agentId" />
              </b-form-group>
            </b-col>
            <b-col class="text-md-right" sm="12" md="6" align-self="end">
              <b-button variant="success" @click.prevent="addDocUploadType">
                <feather-icon icon="PlusIcon" />
                Add New
              </b-button>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <b-row>
        <b-col col md="9" sm="12">
          <span>Move elements below to change their order:</span>
          <div class="mt-2">
            <template v-for="(doc, i) of docUploadTypes">
              <span :key="i" class="btn btn-outline-secondary bg-light-secondary btn-block text-left">
                {{ doc.document_type }}
                <feather-icon class="float-right" icon="XIcon" @click.prevent="remove(i)" />
                <feather-icon v-b-toggle:[`doc-${i}`] class="float-right mr-1" icon="EditIcon" />
                <b-collapse :id="`doc-${i}`" class="mt-1">
                  <validation-observer ref="docForm">
                    <b-form class="mt-2" @submit.prevent="submit($event, i)">
                      <!-- email -->
                      <b-form-group>
                        <validation-provider #default="{ errors }" name="Document Type" vid="document_type" rules="required">
                          <b-form-input
                            v-model="doc.document_type"
                            :state="errors.length > 0 ? false : null"
                            name="Document Type"
                            @change="submit($event, i)"
                          />
                          <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                      </b-form-group>

                      <b-form-group>
                        <b-form-checkbox
                          v-model="doc.is_required"
                          :value="true"
                          :unchecked-value="false"
                          name="is_required"
                          switch
                          inline
                          @change="submit($event, i)"
                        >
                          Required
                        </b-form-checkbox>
                        <b-form-checkbox
                          v-model="doc.show_customer"
                          :value="true"
                          :unchecked-value="false"
                          name="show_customer"
                          switch
                          inline
                          @change="submit($event, i)"
                        >
                          Show Customer
                        </b-form-checkbox>
                        <b-form-checkbox
                          v-model="doc.show_affiliate"
                          :value="true"
                          :unchecked-value="false"
                          name="show_affiliate"
                          switch
                          inline
                          @change="submit($event, i)"
                        >
                          Show Affiliate
                        </b-form-checkbox>
                        <b-form-checkbox
                          v-model="doc.show_freight_forwarder"
                          :value="true"
                          :unchecked-value="false"
                          name="show_freight_forwarder"
                          switch
                          inline
                          @change="submit($event, i)"
                        >
                          Show Freight Forwarder
                        </b-form-checkbox>
                      </b-form-group>

                      <b-form-group>
                        <b-form-textarea
                          v-model="doc.document_description"
                          placeholder="Description"
                          rows="3"
                          @change="submit($event, i)"
                        />
                      </b-form-group>

                      <b-form-group label="Assign Role(s) : must select atleast one">
                        <b-form-checkbox-group v-model="doc.permissions" stacked :options="userRoles" @change="submit($event, i)" />
                      </b-form-group>

                      <!-- submit buttons -->
                      <!-- <b-button type="submit" :variant="invalid ? 'danger' : 'success'" :disabled="invalid"> Save Changes </b-button> -->
                    </b-form>
                  </validation-observer>
                </b-collapse>
              </span>
            </template>
          </div>
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
  BButton,
  BFormSelect,
  VBToggle,
  BCollapse,
  BFormInput,
  BForm,
  BFormCheckbox,
  BFormTextarea,
  BFormCheckboxGroup,
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '@validations'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BButton,
    BFormGroup,
    BFormSelect,
    BCollapse,
    ValidationProvider,
    ValidationObserver,
    BFormInput,
    BForm,
    BFormCheckbox,
    BFormTextarea,
    BFormCheckboxGroup,
  },
  directives: {
    'b-toggle': VBToggle,
  },
  data() {
    return {
      modeOfTranportId: this.$route.params.mode,
      required,
    }
  },

  computed: {
    ...mapGetters('doc-upload-type', { modeOfTranports: 'modeOfTranports' }),
    ...mapState('doc-upload-type', { docUploadTypes: 'docUploadTypes', defaultDocUploadType: 'defaultDocUploadType' }),
    ...mapGetters('role', { userRoles: 'userRoles' }),
  },
  watch: {
    modeOfTranportId() {
      this.$router.push({
        name: this.$route.name,
        params: { mode: this.modeOfTranportId },
      })
    },
    // eslint-disable-next-line object-shorthand
    async '$route.params.mode'() {
      await this.getDocUploadTypesByMotId(this.$route.params.mode)
    },
  },
  async mounted() {
    await this.getModeOfTranports()
    await this.getUserRole()
    if (!this.$route.params.mode && this.modeOfTranports[0]?.value) {
      this.modeOfTranportId = this.modeOfTranports[0].value
    } else {
      await this.getDocUploadTypesByMotId(this.$route.params.mode)
    }
  },
  methods: {
    ...mapMutations('doc-upload-type', { setState: 'setState' }),
    ...mapActions('doc-upload-type', {
      getModeOfTranports: 'getModeOfTranports',
      getDocUploadTypesByMotId: 'getDocUploadTypesByMotId',
      deleteDUT: 'delete',
      upsertDUT: 'upsert',
    }),
    ...mapActions('role', { getUserRole: 'all' }),
    addDocUploadType() {
      this.setState({
        key: 'docUploadTypes',
        value: [{ ...this.defaultDocUploadType }, ...this.docUploadTypes],
      })
    },
    async submit(_, i) {
      try {
        const t = await this.upsertDUT({
          shipping_method: this.$route.params.mode,
          ...this.docUploadTypes[i],
        })
        // await this.getDocUploadTypesByMotId(this.$route.params.mode)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: t.data.message,
          },
        })
        // eslint-disable-next-line no-empty
      } catch (err) {
        apiResponseHandler(this, err, {}, this.$refs.docForm[i])
      }
    },
    async remove(i) {
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
          const doc = this.docUploadTypes[i]
          if (doc.id) {
            try {
              const t = await this.deleteDUT(doc.id)
              this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Success',
                  icon: 'CheckIcon',
                  variant: 'success',
                  text: t.message,
                },
              })
              await this.getDocUploadTypesByMotId(this.$route.params.mode)
              // eslint-disable-next-line no-empty
            } catch (err) {
              apiResponseHandler(this, err, {})
            }
          } else {
            this.setState({
              key: 'docUploadTypes',
              value: [...this.docUploadTypes.filter((_, e) => e !== i)],
            })
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
