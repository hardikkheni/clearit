import { formify } from '@/utils/form'

export default {
  async create(_, data) {
    const form = formify(data)

    // eslint-disable-next-line no-return-await
    return (
      await this._vm.$api.post('/ticket-document', form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    ).data
  },
  async updateDocUploadType(_, { id, data }) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.post(`/ticket-document/${id}/document-upload-type`, data)).data
  },
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/ticket-document/${id}`)).data
  },
}
