/**
 * Get cookie value
 * @param {String} searchKey Key to search
 * @returns {String} The value corresponding to the key
 */
export function getCookieValue (searchKey) {
  if (typeof searchKey === 'undefined') {
    return ''
  }

  let val = ''

  document.cookie.split(';').forEach(cookie => {
    const [key, value] = cookie.split('=')
    if (key === searchKey) {
      return val = value
    }
  })

  return val
}

// When success login page
export const OK = 200
// Created user success
export const CREATED = 201
// Internal server
export const INTERNAL_SERVER_ERROR = 500
// 422 error
export const UNPROCESSABLE_ENTITY = 422
// 419 error unauthorized
export const UNAUTHORIZED = 419
// 404 Page not found
export const NOT_FOUND = 404
