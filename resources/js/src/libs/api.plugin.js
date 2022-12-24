/* eslint-disable no-unused-expressions */
/* eslint-disable prefer-promise-reject-errors */
/* eslint-disable no-param-reassign */

import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { TOKEN_KEY } from './jwt.plugin'

const { CancelToken } = axios
const requestQueue = {}
const APIVALIDATIONERROR = 'api-validation-error'

export default {
  install: Vue => {
    const apiService = axios.create({
      baseURL: `${process.env.APP_URL}/api`,
    })

    apiService.interceptors.request.use(
      config => {
        const { cancelToken } = config
        if (cancelToken) {
          // cancel previous request and delete from queue
          if (requestQueue[cancelToken]) {
            const source = requestQueue[cancelToken]
            delete requestQueue[cancelToken]
            source.cancel()
          }

          // add current request in queue
          const source = CancelToken.source()
          config.cancelToken = source.token
          requestQueue[cancelToken] = source
        }

        // change some global axios configurations
        // add accessToken header before sending api
        const accessToken = localStorage.getItem(TOKEN_KEY)
        config.headers.common.Authorization = `Bearer ${accessToken}`
        return config
      },
      error =>
        // handle error from sending api requests
        // eslint-disable-next-line implicit-arrow-linebreak
        Promise.reject(error),
    )

    apiService.interceptors.response.use(
      response => {
        const { cancelToken } = response.config
        if (cancelToken) {
          // delete request from queue
          delete requestQueue[response.config.cancelToken]
        }

        const exts = {
          csv: 'text/csv',
          xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          pdf: 'application/pdf',
        }
        if (Object.values(exts).includes(response.headers['content-type'].split(';')[0])) {
          let fileName = response.headers['content-disposition'].split(';')[1].split('=')[1]
          if (fileName.includes('"')) {
            fileName = JSON.parse(fileName)
          }
          const url = URL.createObjectURL(
            new Blob([response.data], {
              type: response.headers['content-type'],
            }),
          )
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', fileName)
          document.body.appendChild(link)
          link.click()
          response.config.downloaded = true
        }
        return response
      },
      error => {
        const app = document.getElementById('app')?.__vue__

        const { response } = error
        if (axios.isCancel(error)) {
          return Promise.reject({ cancelled: true })
        }
        const { cancelToken } = response.config
        if (cancelToken) {
          // delete request from queue
          delete requestQueue[response.config.cancelToken]
        }

        if (error?.response?.status === 401) {
          app?.$store.commit('auth/purgeAuth')
          app?.$router.push({
            name: 'auth-login',
          })
          app?.$toast({
            component: ToastificationContent,
            props: {
              title: 'Error',
              icon: 'LockIcon',
              variant: 'danger',
              text: 'Your session is expired!. Please start new session to countinue.',
            },
          })
        } else if (error?.response?.status === 403) {
          app?.$toast({
            component: ToastificationContent,
            props: {
              title: 'Error',
              icon: 'LockIcon',
              variant: 'danger',
              text: error?.response?.data?.message || 'Something went wrong!.',
            },
          })
        }
        return Promise.reject(error)
      },
    )

    Vue.prototype.$api = apiService
    Vue.prototype.APIVALIDATIONERROR = APIVALIDATIONERROR
    // Vue.$store = apiService
    // app.config.globalProperties.$api = apiService
    // app.config.globalProperties.$store.$api = apiService
    // Vue.provide('$api', apiService)
  },
}

// TODO if we need this or not
// export const useApi = () => {
//   const api = inject('$api')
//   if (!api) throw new Error('No api service provided!')
//   return api
// }
