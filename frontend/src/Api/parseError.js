import isJsonResponse from './isJsonResponse'

async function parseError(error) {
  if (error.status === 422) {
    return null
  }

  if (error.status && isJsonResponse(error)) {
    const data = await error.json()

    if (data.message) {
      return data.message
    }
  }

  if (error.status) {
    return error.statusText
  }

  return error.message
}

export default parseError
