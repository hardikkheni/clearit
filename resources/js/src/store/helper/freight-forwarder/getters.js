export default {
  // TODO: move to agents module
  allAgents(state) {
    return state.allAgents.map(e => ({ ...e, value: e.id, text: [e.firstname, e.lastname].join(', ') }))
  },
  contactsOfForwarder(state) {
    return state.freightContactsOfForwarder.map(i => ({ ...i, text: i.isfcName, value: i.id }))
  },
}
