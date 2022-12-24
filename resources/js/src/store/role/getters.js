import _ from 'lodash'

export default {
  getState(store) {
    return key => _.get(store, key)
  },
  userRoleOptionsByIds(state) {
    return (state.roles || []).map(e => ({ ...e, text: e.name, value: e.id }))
  },
  userRoles(state) {
    return (state.roles || []).map(e => ({ ...e, text: e.name, value: e.bitmaskValue }))
  },
  dashRoles(state) {
    return (state.roles || []).map(e => ({ ...e, value: e.internalKey, text: e.name }))
  },
}
