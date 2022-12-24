export default {
  modeOfTranports(state) {
    return state.modeOfTranports.map(e => ({ ...e, text: e.description, value: e.id }))
  },
}
