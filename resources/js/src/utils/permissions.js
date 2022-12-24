/* eslint-disable no-bitwise */
/* eslint-disable implicit-arrow-linebreak */
/* eslint-disable operator-linebreak */
import { mapGetters } from 'vuex'

export default {}

export const hasBitMaskValue = (perm, bitMaskValue) => (bitMaskValue & perm) === +perm

export const mapBitMaskValue = (perms, bitMaskValue) => (perms || []).filter(i => hasBitMaskValue(i, bitMaskValue))

export const PERMISSION_BITMASK_AGENTS = 1
export const PERMISSION_BITMASK_AGENT_STATUSES = 2
export const PERMISSION_BITMASK_TICKET_STATUSES = 4
export const PERMISSION_BITMASK_VIEW_MESSAGES = 8
export const PERMISSION_BITMASK_AFFILIATES = 16
export const PERMISSION_BITMASK_TO_DO_CHECKLISTS = 32
export const PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES = 64
export const PERMISSION_BITMASK_DOC_UPLOAD_TYPES = 128
export const PERMISSION_BITMASK_VIEW_NOTIFICATIONS = 256
export const PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES = 512
// export const PERMISSION_BITMASK_ROLES = 1024 /* "Not added to the DB yet" */

export const permissionMixin = {
  computed: {
    ...mapGetters('auth', { user: 'currentUser' }),
    checkPermission() {
      return link =>
        (link.permissions || []).some(i => hasBitMaskValue(i, this.user.permissionBitmask)) ||
        !link.needToBeMaster ||
        (!!link.needToBeMaster && this.user.isMaster)
    },
  },
}
