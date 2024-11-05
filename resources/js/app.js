import.meta.glob([
    '../images/**',
])

import './bootstrap'

import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs'
import Vue3Toastify from 'vue3-toastify'

import SearchInput from './components/SearchInput.vue'
import Table from './components/Table.vue'
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

app.component('search-input', SearchInput)
app.component('vue-table', Table)

app.component('hotel-select', HotelSelect)
app.component('transaction-selects', TransactionSelects)
app.component('transaction-live-search', TransactionLiveSearch)

app.component('service-list', ServiceList)
app.component('dining-service-list', DiningServiceList)

app.component('product-transactions', ProductTransactions)
app.component('product-list', ProductList)

app.component('room-list', RoomList)

app.component('prop-transactions', PropTransactions)
app.component('prop-list', PropList)

app.component('asset-list', AssetList)

app.component('note-create', NoteCreate)
app.component('tag-list', TagList)

app.component('search-guests', SearchGuests)
app.component('process-list', ProcessList)
app.component('vouchers-index', VoucherIndex);

app.component('home-index', HomeIndex);

// app.use(ContextMenu)
app.use(LaravelPermissionToVueJS)
app.use(Vue3Toastify, {
    autoClose: 3000,
})

app.use(i18nVue, {
    resolve: lang => {
        const langs = import.meta.glob('../../lang/*.json', { eager: true });

        return langs[`../../lang/${lang}.json`].default;
    },
})

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
// })

app.mount("#app")
