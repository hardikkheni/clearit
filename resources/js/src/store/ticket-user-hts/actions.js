export default {
  async create(_, data) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post('/ticket-user-hts', data)).data
  },
  async update(_, { data, id }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket-user-hts/${id}`, data)).data
  },
  // async updateDocUploadType(_, { id, data }) {
  //   // eslint-disable-next-line no-return-await
  //   return (await this._vm.$api.post(`/ticket-document/${id}/document-upload-type`, data)).data
  // },
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/ticket-user-hts/${id}`)).data
  },
}
