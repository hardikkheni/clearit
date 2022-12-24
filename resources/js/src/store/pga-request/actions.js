export default {
  async delete(_, id) {
    // eslint-disable-next-line no-return-await
    return (await this._vm.$api.delete(`/pga-request/${id}`)).data
  },
}
