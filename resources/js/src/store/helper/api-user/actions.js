export default {
  async dataTable(_, params) {
    return (await this._vm.$api.post('/helper/api-user/list', params, { cancelToken: 'DATATABLE' })).data.data
  },

  async create(_, payload) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/api-user', payload)
  },

  async findById({ commit }, id) {
    commit('setState', { key: 'loading', value: true })
    try {
      const res = await this._vm.$api.get(`/helper/api-user/${id}`)
      commit('setState', { key: 'loading', value: false })
      commit('setState', { key: 'apiUser', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      throw err
    }
  },

  async edit(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/api-user/${id}`, data)
  },
}
