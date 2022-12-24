/* eslint-disable no-unused-vars */
/* eslint-disable eqeqeq */
/* eslint-disable no-plusplus */
/* eslint-disable radix */
/* eslint-disable global-require */

const USER_STATUS_COMMERCIAL = '2'
const USER_STATUS_NON_RESIDENT = '3'

export const userStatus = [USER_STATUS_NON_RESIDENT, USER_STATUS_COMMERCIAL]

export const getCustomerName = user => {
  if (userStatus.includes(user.status)) {
    if (user.tradename !== '') { return user.tradename }
    return user.busname
  } return `${user.lastname}, ${user.firstname}`
}
