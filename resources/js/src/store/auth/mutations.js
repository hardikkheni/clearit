import _ from 'lodash'

export default {
  setState(state, payload) {
    if (_.isArray(payload)) {
      payload.forEach(item => {
        _.set(state, item.key, item.value)
      })
    } else {
      _.set(state, payload.key, payload.value)
    }
  },
  setAuth(state, { user, accessToken }) {
    state.isAuthenticated = true
    state.user = user
    this._vm.$jwt.setToken(accessToken)
  },
  setError(state, error) {
    state.errors = error
  },
  purgeAuth(state) {
    state.isAuthenticated = false
    state.user = {}
    state.errors = {}
    this._vm.$jwt.clearToken()
  },
  setDashboard(state, data) {
    state.loading = data.loading || false
    state.list = data.list || []
    state.userRoles = data.roles || []
    state.ticketStatusList = data.ticketStatusList || []
    state.agents = data.agents || []
    state.agentStatusTypes = data.agentStatusTypes || []
    state.affiliates = data.affiliates || []
    state.count = data.count || 0
    state.limit = data.limit || 0
    state.alibabaId = data.alibabaId || 0
    state.freightosId = data.freightosId || 0
  },
}
