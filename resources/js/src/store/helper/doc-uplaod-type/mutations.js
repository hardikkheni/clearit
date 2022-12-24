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
  setDocUploadTypes(state, { data, roles }) {
    state.docUploadTypes = data.map(e => ({
      ...e,
      permissions: mapBitMaskValue(
        roles.map(i => i.bitmaskValue),
        e.roleBitmask,
      ),
    }))
  },
}
