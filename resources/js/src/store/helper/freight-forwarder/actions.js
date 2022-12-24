export default {
  async dataTable(_, params) {
    return (await this._vm.$api.post('/helper/freight-forwarder/list', params, { cancelToken: 'DATATABLE' })).data.data
  },

  async create(_, payload) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/freight-forwarder', payload)
  },

  async findById({ commit }, id) {
    commit('setState', { key: 'loading', value: true })
    try {
      const res = await this._vm.$api.get(`/helper/freight-forwarder/${id}`)
      commit('setState', { key: 'loading', value: false })
      commit('setState', { key: 'freightForwarder', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      throw err
    }
  },
  async edit(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/freight-forwarder/${id}`, data)
  },
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.delete(`/helper/freight-forwarder/${id}`)
  },

  async createContact(_, { ffId, payload }) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/freight-contact/${ffId}`, payload)
  },

  async findContactById({ commit }, id) {
    // eslint-disable-next-line no-return-await
    commit('setState', { key: 'loading', value: true })
    try {
      const res = await this._vm.$api.get(`/helper/freight-contact/${id}`)
      commit('setState', { key: 'loading', value: false })
      commit('setState', { key: 'freightContact', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      throw err
    }
  },

  async editContact(_, { ffId, id, data }) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/freight-contact/${ffId}/${id}`, data)
  },

  async deleteContact(_, id) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.delete(`/helper/freight-contact/${id}`)
  },
  async getContactsOfForForwarder({ commit }, id) {
    if (!id) {
      commit('setState', { key: 'freightContactsOfForwarder', value: [] })
      return []
    }
    // eslint-disable-next-line no-return-await
    try {
      const res = (await this._vm.$api.get(`/helper/freight-forwarder/${id}/contacts`)).data
      commit('setState', { key: 'freightContactsOfForwarder', value: res.data })
      return res.data
    } catch (err) {
      commit('setState', { key: 'freightContactsOfForwarder', value: [] })
      return []
    }
  },
}
