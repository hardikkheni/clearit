import { formify } from '@/utils/form'

export default {
  async dataTable(_, params) {
    return (await this._vm.$api.post('/helper/affiliate/list', params, { cancelToken: 'DATATABLE' })).data.data
  },

  async create(_, data) {
    const form = formify(data)

    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/affiliate', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  async registerAffiliate(_, data) {
    const form = formify(data)

    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post('/helper/affiliate/register-affiliate', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  async findById({ commit }, id) {
    commit('setState', { key: 'loading', value: true })
    try {
      const res = await this._vm.$api.get(`/helper/affiliate/${id}`)
      // remove image name from response
      const { logofilename, ...affiliate } = res.data.data
      commit('setState', { key: 'loading', value: false })
      commit('setState', { key: 'affiliate', value: affiliate })
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      throw err
    }
  },

  async edit(_, { id, data }) {
    const form = formify(data)

    // eslint-disable-next-line no-return-await
    return await this._vm.$api.post(`/helper/affiliate/${id}`, form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return await this._vm.$api.delete(`/helper/affiliate/${id}`)
  },

  async all({ commit }) {
    try {
      const res = await this._vm.$api.get('/helper/affiliate/all-affiliates')
      commit('setState', { key: 'allAffiliates', value: res.data.data })
    } catch (err) {
      commit('setState', { key: 'allAffiliates', value: [] })
    }
  },
}
