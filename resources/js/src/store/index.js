import Vue from 'vue'
import Vuex from 'vuex'

// Modules
import app from './app'
import appConfig from './app-config'
import verticalMenu from './vertical-menu'
import auth from './auth'
import agent from './helper/agent'
import affiliate from './helper/affiliate'
import customer from './helper/customer'
import reminders from './helper/reminders'
import alertMessage from './helper/alert-message'
import apiUser from './helper/api-user'
import dailyReports from './helper/daily-reports'
import docUploadType from './helper/doc-uplaod-type'
import freightForwarder from './helper/freight-forwarder'
import role from './role'
import ticket from './ticket'
import notification from './notification'
import customerRequest from './customer-request'
import pgaRequest from './pga-request'
import ticketDocument from './ticket-document'
import ticketUserHts from './ticket-user-hts'
import note from './note'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    app,
    appConfig,
    verticalMenu,
    auth,
    agent,
    affiliate,
    customer,
    reminders,
    'alert-message': alertMessage,
    'api-user': apiUser,
    'doc-upload-type': docUploadType,
    'freight-forwarder': freightForwarder,
    'daily-reports': dailyReports,
    role,
    ticket,
    notification,
    'customer-request': customerRequest,
    'pga-request': pgaRequest,
    'ticket-document': ticketDocument,
    'ticket-user-hts': ticketUserHts,
    note,
  },
  strict: process.env.DEV,
})
