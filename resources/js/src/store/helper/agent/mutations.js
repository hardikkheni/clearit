import _ from 'lodash'

import { mapBitMaskValue } from '@/utils/permissions'

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
  setAgent(state, { data, roles }) {
    state.agent = {
      ...data,
      permissions: mapBitMaskValue(
        roles.map(i => i.bitmaskValue),
        data.roleBitmask,
      ),
    }
  },
}
