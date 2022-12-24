export default {
  allAffiliates(state) {
    return state.allAffiliates.map(i => ({ ...i, value: i.id, text: i.companyname }))
  },
}
