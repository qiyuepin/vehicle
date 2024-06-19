import Vue from 'vue'

import Cookies from 'js-cookie'

import 'normalize.css/normalize.css' // a modern alternative to CSS resets

import Element from 'element-ui'
import './styles/element-variables.scss'

import '@/styles/index.scss' // global css

import '@/icons/iconfont/iconfont.css' //自定义图标

import App from './App'
import store from './store'
import router from './router'

import permission from '@/directive/permission/index.js' // 权限判断指令
Vue.use(permission)
import '@/directive/drag/index.js' // 弹窗拖拽判断指令
import moment from 'moment'
Vue.prototype.$moment = moment
import i18n from './lang' // internationalization
import './icons' // icon
import './permission' // permission control

import SocketIO from "socket.io-client"
import defaultSettings from './settings.js'
import VueSocketIO from 'vue-socket.io'


import inputDirective from './directive/input/install';
// import VueAMap from 'vue-amap'
// Vue.use(VueAMap);

// VueAMap.initAMapApiLoader({
//   key: '4cfd1e1cfdc8b0e77e0aaaaade6aee50',
//   plugin: [
//       'AMap.Scale',
//       'AMap.OverView',
//       'AMap.ToolBar',
//       'AMap.MapType',
//       'AMap.PlaceSearch',
//       'AMap.Geolocation',
//       'AMap.Geocoder',
//       'AMap.DrivingPolicy',
//       'AMap.Driving',
//       "AMap.Autocomplete",
//  "AMap.PolyEditor",
//  "AMap.CircleEditor",
//   ],
//   v: '1.4.4',
//   uiVersion: '1.0',
// })

// //高德的安全密钥
// window._AMapSecurityConfig = {
//   securityJsCode:'b64b7df6be939d822d7380f63fb22f31',
// }
Vue.use(new VueSocketIO({
  debug: true,
  connection: SocketIO (process.env.VUE_APP_WS_URL+'?X-Access-Appid='+defaultSettings.appid,{transports: ['websocket']}),
}))
/**
 * If you don't want to use mock-server
 * you want to use MockJs for mock api
 * you can execute: mockXHR()
 *
 * Currently MockJs will be used in the production environment,
 * please remove it before going online ! ! !
 */
Vue.config.productionTip = false

Vue.use(Element, {
  zIndex: 99999,
  size: Cookies.get('size') || 'medium', // set element-ui default size
  i18n: (key, value) => i18n.t(key, value)
})

Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  i18n,
  render: h => h(App)
})

Vue.use( inputDirective );
