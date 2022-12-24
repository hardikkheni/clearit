export default [
  {
    path: '/auth/login',
    name: 'auth-login',
    component: () => import('@/views/pages/authentication/Login.vue'),
    meta: {
      layout: 'full',
      guarded: false,
    },
  },
  {
    path: '/dashbaord/:role?/:type?/:status?/:from?/:to?/:agent?/:transport?/:agent_status?/:affiliateId?/:search?',
    name: 'dashboard',
    component: () => import('@/views/dashboard/Dashboard.vue'),
    meta: {
      pageTitle: 'Dashboard',
    },
  },
  {
    path: '/register-affiliate/:affiliateId',
    name: 'register-affiliate',
    component: () => import('@/views/pages/RegisterAffiliate.vue'),
  },
]
