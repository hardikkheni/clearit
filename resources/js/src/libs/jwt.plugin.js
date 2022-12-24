/* eslint-disable no-param-reassign */

// import { inject } from 'vue'

export const TOKEN_KEY = 'accessToken'

export default {
  install: Vue => {
    const jwtService = {
      getToken() {
        return this.getKey(TOKEN_KEY)
      },
      setToken(token) {
        return this.setKey(TOKEN_KEY, token)
      },
      clearToken() {
        return this.clearKey(TOKEN_KEY)
      },
      setKey(key, value) {
        return localStorage.setItem(key, value)
      },
      getKey(key) {
        return localStorage.getItem(key)
      },
      clearKey(key) {
        return localStorage.removeItem(key)
      },
    }

    Vue.prototype.$jwt = jwtService
    // app.config.globalProperties.$jwt = jwtService
    // app.config.globalProperties.$store.$jwt = jwtService
    // app.provide('$jwt', jwtService)
  },
}

// export const useJWT = () => {
//   const jwt = inject('$jwt')
//   if (!jwt) throw new Error('No JWT service provided!')
//   return jwt
// }
