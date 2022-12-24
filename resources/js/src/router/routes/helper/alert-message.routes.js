import { PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES } from '@/utils/permissions'

export default [
  {
    path: '/helper/alert-message/create',
    name: 'create-helper-alert-message',
    component: () => import('@/views/pages/helper/alert-message/CreateAlertMessage.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES],
    },
  },
  {
    path: '/helper/alert-message/list',
    name: 'helper-alert-message-list',
    component: () => import('@/views/pages/helper/alert-message/AlertMessageList.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES],
    },
  },
  {
    path: '/helper/alert-message/:id',
    name: 'edit-helper-alert-message',
    component: () => import('@/views/pages/helper/alert-message/EditAlertMessage.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES],
    },
  },
]
