import affiliateRoutes from './affiliate.routes'
import agentRoutes from './agent.routes'
import alertMessageRoutes from './alert-message.routes'
import apiUserRoutes from './api-user.routes'
import docUploadTypeRoutes from './doc-upload-type.routes'
import freightForwarderRoutes from './freight-forwarder.routes'
import customerRoutes from './customer.routes'

import {
  PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES,
  PERMISSION_BITMASK_TO_DO_CHECKLISTS,
  PERMISSION_BITMASK_TICKET_STATUSES,
} from '@/utils/permissions'

export default [
  {
    path: '/helper/administration',
    name: 'helper-administration',
    component: () => import('@/views/pages/helper/Administration.vue'),
    meta: {},
  },
  {
    path: '/helper/role-status',
    name: 'helper-role-status',
    component: () => import('@/views/pages/helper/RoleStatus.vue'),
    meta: {
      needToBeMaster: true,
    },
  },
  {
    path: '/helper/role-managment',
    name: 'helper-role-managment',
    component: () => import('@/views/pages/helper/RoleManagement.vue'),
    meta: {},
  },
  {
    path: '/helper/freightos-billing',
    name: 'helper-freightos-billing',
    component: () => import('@/views/pages/helper/FreightosBilling.vue'),
    meta: {},
  },
  {
    path: '/helper/ticket-status-dependencies/:type?/:transport?/:status?',
    name: 'helper-ticket-status-depends',
    component: () => import('@/views/pages/helper/ManageTicketStatusDepends.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES],
    },
  },
  {
    path: '/helper/ticket-status/:type?/:transport?',
    name: 'helper-ticket-status-list',
    component: () => import('@/views/pages/helper/ticket/TicketStatusList.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_TICKET_STATUSES],
    },
  },
  {
    path: '/helper/to-do-ticket-item-list/:type?/:transport?/:role?',
    name: 'helper-to-do-ticket-item-list',
    component: () => import('@/views/pages/helper/ToDoTicketItemList.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_TO_DO_CHECKLISTS],
    },
  },
  {
    path: '/helper/reminders/:filter?',
    name: 'helper-reminders',
    component: () => import('@/views/pages/helper/MyReminders.vue'),
  },
  {
    path: '/helper/daily-client-requests',
    name: 'helper-daily-client-requests',
    component: () => import('@/views/pages/helper/DailyClientRequests.vue'),
  },
  {
    path: '/helper/daily-reports/:report?',
    name: 'helper-daily-reports',
    component: () => import('@/views/pages/helper/daily-reports/DailyReports.vue'),
  },
  {
    path: '/helper/notification',
    name: 'helper-notification-list',
    component: () => import('@/views/pages/helper/NotificationView.vue'),
  },
  {
    path: '/helper/customer-request',
    name: 'helper-customer-request-list',
    component: () => import('@/views/pages/helper/CustomerRequestList.vue'),
  },
  {
    path: '/helper/customer-unverified-with-poa/:all?/:affiliateId?',
    name: 'helper-customer-unverified-with-poa-list',
    component: () => import('@/views/pages/helper/CustomerUnverifiedWithPoaList.vue'),
  },
  ...agentRoutes,
  ...affiliateRoutes,
  ...alertMessageRoutes,
  ...apiUserRoutes,
  ...docUploadTypeRoutes,
  ...freightForwarderRoutes,
  ...customerRoutes,
]
