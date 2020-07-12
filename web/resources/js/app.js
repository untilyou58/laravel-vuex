import Vue from 'vue'
// Import routing definitions
import router from './router'
// Import the root component
import App from './App.vue'
import './bootstrap'
import store from './store'

const createApp = async () => {
  await store.dispatch('auth/currentUser')

  new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App />'
  })
}

createApp()
