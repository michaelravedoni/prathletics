import Vue from 'vue';
import VueResource from 'vue-resource';
import VueMomentLib from 'vue-moment-lib';
import Vue2Filters from 'vue2-filters';
import App from './App.vue';
import router from './router';
import store from './store';
import './registerServiceWorker';

Vue.config.productionTip = false;

Vue.use(VueResource);
Vue.use(VueMomentLib);
Vue.use(Vue2Filters);

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
