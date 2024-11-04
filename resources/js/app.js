/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap'

import { createApp } from 'vue'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Permissions from './mixins/Permissions'
import VueInternationalization from 'vue-i18n'
import Locale from './vue-i18n-locales.generated'

import SearchInput from './components/SearchInput.vue'
import HotelSelect from './components/Hotels/Select.vue'
import TransactionSelects from './components/Transactions/TransactionSelects.vue'
import TransactionLiveSearch from './components/Transactions/TransactionLiveSearch.vue'
import ServiceList from './components/Services/ServiceList.vue'
import DiningServiceList from './components/Services/DiningServiceList.vue'
import ProductTransactions from './components/Products/ProductTransactions.vue'
import ProductList from './components/Products/ProductList.vue'
import RoomList from './components/Rooms/RoomList.vue'
import PropTransactions from './components/Props/PropTransactions.vue'
import PropList from './components/Props/PropList.vue'
import AssetList from './components/Assets/AssetList.vue'
import NoteCreate from './components/Notes/NoteCreate.vue'
import TagList from './components/Tags/TagList.vue'
import SearchGuests from './components/Vouchers/SearchGuests.vue'
import ProcessList from './components/Vouchers/ProcessList.vue'
import VoucherIndex from './components/Vouchers/Index.vue'
import HomeIndex from './components/Home/Index.vue'

const app = createApp()

Vue.component('search-input', SearchInput)
Vue.component('hotel-select', HotelSelect)
Vue.component('transaction-selects', TransactionSelects)
Vue.component('transaction-live-search', TransactionLiveSearch)

Vue.component('service-list', ServiceList)
Vue.component('dining-service-list', DiningServiceList)

Vue.component('product-transactions', ProductTransactions)
Vue.component('product-list', ProductList)

Vue.component('room-list', RoomList)

Vue.component('prop-transactions', PropTransactions)
Vue.component('prop-list', PropList)

Vue.component('asset-list', AssetList)

Vue.component('note-create', NoteCreate)
Vue.component('tag-list', TagList)

Vue.component('search-guests', SearchGuests)
Vue.component('process-list', ProcessList)
Vue.component('vouchers-index', VoucherIndex);

Vue.component('home-index', HomeIndex);


// Vue.mixin(Permissions);

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);

const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

// Vue.filter('date', function(value) {
//     if (value) {
//         return moment(String(value)).format('YY-MM-DD')
//     }

//     return ''
// });

// Vue.mixin({
//     methods: {
//         route: route
//     }
// });

// const app = new Vue({
//     el: '#app',
//     i18n,
//     router: router
// });

app.mount("#app")
