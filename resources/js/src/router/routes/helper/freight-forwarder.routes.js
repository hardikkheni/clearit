import { PERMISSION_BITMASK_AFFILIATES } from '@/utils/permissions'

export default [
  {
    path: '/helper/freight-forwarder/create',
    name: 'create-helper-freight-forwarder',
    component: () => import('@/views/pages/helper/freight-forwarder/CreateFreightForwarder.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },

  {
    path: '/helper/freight-forwarder/list',
    name: 'helper-freight-forwarder-list',
    component: () => import('@/views/pages/helper/freight-forwarder/FreightForwarderList.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },
  {
    path: '/helper/freight-forwarder/:id',
    name: 'edit-helper-freight-forwarder',
    component: () => import('@/views/pages/helper/freight-forwarder/EditFreightForwarder.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },

  {
    path: '/helper/freight-contact/:ffId',
    name: 'create-helper-freight-contact',
    component: () => import('@/views/pages/helper/freight-forwarder/CreateFreightContact.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },

  {
    path: '/helper/freight-contact/:ffId/:id',
    name: 'edit-helper-freight-contact',
    component: () => import('@/views/pages/helper/freight-forwarder/EditFreightContact.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },
]
