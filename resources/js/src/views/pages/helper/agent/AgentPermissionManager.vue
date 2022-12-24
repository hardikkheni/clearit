<template>
  <b-card>
    <template #header>
      <b-container fluid>
        <b-row>
          <b-col class="clearfix" cols>
            <h2 class="float-left">Agent Permission Manager</h2>
            <!-- <b-button variant="outline-success"> Reset </b-button> -->
          </b-col>
        </b-row>
        <hr />
      </b-container>
    </template>
    <b-container fluid>
      <b-row>
        <b-col md="6">
          <b-form-group label="Viewing Agent:" label-cols-md="3">
            <b-form-select v-model="form.agentId" :options="allAgents" name="agentId" />
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-table responsive :items="allPermissions" :fields="columns">
            <template #cell(status)="{ item }">
              <b-form-checkbox
                :checked="form.permissions.includes(item.bitmaskValue)"
                switch
                @change="handleChanges($event, item.bitmaskValue)"
              />
            </template>
          </b-table>
        </b-col>
      </b-row>
      <!-- <router-link :to="{ name: 'dashboard' }" class="btn btn-outline-danger mr-1"> Cancel </router-link>
      <b-button variant="success" @click.prevent="saveChanges"> Save Changes </b-button> -->
    </b-container>
  </b-card>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BContainer,
  BFormGroup,
  BFormSelect,
  BTable,
  BFormCheckbox,
  //  BButton
} from 'bootstrap-vue'
import { mapActions, mapGetters, mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

import { mapBitMaskValue } from '@/utils/permissions'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BContainer,
    BFormGroup,
    BFormSelect,
    BTable,
    BFormCheckbox,
    // BButton,
  },
  data() {
    return {
      form: {
        agentId: null,
        permissions: [],
      },
      columns: [{ key: 'description', label: 'PERMISSION' }, { key: 'status' }],
    }
  },
  computed: {
    ...mapGetters('agent', { allAgents: 'allAgents' }),
    ...mapState('agent', { allPermissions: 'allPermissions' }),
  },
  watch: {
    // eslint-disable-next-line object-shorthand
    'form.agentId'() {
      const permissionBitmask = this.allAgents.find(e => e.id === this.form.agentId)?.permissionBitmask || 0
      this.form.permissions = mapBitMaskValue(
        this.allPermissions.map(i => i.bitmaskValue),
        permissionBitmask,
      )
    },
  },
  async mounted() {
    await this.getAllAgents()
    await this.getAllPermissions()
    this.form.agentId = this.allAgents[0]?.id
  },
  beforeDestroy() {},
  methods: {
    ...mapActions('agent', { getAllAgents: 'all', getAllPermissions: 'allPermissions', saveAgentPermissions: 'savePermissions' }),
    handleChanges($event, i) {
      const x = (this.form.permissions || []).includes(i)
      if (!x) {
        this.form.permissions.push(i)
      } else {
        this.form.permissions = [...this.form.permissions.filter(e => e !== i)]
      }
      this.saveChanges()
    },
    async saveChanges() {
      try {
        const res = await this.saveAgentPermissions(this.form)
        this.$toast({
          component: ToastificationContent,
          props: {
            title: 'Success',
            icon: 'CheckIcon',
            variant: 'success',
            text: res.message,
          },
        })
      } catch (err) {
        apiResponseHandler(this, err, {})
      }
    },
  },
}
</script>
