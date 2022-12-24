/* eslint-disable no-unused-vars */
/* eslint-disable eqeqeq */
/* eslint-disable no-plusplus */
/* eslint-disable radix */
/* eslint-disable global-require */
export const DEFAULT_TEXT_HEX_COLOR = 'FFFFFF'
export const DEFAULT_HEX_COLOR = '000000'

export const TICKET_TYPE_ALL = 'all'
export const TICKET_TYPE_CLEARANCE = 'clearance'
export const TICKET_TYPE_CAR = 'car'
export const TICKET_TYPE_AES = 'aes'
export const TICKET_TYPE_FREIGHT = 'freight'
export const TICKET_TYPE_CUSTOM = 'custom'

export const TICKET_TRANSPORT_TRUCK = 1
export const TICKET_TRANSPORT_OCEAN = 2
export const TICKET_TRANSPORT_AIR = 3
export const TICKET_TRANSPORT_COURIER = 4
export const TICKET_TRANSPORT_HANDCARRY = 5

export const TICKET_DELIVERY_SELECTION_COMMERCIAL = 1
export const TICKET_DELIVERY_SELECTION_RESIDENTIAL = 2
export const TICKET_DELIVERY_SELECTION_AMAZON = 3

export const types = [
  { value: 'all', label: 'ALL' },
  { value: 'clearance', label: 'CLEARANCE' },
  { value: 'car', label: 'CAR' },
  { value: 'aes', label: 'AES' },
  { value: 'freight', label: 'FREIGHT' },
  { value: 'custom', label: 'CUSTOM' },
]

export const ticketTypes = [TICKET_TYPE_CLEARANCE, TICKET_TYPE_FREIGHT, TICKET_TYPE_CUSTOM, TICKET_TYPE_CAR]

export const transportModes = [
  TICKET_TRANSPORT_TRUCK,
  TICKET_TRANSPORT_OCEAN,
  TICKET_TRANSPORT_AIR,
  TICKET_TRANSPORT_COURIER,
  TICKET_TRANSPORT_HANDCARRY,
]

export const transportModesRequiresDelivery = [TICKET_TRANSPORT_OCEAN, TICKET_TRANSPORT_AIR]

export const getTicketNumberFromId = id => id + 99999

export const getTicketStatusName = status => {
  let name = status
  switch (status) {
    case TICKET_TYPE_CLEARANCE:
      name = 'Clearance'
      break
    case TICKET_TYPE_CUSTOM:
      name = 'Consulting'
      break
    case TICKET_TYPE_FREIGHT:
      name = 'Freight'
      break
    case TICKET_TYPE_CAR:
      name = 'Car'
      break
    default:
      break
  }
  return name
}

export const getTransportName = transport => {
  let carrier = transport
  switch (transport) {
    case 1:
      carrier = 'Truck'
      break
    case 2:
      carrier = 'Ocean'
      break
    case 3:
      carrier = 'Air'
      break
    case 4:
      carrier = 'Courier'
      break
    case 5:
      carrier = 'Handcarry'
      break
    default:
      break
  }
  return carrier
}

export const ticketTypeOptions = ticketTypes.map(type => ({ value: type, text: getTicketStatusName(type) }))
export const transportModeOptions = transportModes.map(mode => ({ value: mode, text: getTransportName(mode) }))

export const getTicketBadge = (type, transport = null) => {
  let attr = {}
  if (type == TICKET_TYPE_CLEARANCE) {
    if (transport == TICKET_TRANSPORT_TRUCK) {
      attr = {
        src: require('@/assets/images/icons/truck-ico.png'),
      }
    } else if (transport == TICKET_TRANSPORT_OCEAN) {
      attr = {
        src: require('@/assets/images/icons/ocean-ico.png'),
      }
    } else if (transport == TICKET_TRANSPORT_AIR) {
      attr = {
        src: require('@/assets/images/icons/air-ico.png'),
      }
    } else if (transport == TICKET_TRANSPORT_COURIER) {
      attr = {
        src: require('@/assets/images/icons/courier-ico.png'),
      }
    }
  } else if (type == TICKET_TYPE_CAR) {
    attr = { text: 'V' }
  } else if (type == TICKET_TYPE_CUSTOM) {
    attr = { text: 'N' }
  } else if (type == TICKET_TYPE_FREIGHT) {
    attr = { text: 'F' }
  }
  return attr
}

export const getAdditionalCharges = (ticket, paid = 0) => {
  const delim = ';'
  const paymentItem = (ticket.paymentitem || '').split(delim)
  const paymentAmount = (ticket.paymentamount || '').split(delim)
  const paymentStatus = (ticket.paymentstatus || '').split(delim)
  // const paymentFiles = ticket.paymentfile.split(delim)

  let fullAmount = 0
  let fullAmountPaid = 0
  let fullAmountUnpaid = 0
  for (let f = 0; f < paymentItem.length; f++) {
    const amount = paymentAmount[f]
    const status = paymentStatus[f]
    if (status != 2) {
      fullAmountUnpaid += parseInt(amount || 0)
    } else {
      fullAmountPaid += parseInt(amount || 0)
    }
    fullAmount += parseInt(amount || 0)
  }

  switch (paid) {
    case 0:
      return fullAmount
    case 1:
      return fullAmountPaid
    case 2:
      return fullAmountUnpaid
    default:
      return 0
  }
}

export const ticketDeliverySelection = {
  [TICKET_DELIVERY_SELECTION_COMMERCIAL]: 'Commercial',
  [TICKET_DELIVERY_SELECTION_RESIDENTIAL]: 'Residential',
  [TICKET_DELIVERY_SELECTION_AMAZON]: 'Amazon',
}

// TODO: do all calculation in the backend only
export const countTicketAccountMoney = (ticket, isPaid = true, isAdditional = 0) => {
  let paid = 0
  let unpaid = 0
  let additionalPaid = 0
  let additionalUnpaid = 0
  let additional = 0
  const delim = ';'
  const paymentItems = ticket.paymentItem?.split(delim) || []
  const paymentAmounts = ticket.paymentAmount?.split(delim) || []
  const paymentStatuses = ticket.paymentStatus?.split(delim) || []
  const paymentFiles = ticket.paymentFile?.split(delim) || []
  for (let i = 0; i < paymentItems.length; i++) {
    const amount = parseFloat(paymentAmounts[i] || 0)
    const status = parseFloat(paymentStatuses[i] || 0)

    if (status == 2) {
      paid += amount
      additionalPaid += amount
    } else {
      unpaid += amount
      additionalUnpaid += amount
    }
    additional += amount
  }

  const amount = parseFloat(ticket.amount || 0)
  if (ticket.isPaid == 1) {
    paid += amount
  } else {
    unpaid += amount
  }
  if (isAdditional == 2) {
    return additional
  }
  if (isAdditional == 1) {
    if (isPaid) return additionalPaid
    return additionalUnpaid
  }
  if (isPaid) return paid
  return unpaid
}
