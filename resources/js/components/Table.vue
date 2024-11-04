<template>
    <div>
        <div v-if="hasUrl">
            <div class="d-flex flex-column">
                <span>
                    {{ trans('display') }}:
                </span>
            </div>
            <div class="d-flex mb-4">
                <div class="mr-auto w-25">
                    <select class="form-control" v-model.number="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="ml-auto w-25">
                    <div class="input-group">
                        <input class="form-control" type="search" v-model="search" :placeholder="trans('search')" />
                        <div class="input-group-append">
                            <button v-if="hasIcon" class="input-group-text" id="btnGroupAddon">
                                <em :class="searchIcon"></em>
                            </button>
                            <button v-else class="input-group-text" id="btnGroupAddon">
                                &#128269;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table" id="categories">
                <thead>
                    <tr>
                        <th v-for="(header, index) in titles" :key="index" @click="setSort(header)">
                            {{ header.description }} <span v-if="header.selected">&ddarr;</span>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="hasRecords">
                    <tr v-for="(record, index) in records" :key="index" :id="index">
                        <slot name="record" :record="record"></slot>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <th :colspan="headers.length">...</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="hasUrl" class="d-flex">
            <div class="mr-auto">
                <div class="mb-4 text-center text-sm-center text-md-left align-self-center">
                    <p>{{ trans('record') }} {{ this.from }} {{ trans('of') }} {{ this.to }} / {{ trans('total') }} {{ this.total }}</p>
                </div>
            </div>
            <div class="ml-auto">
                <div class="d-flex">
                    <div class="mb-4 mr-2 flex-fill">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item">
                                    <button
                                        class="page-link"
                                        :class="[isFirstPage ? 'disabled text-body-secondary' : '']"
                                        aria-label="First"
                                        @click="goToFirst"
                                    >
                                        <span aria-hidden="true">&lt;&lt;</span>
                                    </button>
                                </li>
                                <li class="page-item">
                                    <button
                                        class="page-link"
                                        :class="[isFirstPage ? 'disabled text-body-secondary' : '']"
                                        aria-label="Previous"
                                        @click="goToPrev"
                                    >
                                        <span aria-hidden="true">&lt;</span>
                                    </button>
                                </li>
                                <li class="page-item">
                                    <button
                                        class="page-link"
                                        :class="[isLastPage ? 'disabled text-body-secondary' : '']"
                                        aria-label="Next"
                                        @click="goToNext"
                                    >
                                        <span aria-hidden="true">&gt;</span>
                                    </button>
                                </li>
                                <li class="page-item">
                                    <button
                                        class="page-link"
                                        :class="[isLastPage ? 'disabled text-body-secondary' : '']"
                                        aria-label="Last"
                                        @click="goToLast"
                                    >
                                        <span aria-hidden="true">&gt;&gt;</span>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="mb-4 flex-fill">
                        <div class="input-group">
                            <input
                                type="number"
                                step="1"
                                min="1"
                                :max="last"
                                class="form-control"
                                :value="page"
                                @change="setPage"
                                @keyup.enter="setPage"
                            />

                            <div class="input-group-append">
                                <button class="input-group-text">
                                    {{ last }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        mounted() {
            this.prepareTitles();

            if (Array.isArray(this.userData)) {
                if (this.userData.length == 0) {
                    this.loadData();
                } else {
                    this.records = this.userData
                }
            } else {
                this.last = this.userData.last_page;
                this.total = this.userData.total;
                this.from = this.userData.from;
                this.to = this.userData.to;
                this.records = this.userData.data;

                this.$emit('url-update', this.userData.path)
            }
        },
        props: {
            headers: {
                type: Array,
                default: function () {
                    return []
                },
            },
            url: {
                type: String,
                default: function () {
                    return ''
                },
            },
            lang: {
                type: String,
                default: function () {
                    return 'en'
                }
            },
            locales: {
                type: Object,
                default: function () {
                    return {
                        en:{
                            display: 'Records per page',
                            search: 'Search',
                            record: 'Record',
                            of: 'of',
                            total: 'Total'
                        },
                        es:{
                            display: 'Registros por página',
                            search: 'Buscar',
                            record: 'Registro',
                            of: 'de',
                            total: 'Total'
                        }
                    }
                }
            },
            params: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            userData: {
                type: [Array, Object],
                default: function () {
                    return []
                }
            },
            dataKey: {
                type: String,
                default: function () {
                    return ''
                }
            },
            searchIcon: {
                type: String,
                default: function () {
                    return ''
                }
            }
        },
        data() {
            return {
                titles: [],
                page: 1,
                last: 1,
                records: [],
                total: 0,
                state: {},
                perPage: 10,
                from: 0,
                to: 0,
                search: '',
                sort: '',
                direction: ''
            };
        },
        watch: {
            page() {
                this.loadData();
            },
            perPage() {
                if (this.page == 1) {
                    this.loadData();
                } else {
                    this.page = 1;
                }
            },
            search(current) {
                if (current.length == 0 || this.search.length == 0) {
                    this.restoreState();
                }

                if (current.length >= 3) {
                    if (!this.state.hasOwnProperty('records')) {
                        this.saveState();
                    }

                    this.page = 1;
                    this.sort = '';
                    this.loadData();
                }
            },
            url() {
                this.resetState()

                if (this.url.length) {
                    this.loadData();
                }
            },
            params() {
                this.resetState()

                if (this.url.length) {
                    this.loadData();
                }
            }
        },
        computed: {
            hasUrl() {
                return this.url.length > 0
            },
            hasRecords() {
                return this.records.length > 0

            },
            hasIcon() {
                return this.searchIcon.length > 0
            },
            isLastPage() {
                return this.page == this.last
            },
            isFirstPage() {
                return this.page == 1
            }
        },
        methods: {
            assignParams() {
                let required = {
                    page: this.page,
                    per_page: this.perPage
                }

                Object.assign(this.params, required);
                this.assignQuery()
                this.assignSortBy()
            },
            loadData() {
                this.assignParams()

                axios.get(this.url, {
                    params: this.params
                }).then((response) => {
                    const records = this.getDataObject(response)

                    if (records.data.length) {
                        this.last = records.last_page;
                        this.total = records.total;
                        this.from = records.from;
                        this.to = records.to;
                        this.records = records.data;
                    }
                })
            },
            getDataObject(response) {
                if (this.dataKey.length) {
                    return response.data[this.dataKey]
                }

                return response.data
            },
            nextPage() {
                if (this.page < this.last) {
                    this.page++;
                }
            },
            saveState() {
                this.state.page = this.page;
                this.state.last = this.last;
                this.state.records = this.records;
                this.state.total = this.total;
                this.state.from = this.from;
                this.state.to = this.to;
                this.state.sort = this.sort;
            },
            restoreState() {
                if (this.state.hasOwnProperty('records')) {
                    this.records = this.state.records;
                    this.page = this.state.page;
                    this.last = this.state.last;
                    this.total = this.state.total;
                    this.from = this.state.from;
                    this.to = this.state.to;
                    this.sort = this.state.sort;

                    this.state = {};
                }
            },
            resetState() {
                this.records = [];
                this.page = 1;
                this.last = 1;
                this.total = 0;
                this.from = 0;
                this.to = 0;
                this.sort = '';
                this.search = '';

                this.state = {}
            },
            goToFirst() {
                this.page = 1;
            },
            goToLast() {
                this.page = this.last;
            },
            goToNext() {
                if (this.page < this.last) {
                    this.page++;
                }
            },
            goToPrev() {
                if (this.page > 1) {
                    this.page--;
                }
            },
            setPage(event) {
                let number = parseInt(event.target.value);

                if (number < 1) {
                    this.page = 1;
                } else if (number > this.last) {
                    this.page = this.last;
                } else {
                    this.page = number;
                }
            },
            assignQuery() {
                if (this.state.hasOwnProperty('records')) {
                    if (this.search.length) {
                        Object.assign(this.params, {search: this.search});
                    }
                }
            },
            assignSortBy() {
                if (this.sort.length) {
                    Object.assign(this.params, {direction: this.direction});
                }
            },
            prepareTitles() {
                if (this.headers.length) {
                    // Add  the selected custom field
                    this.headers.forEach((title) => {
                        this.$set(title, 'selected', false);
                        this.titles.push(title)
                    });
                }
            },
            setSort(header) {
                this.titles.forEach((title) => {
                    title.selected = false;
                });

                header.selected = true;

                let direction = this.getDirection(header);

                if (this.sort == header.sortable) {
                    if (this.direction == direction.asc) {
                        this.direction = direction.desc;
                    } else if (this.direction == direction.desc) {
                        this.direction = direction.asc;
                    } else {
                        this.direction = direction.asc;
                    }
                } else {
                    this.sort = header.sortable;
                    this.direction = direction.asc;
                }

                this.loadData();
            },
            getDirection(header) {
                return {
                    desc: `&ordered_desc=${header.sortable}`,
                    asc: `&ordered_asc=${header.sortable}`,
                };
            },
            trans(key) {
                return this.locales[this.lang][key]
            }
        },
    };
</script>