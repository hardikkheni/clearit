export default {
  async reminders({ commit }, payload = {}) {
    commit('setState', { key: 'loading', value: true })
    try {
      const {
        data: { data },
      } = await this._vm.$api.get(`helper/reminder/${payload.filter}`)
      commit('setState', { key: 'loading', value: false })
      commit('setReminders', data)
    } catch (err) {
      commit('setState', { key: 'loading', value: false })
      commit('setReminders', {})
    }
  },
  async edit(_, { id, data }) {
    const form = {
      ticketId: data.ticketId,
      assignedToAgentId: data.assignedToUserId,
      dueOnDate: data.dueon_format_date_y_m_d,
      dueOnTime: data.dueon_format_time,
      message: data.message,
    }
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/helper/reminder/${id}`, form)).data
  },
  async create(_, payload) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post('/helper/reminder', payload)).data
  },
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/helper/reminder/${id}`)).data
  },
  async completeReminders(_, data) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.patch('/helper/reminder', { id: data })).data
  },
}
