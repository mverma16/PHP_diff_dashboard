import Vue from 'vue';
import VueRouter from 'vue-router';
import App from './components/App.vue';
import ScanResult from './components/ScanResult.vue';
import ScanList from './components/ScanList.vue';
import ScanForm from './components/ScanForm.vue';
import FileDiff from './components/FileDiff.vue';

Vue.use(VueRouter);
Vue.component("entry-point", require('./components/Layout.vue'));
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]').content;

Vue.mixin({
    methods: {
  		localtime(timeString){
  			var date = new Date(timeString);
  			return date.toString().replace(/GMT.*/g,"")
  		},
    }
})

const routes = [
	{path:"/", component:App},
	{path:"/scan-result/:id", component:App, props: true},
	{path:"/scan-list", component:ScanList},
	{path:"/process-scan", component:ScanForm},
  {path:"/modified-files/:id", component:FileDiff, name:'diffScan'},
  {path:"/file-diff/:id", component:FileDiff, name:'diffFile'}
]

const router = new VueRouter({
	mode : 'history',
    routes: routes // short for `routes: routes`
})

const app = new Vue({
    router,
}).$mount('#app')
