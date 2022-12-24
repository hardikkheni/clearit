export default {
  async get({ commit }, isAffiliate) {
    try {
      const { data } = (await this._vm.$api.get(`/helper/notification/list?is_affiliate=${isAffiliate}`)).data
      commit('setState', [
        { key: 'notifications', value: data.list },
        { key: 'count', value: data.count },
      ])
    } catch (err) {
      commit('setState', [
        { key: 'notifications', value: [] },
        { key: 'count', value: 0 },
      ])
    }
  },
  async markViewed(_, payload) {
    return (await this._vm.$api.post('/helper/notification/mark-viewed', payload)).data
  },
}
