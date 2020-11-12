import './utility';
window.Vue = require('vue');

Vue.component('footer-component', require('./components/FooterComponent.vue').default);
Vue.component('dash-board', require('./components/DashBoard.vue').default);
Vue.component('login', require('./components/Login.vue').default);
Vue.component('header', require('./components/Header.vue').default);
Vue.component('panel-filter', require('./components/PanelFilter.vue').default);
Vue.component('panel-follow', require('./components/PanelFollow.vue').default);
Vue.component('panel-like', require('./components/PanelLike.vue').default);
Vue.component('panel-tweet', require('./components/PanelTweet.vue').default);
Vue.component('panel-unfollow', require('./components/PanelUnfollow.vue').default);
Vue.component('password-reset', require('./components/PasswordReset.vue').default);
Vue.component('twitter-account', require('./components/TwitterAccount.vue').default);

const app = new Vue({
    el: '#app',
});