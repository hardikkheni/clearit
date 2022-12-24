import { formify } from '@/utils/form'

export default {
  async getTicketStatusDependencies({ commit }, payload) {
    try {
      commit('setState', { key: 'loading', value: true })
      const { data } = (await this._vm.$api.post('/ticket/status-dependencies', payload)).data
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'ticketStatusList', value: data.ticketStatusList || [] },
        { key: 'toDoTicketItemList', value: data.toDoTicketItemList || [] },
      ])
      return data
    } catch (err) {
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'ticketStatusList', value: [] },
        { key: 'toDoTicketItemList', value: [] },
      ])
      return {}
    }
  },
  async putTicketStatusDependencies(_, payload) {
    return (await this._vm.$api.put('/ticket/status-dependencies', payload)).data
  },
  async getToDoTicketItemList({ commit }, payload) {
    try {
      commit('setState', { key: 'loading', value: true })
      const { data } = (await this._vm.$api.post('/helper/ticket/to-do-ticket-item-list', payload)).data
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'toDoTicketItemList', value: data || [] },
      ])
      return data
    } catch (err) {
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'toDoTicketItemList', value: [] },
      ])
      return {}
    }
  },
  async upsertToDoTicketItem(_, payload) {
    return (await this._vm.$api.post('/helper/ticket/to-do-ticket-item', payload)).data
  },
  async deleteToDoTicketItem(_, id) {
    return (await this._vm.$api.delete(`/helper/ticket/to-do-ticket-item/${id}`)).data
  },
  async reOrderTodoTicketItem(_, payload) {
    return (await this._vm.$api.post('/helper/ticket/re-order-to-do-ticket-item-list', payload)).data
  },
  async getTicket({ commit }, { role, guid }) {
    try {
      commit('setState', { key: 'loading', value: true })
      // await new Promise(res => setTimeout(res, 5000))
      const { data } = (await this._vm.$api.get(`/ticket/${role}/${guid}`)).data
      commit('setState', [{ key: 'loading', value: false }])
      commit('setTicketView', data)
    } catch (err) {
      commit('setState', [{ key: 'loading', value: false }])
      commit('setTicketView', {})
      throw err
    }
  },

  async getTicketStatusList({ commit }, payload) {
    try {
      commit('setState', { key: 'loading', value: true })
      const { data } = (await this._vm.$api.post('/helper/ticket/status-list', payload)).data
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'ticketStatusList', value: data || [] },
      ])
      return data
    } catch (err) {
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'ticketStatusList', value: [] },
      ])
      return {}
    }
  },
  async upsertTicketStatus(_, payload) {
    return (await this._vm.$api.post('/helper/ticket/status', payload)).data
  },
  async deleteTicketStatus(_, id) {
    return (await this._vm.$api.delete(`/helper/ticket/status/${id}`)).data
  },
  async reOrderTicketStatus(_, payload) {
    return (await this._vm.$api.post('/helper/ticket/re-order-status-list', payload)).data
  },
  async updateRequireBrokerReview({ commit }, { id, requires_broker_review }) {
    try {
      const { data } = (
        await this._vm.$api.post(`/ticket/${id}/update-require-broker-review`, {
          requires_broker_review,
        })
      ).data
      commit('setState', [{ key: 'viewTicketAlert', value: data.alert }])
      return data
    } catch (err) {
      commit('setState', [{ key: 'viewTicketAlert', value: null }])
      throw err
    }
  },
  async patch(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.patch(`/ticket/${id}`, data)).data
  },
  async updateStatus(_, { id, statusId }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/update-status`, { statusId })).data
  },
  async updateAgentStatusType({ dispatch }, { id, agentStatusTypeId }) {
    // eslint-disable-next-line no-return-await
    return await dispatch('patch', {
      id,
      data: {
        agentStatusTypeId,
      },
    })
  },
  async updateAgent(_, { id, agentid }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/update-agent`, { agentid })).data
  },
  async updateProcessingAgent(_, { id, processingAgentId }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/update-processing-agent`, { processingAgentId })).data
  },
  async delete(_, { id, reason }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/delete`, { reason })).data
  },
  async addAffiliateReferance(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/add-affiliate-referance`, data)).data
  },
  async removeAffiliateReferance(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/remove-affiliate-referance`)).data
  },
  async addCarrierDetails(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/add-carrier-details`, data)).data
  },
  async addEta(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/add-eta`, data)).data
  },
  async addBilling(_, { id, data }) {
    const form = formify(data)
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/add-billing`, form)).data
  },
  async markAsPaid(_, { id, payId }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/mark-as-paid/${payId}`)).data
  },
  async removePayment(_, { id, payId }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/ticket/${id}/payment/${payId}`)).data
  },

  async addPgaRequest(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/add-pga-request`, data)).data
  },
  async attachUserHts(_, { ticketId, uhtsId }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${ticketId}/attach-user-hts`, { uhtsId })).data
  },
  async notifyTariffCodeEmail(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/send-notify-tariff-code-email`, data)).data
  },
  async updateNotifyTariffCode(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket/${id}/update-notify-tariff-code`, data)).data
  },
}
