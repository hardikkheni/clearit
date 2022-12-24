const defaultAlertMessage = {
  acknowledgementRequired: false,
  showNewAgent: false,
  isActive: false,
}

export default {
  defaultAlertMessage,
  alertMessage: { ...defaultAlertMessage },
  loading: false,
}
