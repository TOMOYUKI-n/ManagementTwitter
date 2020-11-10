import './utility';
window.Vue = require('vue');

Vue.component('footer-component', require('./components/FooterComponent.vue').default);
Vue.component('dash-board', require('./components/DashBoard.vue').default);
Vue.component('Login', require('./components/Login.vue').default);
Vue.component('Header', require('./components/Header.vue').default);
Vue.component('PanelFilter', require('./components/PanelFilter.vue').default);
Vue.component('PanelFollow', require('./components/PanelFollow.vue').default);
Vue.component('PanelLike', require('./components/PanelLike.vue').default);
Vue.component('PanelTweet', require('./components/PanelTweet.vue').default);
Vue.component('PanelUnfollow', require('./components/PanelUnfollow.vue').default);
Vue.component('PasswordReset', require('./components/PasswordReset.vue').default);

const app = new Vue({
    el: '#app',
});