export default {
  async get({ commit }) {
    try {
      const { data } = (await this._vm.$api.get('/helper/client-request/list')).data
      commit('setState', [
        { key: 'customerRequests', value: data.list },
        { key: 'count', value: data.count },
      ])
    } catch (err) {
      commit('setState', [
        { key: 'customerRequests', value: [] },
        { key: 'count', value: 0 },
      ])
    }
  },
  async markViewed(_, id) {
    return (await this._vm.$api.post(`/helper/client-request/mark-viewed/${id}`)).data
  },
  async bulkInsert(_, payload) {
    return (await this._vm.$api.post('/helper/client-request/bulk-insert', payload)).data
  },
  async maskAsReceived(_, id) {
    return (await this._vm.$api.post(`/helper/client-request/${id}/mark-as-received`)).data
  },
  async dailyMails({ commit }) {
    commit('setState', { key: 'loading', value: true })
    try {
      const {
        data: { data },
      } = await this._vm.$api.get('helper/client-request/daily-mails')
      commit('setState', { key: 'loading', value: false })
      commit('setRequests', data)
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      commit('setRequests', {})
    }
  },
}
