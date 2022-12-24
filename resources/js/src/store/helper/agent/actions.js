export default {
  async dataTable(_, params) {
    return (await this._vm.$api.post('/helper/agent/list', params, { cancelToken: 'DATATABLE' })).data.data
  },

  async create(_, payload) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/agent', payload)
  },

  async findById({ commit, rootState }, id) {
    commit('setState', { key: 'loading', value: true })
    try {
      const res = await this._vm.$api.get(`/helper/agent/${id}`)
      commit('setState', { key: 'loading', value: false })
      commit('setAgent', { data: res.data.data, roles: rootState.role.roles })
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      throw err
    }
  },
  async edit(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/agent/${id}`, data)
  },

  async all({ commit }) {
    try {
      const res = await this._vm.$api.get('/helper/agent/all-agents')
      commit('setState', { key: 'allAgents', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'allAgents', value: [] })
    }
  },

  async allInternalAgents({ commit }) {
    try {
      const res = await this._vm.$api.get('/helper/agent/all-internal-agents')
      commit('setState', { key: 'allInternalAgents', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'allInternalAgents', value: [] })
    }
  },

  async allPermissions({ commit }) {
    try {
      const res = await this._vm.$api.get('/helper/agent/all-permissions')
      commit('setState', { key: 'allPermissions', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'allPermissions', value: [] })
    }
  },
  async savePermissions(_, payload) {
    return (await this._vm.$api.post('/helper/agent/save-permissions', payload)).data
  },

  async getAgentStatusList({ commit }, payload) {
    try {
      commit('setState', { key: 'loading', value: true })
      const { data } = (await this._vm.$api.post('/helper/agent/status-list', payload)).data
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'agentStatusList', value: data || [] },
      ])
      return data
    } catch (err) {
      commit('setState', [
        { key: 'loading', value: false },
        { key: 'agentStatusList', value: [] },
      ])
      return {}
    }
  },
  async upsertAgentStatus(_, payload) {
    return (await this._vm.$api.post('/helper/agent/status', payload)).data
  },
  async deleteAgentStatus(_, id) {
    return (await this._vm.$api.delete(`/helper/agent/status/${id}`)).data
  },
  async reOrderAgentStatus(_, payload) {
    return (await this._vm.$api.post('/helper/agent/re-order-status-list', payload)).data
  },
}
