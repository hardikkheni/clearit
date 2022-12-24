<template>
  <b-row>
    <b-col xs="12" sm="4">
      <b-avatar square size="56px" :src="require('@/assets/images/icons/ticket-agent.png')" /> <span class="h2 ml-1">Agents</span>
    </b-col>
    <b-col xs="12" sm="8">
      <b-form-group label-cols-lg="4" content-cols-lg="8" label="Customer Service:">
        <b-form-select :value="agentGuid" :options="agentOptions" @change="handleAgentChange">
          <template #first>
            <b-form-select-option :value="null">Select Agent</b-form-select-option>
          </template>
        </b-form-select>
      </b-form-group>
      <b-form-group label-cols="4" content-cols-lg="8" label="Processing Agent:">
        <b-form-select :value="processingAgentGuid" :options="agentOptions" @change="handleProcessingAgentChange">
          <template #first>
            <b-form-select-option :value="null">Select Agent</b-form-select-option>
          </template>
        </b-form-select>
      </b-form-group>
    </b-col>
  </b-row>
</template>

<script>
import { BRow, BCol, BAvatar, BFormGroup, BFormSelect, BFormSelectOption } from 'bootstrap-vue'
import { mapActions } from 'vuex'

import { apiResponseHandler } from '@/libs/api.handler'

export default {
  components: {
    BRow,
    BCol,
    BAvatar,
    BFormGroup,
    BFormSelect,
    BFormSelectOption,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    agents: {
      type: Array,
      required: true,
    },
    agent: {
      type: Object,
      required: true,
    },
    processingAgent: {
      type: Object,
      required: true,
    },
  },
  computed: {
    agentOptions() {
      return this.agents.map(i => ({ ...i, value: i.guid, text: `${i.firstname} ${i.lastname}` }))
    },
    agentGuid() {
      return this.agent.guid || null
    },
    processingAgentGuid() {
      return this.processingAgent.guid || null
    },
  },
  methods: {
    ...mapActions('ticket', { updateAgent: 'updateAgent', updateProcessingAgent: 'updateProcessingAgent' }),
    async handleAgentChange(agentGuid) {
      try {
        const res = await this.updateAgent({ id: this.ticket.id, agentid: agentGuid })
        this.$emit('change')
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
      } catch (err) {
        apiResponseHandler(this, err)
      }
    },
    async handleProcessingAgentChange(processingAgentGuid) {
      try {
        const res = await this.updateProcessingAgent({ id: this.ticket.id, processingAgentId: processingAgentGuid })
        this.$emit('change')
        apiResponseHandler(
          this,
          {},
          {
            toast: {
              title: 'Success',
              icon: 'CheckIcon',
              variant: 'success',
              text: res.message,
            },
          },
        )
      } catch (err) {
        apiResponseHandler(this, err)
      }
    },
  },
}
</script>
