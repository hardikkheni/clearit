import _ from 'lodash'

export default {
  getState(store) {
    return key => _.get(store, key)
  },
  ticketStatusList(state) {
    return (state.ticketStatusList || []).map(i => ({ ...i, value: i.id, text: i.statusName }))
  },
  toDoTicketItemList(state) {
    return (state.toDoTicketItemList || []).map(i => ({ ...i, value: i.id, text: i.itemName }))
  },
  agentRoleStatusList(state) {
    return (state.ticketView?.getFullTicket?.agent_status || []).map(i => ({ ...i, text: i.agentStatusName, value: i.agentStatusTypeId }))
  },
}
