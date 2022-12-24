export default {
  async all({ commit }) {
    try {
      const roles = (await this._vm.$api.get('/resource/user-roles')).data.data
      commit('setState', { key: 'roles', value: roles })
    } catch (err) {
      commit('setState', { key: 'roles', value: [] })
    }
  },

  async updateUserRolePermissions(_, payload) {
    return (await this._vm.$api.post('/user-role/permissions', payload)).data
  },
  async getRoleAgents(_, id) {
    return (await this._vm.$api.get(`/user-role/${id}/agents`)).data
  },
  async grantRevokeAgentFromRole(_, payload) {
    return (await this._vm.$api.post('/user-role/grant-revoke-agent-role', payload)).data
  },
}
