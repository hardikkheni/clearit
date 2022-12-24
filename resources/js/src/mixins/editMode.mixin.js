/* eslint-disable arrow-body-style */
export default (prefix = '') => {
  return {
    data() {
      return {
        [`inEdit${prefix}Mode`]: [],
      }
    },
    methods: {
      [`pushInEdit${prefix}Mode`](id) {
        this[`inEdit${prefix}Mode`].push(id)
      },
      [`removeFromEdit${prefix}Mode`](id) {
        const i = this[`inEdit${prefix}Mode`].indexOf(id)
        if (i !== -1) {
          this[`inEdit${prefix}Mode`].splice(i, 1)
        }
      },
      [`isInEdit${prefix}Mode`](id) {
        return this[`inEdit${prefix}Mode`].includes(id)
      },
    },
  }
}
