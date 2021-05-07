import Vue from 'vue';
import VueRouter from 'vue-router';
import App  from './components/App.Vue';
import ScanForm  from './components/ScanForm.Vue';
import ScanResult  from './components/ScanResult.Vue';
import ScanList  from './components/ScanList.Vue';

Vue.use(VueRouter);
Vue.component("entry-point", require('./components/Layout.vue'))


const routes = [
    { path: '/scan-form', component: ScanForm },
    { path: '/scan-list', component: ScanList },
    { path: '/', component: App }
]

const router = new VueRouter({
    routes // short for `routes: routes`
})

const app = new Vue({
  router
}).$mount('#app')