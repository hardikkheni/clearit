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
  setTicketView(state, data) {
    state.ticketView = data || {}
  },
  addNote(state, note) {
    const { notes } = state.ticketView
    state.ticketView.notes = [note, ...(Array.isArray(notes) ? notes : [])]
  },
}
