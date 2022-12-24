/* eslint-disable import/extensions */
import countries from '@/utils/countries'
import states from '@/utils/states'

export const DEFAULT_COUNTRY = 'US'
export const COUNTRY_FULL_NAME = 'the United States'
export const COUNTRY_SHORT_NAME = 'the U.S.'
export const STEPS_COUNT_TICKET_CLEARANCE = 6
export const STEPS_COUNT_FREIGHT = 4
export const STEPS_COUNT_ACCOUNT_REGISTRATION = 7
export const SERVICE_NAME = 'Clearit USA'
export const PAPS_EMAIL = 'paps@clearitusa.com'
export const PAPS_NAME = 'PAPS'
export const INFO_EMAIL = 'info@clearitusa.com'
export const SITE_URL = 'https://clearitusa.com'
export const WORKING_HOURS = '9:00 am to 8:00 pm'

export const DATE_FORMAT = 'MM-DD-YYYY'
export const HOUR_FORMAT = 'hh:mm a'
export const DATETIME_FORMAT = `${DATE_FORMAT} ${HOUR_FORMAT}`
export const DATE_FORMAT_SHORT_YEAR = 'MM-DD-YY'

export const dateConfig = {
  altFormat: 'm/d/Y',
  dateFormat: 'Y-m-d',
  altInput: true,
  dayjsFormat: 'YYYY-MM-DD',
}

export const timeConfig = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i:S',
  altFormat: 'h:i K',
  altInput: true,
  dayjsFormat: 'HH:mm:ss',
}

export const getCountries = () => Object.keys(countries).map(i => ({ value: i, text: countries[i] }))
export const getStates = alpha2 => Object.keys(states[alpha2] || {}).map(i => ({ value: i, text: states[alpha2][i] }))

export const getCountryFullName = alpha2 => countries[alpha2] || alpha2
export const getStateFullName = (country, alpha2) => states[country]?.[alpha2] || alpha2
