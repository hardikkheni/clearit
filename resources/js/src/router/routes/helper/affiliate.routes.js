import { PERMISSION_BITMASK_AFFILIATES } from '@/utils/permissions'

export default [
  {
    path: '/helper/affiliate/create',
    name: 'create-helper-affiliate',
    component: () => import('@/views/pages/helper/affiliate/CreateAffiliate.vue'),
    meta: {
      // pageTitle: 'Create Affiliate',
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },
  {
    path: '/helper/affiliate/list',
    name: 'helper-affiliate-list',
    component: () => import('@/views/pages/helper/affiliate/AffiliateList.vue'),
    meta: {
      // pageTitle: 'Affiliate List',
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },
  {
    path: '/helper/affiliate/:id',
    name: 'edit-helper-affiliate',
    component: () => import('@/views/pages/helper/affiliate/EditAffiliate.vue'),
    meta: {
      // pageTitle: 'Affiliate List',
      permissions: [PERMISSION_BITMASK_AFFILIATES],
    },
  },
]
