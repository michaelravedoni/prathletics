import Vue from 'vue';
import VueResource from 'vue-resource';
import VueMomentLib from 'vue-moment-lib';
import Vue2Filters from 'vue2-filters';
import VueLodash from 'vue-lodash';
import VueProgressBar from 'vue-progressbar';
import VueQRCodeComponent from 'vue-qrcode-component';
import App from './App.vue';
import router from './router';
import store from './store';
import './registerServiceWorker';

Vue.config.productionTip = false;

Vue.use(VueResource);
Vue.use(VueMomentLib);
Vue.use(Vue2Filters);
Vue.use(VueLodash);
Vue.use(VueProgressBar, { thickness: '5px',transition: {speed: '0.2s', opacity: '0.6s', termination: 300}, });
Vue.component('qr-code', VueQRCodeComponent);

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
