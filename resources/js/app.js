
window.Vue = require('vue');

Vue.component('login', require('./components/Login.vue').default);

Vue.component('footer-component', require('./components/FooterComponent.vue').default);
Vue.component('sidebar-component', require('./components/SidebarComponent.vue').default);

Vue.component('dash-board', require('./components/DashBoard.vue').default);
Vue.component('panel-keyword', require('./components/PanelKeyword.vue').default);
Vue.component('panel-follow', require('./components/PanelFollow.vue').default);
Vue.component('panel-like', require('./components/PanelLike.vue').default);
Vue.component('panel-tweet', require('./components/PanelTweet.vue').default);
Vue.component('panel-unfollow', require('./components/PanelUnfollow.vue').default);
Vue.component('twitter-account', require('./components/TwitterAccount.vue').default);
Vue.component('account-card', require('./components/AccountCard.vue').default);

const app = new Vue({
    el: '#app',
});