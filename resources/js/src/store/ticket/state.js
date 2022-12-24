const defaultToDoTicketItem = {
  itemName: 'New Item',
}

const defaultTicketStatus = {
  statusName: 'New Status',
  substatus: null,
  hexColor: '000000',
  textHexColor: 'FFFFFF',
}

export default {
  defaultToDoTicketItem: { ...defaultToDoTicketItem },
  loading: false,
  ticket: {},
  ticketView: {},
  ticketStatusList: [],
  toDoTicketItemList: [],
  defaultTicketStatus: { ...defaultTicketStatus },
  viewTicketAlert: null,
}
