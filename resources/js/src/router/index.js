import Vue from 'vue'
import VueRouter from 'vue-router'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import store from '@/store'

import helperRoutes from '@/router/routes/helper'
import authRoutes from '@/router/routes/auth.routes'
import ticketRoutes from '@/router/routes/ticket.routes'

import { hasBitMaskValue } from '@/utils/permissions'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior(to) {
    if (to.hash) {
      return {
        selector: to.hash,
        behavior: 'smooth',
      }
    }
    return { x: 0, y: 0, behavior: 'smooth' }
  },
  routes: [
    { path: '/', redirect: { name: 'dashboard' } },
    ...authRoutes,
    ...helperRoutes,
    ...ticketRoutes,
    {
      path: '/error-404',
      name: 'error-404',
      component: () => import('@/views/error/Error404.vue'),
      meta: {
        layout: 'full',
        allowAll: true,
      },
    },
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})

const sendBackToLogin = next => next({ name: 'auth-login' })
const sendBackToDashboard = next => next({ name: 'dashboard' })

const checkAuth = async (to, from, next) => {
  const loggedIn = await store.dispatch('auth/verifyAuth')
  // Ensure we checked auth before each page load.
  if (!to.meta.allowAll) {
    if ('guarded' in to.meta) {
      if (to.meta.guarded) {
        if (!loggedIn) {
          return sendBackToLogin(next)
        }
      } else if (loggedIn) {
        return sendBackToDashboard(next)
      }
    } else if (!loggedIn) {
      return sendBackToLogin(next)
    }
  }
  return next()
}

// eslint-disable-next-line consistent-return
const checkPageAccess = async (to, from, next) => {
  const user = store.getters['auth/currentUser']
  // check is page requires permissions and check is user has permission to visit the page

  if (
    !(!to.meta?.permissions || (to.meta?.permissions && (to.meta?.permissions || []).some(i => hasBitMaskValue(i, user.permissionBitmask))))
  ) {
    router.app.$toast({
      component: ToastificationContent,
      props: {
        title: 'Error',
        icon: 'LockIcon',
        variant: 'danger',
        text: `You don't have access to \`${to.name}\` page`,
      },
    })
    return sendBackToDashboard(next)
    // eslint-disable-next-line no-else-return
  } else if (!(!to.meta?.needToBeMaster || (to.meta?.needToBeMaster && user.isMaster))) {
    router.app.$toast({
      component: ToastificationContent,
      props: {
        title: 'Error',
        icon: 'LockIcon',
        variant: 'danger',
        text: `You don't have access to \`${to.name}\` page`,
      },
    })
    return sendBackToDashboard(next)
  }
  next()
}

router.beforeEach(checkAuth)
router.beforeEach(checkPageAccess)

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
