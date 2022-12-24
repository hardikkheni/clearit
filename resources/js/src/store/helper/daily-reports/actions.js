export default {
  async loadDailyReport({ commit }, payload = {}) {
    try {
      const {
        data: { data },
      } = await this._vm.$api.get('/helper/daily-reports', { params: payload })
      commit('setState', { key: 'list', value: data[0] })
      commit('setState', { key: 'count', value: data[1][0].COUNT })
    } catch (err) {
      commit('setState', { key: 'list', value: [] })
    }
  },
}
