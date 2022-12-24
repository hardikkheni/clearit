export default {}

export const formify = data => {
  const form = new FormData()
  // eslint-disable-next-line no-restricted-syntax
  for (const key in data) {
    if (data[key] !== undefined && data[key] != null) {
      form.append(key, data[key])
    }
  }
  return form
}
