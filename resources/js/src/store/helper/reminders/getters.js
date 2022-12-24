export default {
  listAgents(state) {
    return (state.agents || []).map(e => ({ ...e, value: e.id, text: `${e.firstname} ${e.lastname}` }))
  },
}
