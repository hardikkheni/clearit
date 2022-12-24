/* eslint-disable consistent-return */
/* eslint-disable indent */
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

// eslint-disable-next-line import/prefer-default-export
export const apiResponseHandler = (vm, error, options = {}, form = null) => {
  if (error?.cancelled) return
  // eslint-disable-next-line operator-linebreak
  const toast =
    typeof options.toast === 'function'
      ? options.toast(error)
      : options.toast || {
          title: 'Error',
          icon: 'LockIcon',
          variant: 'danger',
          text: error?.response?.data?.message || 'Something went wrong!.',
        }

  vm.$toast({
    component: ToastificationContent,
    props: toast,
  })
  if (form && error.response?.status === 422) {
    form.setErrors(error?.response?.data?.errors || {})
  }
}

// eslint-disable-next-line import/prefer-default-export
export const apiResponseWrapper = async (vm, cb, form = null, show = true) => {
  try {
    const res = await cb()
    if (show) {
      vm.$toast({
        component: ToastificationContent,
        props: {
          title: 'Success',
          icon: 'CheckIcon',
          variant: 'success',
          text: res?.message,
        },
      })
    }
    return res
  } catch (err) {
    if (err?.cancelled || !show) return null
    vm.$toast({
      component: ToastificationContent,
      props: {
        title: 'Error',
        icon: 'LockIcon',
        variant: 'danger',
        text: err?.response?.data?.message || 'Something went wrong!.',
      },
    })
    if (form && err.response?.status === 422) {
      form.setErrors(err?.response?.data?.errors || {})
    }
  }
}
