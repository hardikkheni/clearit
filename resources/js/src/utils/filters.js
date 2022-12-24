/* eslint-disable arrow-body-style */
/* eslint-disable import/prefer-default-export  */
import dayjs from 'dayjs'

export const defaultDateSeperator = '-'

export const dateFormat = (value, format, dateSeperator = defaultDateSeperator) => {
  return value && dayjs(value).format(format.split(defaultDateSeperator).join(dateSeperator))
}

export const currencyFormat = value => `$ ${Number(value).toLocaleString('en-US')}`
// export const currencyFormat = value => `$ ${value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')}`

export const formatPhone = value => {
  if (value) {
    return value.toString().replace(/^(\+?\d{1,3}|)\s?(\d{3}).*(\d{3}).*(\d{4})$/, '$1 $2-$3-$4')
  }
  return value
}

export const decodeHtmlSpecialChars = encodedString => {
  const textArea = document.createElement('textarea')
  textArea.innerHTML = encodedString
  return textArea.value
}

export const formatBoolean = value => {
  if (value) {
    if (value.toString().trim().toLowerCase() === 'true') {
      return 'Yes'
    }
  }
  return 'No'
}
