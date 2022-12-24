const defaultDocUploadType = {
  document_type: 'New Document Type',
  is_required: true,
  show_customer: true,
  show_affiliate: true,
  show_freight_forwarder: true,
  document_description: null,
  permissions: [],
}

export default {
  defaultDocUploadType,
  docUploadType: { ...defaultDocUploadType },
  modeOfTranports: [],
  docUploadTypes: [],
  loading: false,
}
