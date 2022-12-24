import { formify } from '@/utils/form'

export default {
  async create(_, data) {
    const form = formify(data)
    // eslint-disable-next-line no-return-await
    return (
      await this._vm.$api.post('/note', form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    ).data
  },
}
