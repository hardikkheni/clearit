export default [
  {
    path: '/ticket/:role/:guid',
    name: 'view-ticket',
    component: () => import('@/views/pages/ticket/TicketView.vue'),
    meta: {},
  },
]
