// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Index from './Index'
import VueRouter from 'vue-router'
import App from './index-App'

Vue.use(VueRouter)

const router = new VueRouter({
  routes: App.routes
})

/* eslint-disable no-new */
new Vue({
  //el: '#app',
  //template: '<Index/>',
  //components: { Index },
      el: '#app',
      router,
      render: h => h(App)
})
