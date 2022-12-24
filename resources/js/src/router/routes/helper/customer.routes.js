import { PERMISSION_BITMASK_AFFILIATES } from '@/utils/permissions'

export default [
  {
    path: '/helper/customer',
    name: 'helper-customer',
    component: () => import('@/views/pages/helper/customer/CustomerList.vue'),
  },
]
