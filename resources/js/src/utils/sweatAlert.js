/* eslint-disable prefer-object-spread */

export const DELETE_PRESET = 'delete'

const presets = {
  delete: {
    title: 'Are you sure?',
    text: 'You want to remove this!',
    icon: 'error',
    showCancelButton: true,
    confirmButtonText: 'Yes, remove it!',
    customClass: {
      confirmButton: 'btn btn-outline-danger',
      cancelButton: 'btn btn-primary ml-1',
    },
    buttonsStyling: false,
  },
}

const showSweatAlert = (vue, cb, preset = null, extraOptions = {}) => {
  const options = Object.assign(presets[preset] || {}, extraOptions)
  vue.$swal(options).then(result => cb(result))
}

export default showSweatAlert
