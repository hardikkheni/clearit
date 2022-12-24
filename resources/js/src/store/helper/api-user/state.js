const defaultApiUser = {
  isActive: false,
  token: null,
}

export default {
  defaultApiUser,
  apiUser: { ...defaultApiUser },
  loading: false,
}
