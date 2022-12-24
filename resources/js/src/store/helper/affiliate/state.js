const defaultAffiliate = {
  mail_list_id: null,
  expressEnabled: false,
  isPaymentProfileRequired: false,
  isCreditAccount: false,
  disableChatEmails: false,
  isActive: false,
}

export default {
  defaultAffiliate,
  affiliate: { ...defaultAffiliate },
  loading: false,
  allAffiliates: [],
}
