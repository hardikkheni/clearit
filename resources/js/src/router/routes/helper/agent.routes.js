import { PERMISSION_BITMASK_AGENTS, PERMISSION_BITMASK_AGENT_STATUSES } from '@/utils/permissions'

export default [
  {
    path: '/helper/agent/create',
    name: 'create-helper-agent',
    component: () => import('@/views/pages/helper/agent/CreateAgent.vue'),
    meta: {
      // pageTitle: 'Create Agent',
      permissions: [PERMISSION_BITMASK_AGENTS],
    },
  },

  {
    path: '/helper/agent/list',
    name: 'helper-agent-list',
    component: () => import('@/views/pages/helper/agent/AgentList.vue'),
    meta: {
      // pageTitle: 'Agent List',
      permissions: [PERMISSION_BITMASK_AGENTS],
    },
  },
  {
    path: '/helper/agent/permission-manager',
    name: 'edit-helper-agent-permission-manager',
    component: () => import('@/views/pages/helper/agent/AgentPermissionManager.vue'),
    meta: {
      needToBeMaster: true,
    },
  },
  {
    path: '/helper/agent/:id',
    name: 'edit-helper-agent',
    component: () => import('@/views/pages/helper/agent/EditAgent.vue'),
    meta: {
      // pageTitle: 'Agent List',
      permissions: [PERMISSION_BITMASK_AGENTS],
    },
  },
  {
    path: '/helper/agent-status-list/:type?/:role?',
    name: 'helper-agent-status-list',
    component: () => import('@/views/pages/helper/agent/AgentStatusList.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AGENT_STATUSES],
    },
  },
]
