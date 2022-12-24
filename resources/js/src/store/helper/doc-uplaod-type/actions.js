export default {
  async upsert(_, payload) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/document-upload-type', payload)
  },

  async getModeOfTranports({ commit }) {
    try {
      const res = await this._vm.$api.get('/helper/document-upload-type/mode-of-transport-list')
      commit('setState', { key: 'modeOfTranports', value: res.data.data })
      // eslint-disable-next-line no-empty
    } catch (err) {}
  },

  async getDocUploadTypesByMotId({ commit, rootState }, id) {
    try {
      const res = await this._vm.$api.get(`/helper/document-upload-type/${id}`)
      commit('setDocUploadTypes', { roles: rootState.role.roles, data: res.data.data })
    } catch (err) {
      commit('setDocUploadTypes', [])
    }
  },

  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/helper/document-upload-type/${id}`)).data
  },
}
