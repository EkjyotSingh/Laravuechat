
require('./bootstrap');
window.Vue = require('vue').default;
import Vuex from 'vuex'
import filter from './filter'
import emojione from './emojionearea/dist/emojionearea'

import storeVuex from './store/index'
Vue.use(Vuex)
Vue.use(emojione)

const store = new Vuex.Store(storeVuex)
Vue.config.ignoredElements = ['sidebar']
Vue.component('main-app', require('./components/MainApp.vue').default);

const app = new Vue({
    el: '#app',
    store

});
