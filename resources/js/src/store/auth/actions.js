export default {
  async login({ commit }, payload) {
    commit('setState', {
      key: 'loading',
      value: true,
    })
    try {
      const response = await this._vm.$api.post('/auth/login', payload)
      commit('setState', {
        key: 'loading',
        value: false,
      })
      commit('setAuth', {
        user: response.data.data.user,
        accessToken: response.data.data.access_token,
      })
    } catch (err) {
      commit('setState', {
        key: 'loading',
        value: false,
      })
      throw err
    }
  },
  async logout({ commit }) {
    // await this.$api.post('/auth/delete')
    commit('purgeAuth')
  },
  async verifyAuth({ commit, state }) {
    const vm = this._vm
    if (!state.isAuthenticated && vm.$jwt.getToken()) {
      try {
        const { data } = await vm.$api.get('/auth/profile')
        commit('setAuth', {
          user: data.data.user,
          accessToken: vm.$jwt.getToken(),
        })
        return true
      } catch ({ response }) {
        commit('purgeAuth')
        return false
      }
    } else if (!vm.$jwt.getToken()) {
      commit('purgeAuth')
      return false
    }
    return true
  },

  async getTicketStatus2({ commit }) {
    try {
      const roles = (await this._vm.$api.get('/resource/ticket-status2')).data.data
      commit('setState', { key: 'ticketStatus2', value: roles })
    } catch (err) {
      commit('setState', { key: 'ticketStatus2', value: [] })
    }
  },

  async dashboard({ commit }, payload = {}) {
    commit('setState', { key: 'loading', value: true })
    try {
      const {
        data: { data },
      } = await this._vm.$api.post('dashboard', payload)
      commit('setDashboard', data)
    } catch (err) {
      commit('setDashboard', {})
    }
  },
}
