/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import VueRouter from 'vue-router';
import Permissions from './mixins/Permissions';

// Commom
Vue.component('search-input', require('./components/SearchInput.vue').default);
Vue.component('hotel-select', require('./components/Hotels/Select.vue').default);
Vue.component('transaction-selects', require('./components/Transactions/TransactionSelects.vue').default);
Vue.component('transaction-live-search', require('./components/Transactions/TransactionLiveSearch.vue').default);

Vue.component('service-list', require('./components/Services/ServiceList.vue').default);
Vue.component('dining-service-list', require('./components/Services/DiningServiceList.vue').default);

Vue.component('product-transactions', require('./components/Products/ProductTransactions.vue').default);
Vue.component('product-list', require('./components/Products/ProductList.vue').default);

Vue.component('room-list', require('./components/Rooms/RoomList.vue').default);

Vue.component('prop-transactions', require('./components/Props/PropTransactions.vue').default);
Vue.component('prop-list', require('./components/Props/PropList.vue').default);

Vue.component('asset-list', require('./components/Assets/AssetList.vue').default);

Vue.component('process-list', require('./components/Vouchers/ProcessList.vue').default);

Vue.component('note-create', require('./components/Notes/NoteCreate.vue').default);
Vue.component('tag-list', require('./components/Tags/TagList.vue').default);

Vue.component('search-guests', require('./components/Vouchers/SearchGuests.vue').default);

Vue.component('home-index', require('./components/Home/Index.vue').default);

import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales.generated';

Vue.mixin(Permissions);

Vue.use(VueRouter);
Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);

const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

const router = new VueRouter({
    mode: 'history'
});

Vue.filter('date', function(value) {
    if (value) {
        return moment(String(value)).format('YY-MM-DD')
    }

    return ''
})

const app = new Vue({
    el: '#app',
    i18n,
    router: router
});