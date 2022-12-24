export default {}

export const DOCUMENT_TYPE_COMMERCIAL_INVOICE = 'Commercial Invoice'
export const DOCUMENT_TYPE_BILL_OF_LADING = 'Bill of lading'
export const DOCUMENT_TYPE_ISF = 'ISF FILE'
export const DOCUMENT_TYPE_PAPS = 'PAPS'
export const DOCUMENT_TYPE_OTHER = 'Other'
export const DOCUMENT_TYPE_7501 = '7501'
export const DOCUMENT_TYPE_ACE_CARGO_RELEASE = 'ACE'
export const DOCUMENT_TYPE_ISF_CERTIFICATE = 'ISF Certificate'

export const personalUserDocumentTypes = {
  DOCUMENT_TYPE_DRIVERS_LICENSE: "US Driver's License",
  DOCUMENT_TYPE_MILITARY_ID: 'Military ID',
  DOCUMENT_TYPE_STATE_ISSUED_ID: 'State Issued ID',
  DOCUMENT_TYPE_OTHER_ID: 'Other',
}

export const getPersonalUserDocumentType = docTypeId => personalUserDocumentTypes[docTypeId] || ''
