import { getCookieValue } from './util'

window.axios = require('axios')

// Add header indicating that it is an Ajax request
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.axios.interceptors.request.use(config => {
  // Get token from cookie and attach to header
  config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN')

  return config
})

// Axios response
window.axios.interceptors.response.use(
  response => response,
  error => error.response || error
)
