export default {
  allAgents(state) {
    return state.allAgents.map(e => ({ ...e, value: e.id, text: [e.firstname, e.lastname].join(', ') }))
  },
  allInternalAgents(state) {
    return state.allInternalAgents.map(e => ({ ...e, value: e.id, text: [e.firstname, e.lastname].join(' ') }))
  },
  agentStatusList(state) {
    return (state.agentStatusList || []).map(i => ({ ...i, value: i.id, text: i.statusName }))
  },
}
