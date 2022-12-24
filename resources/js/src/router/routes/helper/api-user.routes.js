export default [
  {
    path: '/helper/api-user/create',
    name: 'create-helper-api-user',
    component: () => import('@/views/pages/helper/api-user/CreateApiUser.vue'),
    meta: {},
  },

  {
    path: '/helper/api-user/list',
    name: 'helper-api-user-list',
    component: () => import('@/views/pages/helper/api-user/ApiUserList.vue'),
    meta: {},
  },
  {
    path: '/helper/api-user/:id',
    name: 'edit-helper-api-user',
    component: () => import('@/views/pages/helper/api-user/EditApiUser.vue'),
    meta: {},
  },
]
