export default {
  async dataTable(_, params) {
    return (await this._vm.$api.post('/helper/customer/list', params, { cancelToken: 'DATATABLE' })).data.data
  },
  async unverifiedWithPoaDataTable(_, params) {
    return (await this._vm.$api.post('/helper/customer/unverified-with-poa-data-table', params, { cancelToken: 'DATATABLE' })).data.data
  },
}
