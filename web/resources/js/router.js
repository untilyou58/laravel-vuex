import Vue from 'vue'
import VueRouter from 'vue-router'

// Import page components
import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'
import PhotoDetail from './pages/PhotoDetail.vue'
import SystemError from './pages/errors/System.vue'
import NotFound from './pages/errors/NotFound.vue'

// Store
import store from './store'

// Use the VueRouter plugin
// This allows you to use the <RouterView /> component etc.
Vue.use(VueRouter)

// Path and component mapping
const routes = [
  {
    path: '/photos/:id',
    component: PhotoDetail,
    props: true
  },
  {
    path: '/',
    component: PhotoList,
    props: route => {
      const page = route.query.page
      return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
    }
  },
  {
    path: '/photos/:id',
    component: PhotoDetail,
    props: true
  },
  {
    path: '/login',
    component: Login,
    beforeEnter (to, from, next) {
      if (store.getters['auth/check']) {
        next('/')
      } else {
        next()
      }
    }
  },
  {
    path: '/500',
    component: SystemError
  },
  {
    path: '*',
    component: NotFound
  },
]

// Create a VueRouter instance
const router = new VueRouter({
  mode: 'history',
  scrollBehavior () {
    return { x: 0, y: 0 }
  },
  routes
})

// Export a VueRouter instance
// To import in app.js
export default router
