const defaultFreightForwarder = {}
const defaultFreightContact = {
  isDefault: true,
}

export default {
  defaultFreightForwarder,
  defaultFreightContact,
  freightForwarder: { ...defaultFreightForwarder },
  freightContact: { ...defaultFreightContact },
  loading: false,
  freightContacts: [],
  freightContactsOfForwarder: [],
}
