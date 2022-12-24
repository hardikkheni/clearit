import _ from 'lodash'

export default {
  getState(store) {
    return key => _.get(store, key)
  },
  currentUser(state) {
    return state.user
  },
  isAuthenticated(state) {
    return state.isAuthenticated
  },
  ticketStatus2(state) {
    return state.ticketStatus2.map(e => ({ ...e, text: e.statusName, value: e.bitmaskValue }))
  },
  dashRoles(state) {
    return (state.userRoles || []).map(e => ({ ...e, value: e.internalKey, text: e.name }))
  },
  dashAgentStatus(state) {
    return (state.agentStatusTypes || []).map(e => ({ ...e, value: e.id, text: `${e.name || `${e.statusName} (${e.ticketType})`}` }))
  },
  dashAffiliates(state) {
    return (state.affiliates || []).map(e => ({ ...e, value: e.id, text: e.companyname }))
  },
  dashAgents(state) {
    return (state.agents || []).map(e => ({ ...e, value: e.guid, text: `${e.firstname} ${e.lastname}` }))
  },
  dashTicketStatusList(state) {
    return (state.ticketStatusList || []).map(e => ({ ...e, value: e.status, text: `${e.statusname} (${e.count})` }))
  },
}
