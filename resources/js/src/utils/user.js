export default {}

export const USER_STATUS_PERSONAL = 1
export const USER_STATUS_COMMERCAIL = 2
export const USER_STATUS_NON_RESIDENT = 3

export const userStatuses = [USER_STATUS_NON_RESIDENT, USER_STATUS_COMMERCAIL]

export const getUserName = user => {
  if (userStatuses.includes(user.status)) {
    if (user.tradename !== '') {
      return user.tradename
    }
    return user.busname
  }
  return `${user.lastname}, ${user.firstname}`
}

export const getAccountTypeLabel = status => {
  switch (+status) {
    case USER_STATUS_PERSONAL:
      return 'Personal'
    case USER_STATUS_COMMERCAIL:
      return 'U.S. Business'
    case USER_STATUS_NON_RESIDENT:
      return 'Non-Resident Importer'
    default:
      return 'N/A'
  }
}

export const getCorporateType = type => {
  switch (+type) {
    case 1:
      return 'Corporation'
    case 2:
      return 'Sole Proprietorship'
    case 3:
      return 'LLC'
    case 4:
      return 'Partnership'
    default:
      return ''
  }
}

export const getCorporateRole = role => {
  switch (+role) {
    case 1:
      return 'Single signing officer'
    case 2:
      return 'Co-signing officer'
    default:
      return ''
  }
}
