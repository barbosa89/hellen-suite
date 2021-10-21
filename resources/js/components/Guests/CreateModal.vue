<template>
    <div
        class="modal fade"
        id="guest-creation-modal"
        tabindex="-1"
        aria-labelledby="guest-creation-modal-label"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guest-creation-modal-label">
                        {{ $t('common.actions.create', {model: $t('guests.guest')}) }}
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="identification_type_id">
                                        {{ $t("common.idType") }}:
                                    </label>
                                    <select
                                        v-model="guest.identification_type_id"
                                        class="form-control"
                                        name="identification_type_id">
                                        <option value="" selected disabled>
                                            {{ $t('users.choose.identification') }}
                                        </option>
                                        <option
                                            v-for="type in identificationTypes"
                                            :value="type.hash"
                                            :key="type.hash">
                                            {{ type.description }}
                                        </option>
                                    </select>

                                    <invalid-feedback :message="getError('identification_type_id')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="dni">{{ $t("common.number") }}:</label>
                                    <input
                                        v-model="guest.dni"
                                        type="text"
                                        class="form-control"
                                        name="dni"
                                        id="dni"
                                        :placeholder="$t('common.required')">

                                    <invalid-feedback :message="getError('dni')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="name">{{ $t("common.name") }}(s):</label>
                                    <input
                                        v-model="guest.name"
                                        type="text"
                                        class="form-control"
                                        name="name"
                                        id="name"
                                        :placeholder="$t('common.required')">

                                    <invalid-feedback :message="getError('name')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="last_name">{{ $t("common.lastname") }}(s):</label>
                                    <input
                                        v-model="guest.last_name"
                                        type="text"
                                        class="form-control"
                                        name="last_name"
                                        id="last_name"
                                        :placeholder="$t('common.required')">

                                    <invalid-feedback :message="getError('last_name')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">{{ $t("common.email") }}:</label>
                                    <input
                                        v-model="guest.email"
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        :placeholder="$t('common.optional')">

                                    <invalid-feedback :message="getError('email')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="address">{{ $t("common.address") }}:</label>
                                    <input
                                        v-model="guest.address"
                                        type="text"
                                        class="form-control"
                                        name="address"
                                        id="address"
                                        :placeholder="$t('common.optional')">

                                    <invalid-feedback :message="getError('address')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="phone">{{ $t("common.phone") }}:</label>
                                    <input
                                        v-model="guest.phone"
                                        type="tel"
                                        class="form-control"
                                        name="phone"
                                        id="phone"
                                        :placeholder="$t('common.optional')">

                                    <invalid-feedback :message="getError('phone')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="profession">{{ $t("guests.profession") }}:</label>
                                    <input
                                        v-model="guest.profession"
                                        type="text"
                                        class="form-control"
                                        name="profession"
                                        id="profession"
                                        :placeholder="$t('common.optional')">

                                    <invalid-feedback :message="getError('profession')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="gender">{{ $t("common.gender") }}:</label>
                                    <select
                                        v-model="guest.gender"
                                        class="form-control"
                                        name="gender"
                                        id="gender">
                                        <option value="" selected disabled>
                                            {{ $t('guests.choose.gender') }}
                                        </option>
                                        <option
                                            v-for="(gender, key) of genders"
                                            :value="key"
                                            :key="key">
                                            {{ gender }}
                                        </option>
                                    </select>

                                    <invalid-feedback :message="getError('gender')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="country_id">{{ $t("guests.birth.country") }}:</label>
                                    <select
                                        v-model="guest.country_id"
                                        class="form-control"
                                        name="country_id"
                                        id="country_id"
                                        required>
                                        <option value="" selected disabled>
                                            {{ $t('common.choose.option') }}
                                        </option>
                                        <option v-for="country in countries" :value="country.hash" :key="country.hash">
                                            {{ country.name }}
                                        </option>
                                    </select>

                                    <invalid-feedback :message="getError('country_id')"></invalid-feedback>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="birthdate">{{ $t("common.birthdate") }}:</label>

                                    <date-picker
                                        v-model="guest.birthdate"
                                        :language="locale"
                                        input-class="form-control"
                                        :format="dateFormat">
                                    </date-picker>

                                    <invalid-feedback :message="getError('birthdate')"></invalid-feedback>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="reset">
                        {{ $t('common.close') }}
                    </button>
                    <button type="button" class="btn btn-primary" @click="create()">
                        {{ $t('common.create') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DatePicker from 'vuejs-datepicker'
import BaseComponent from '../BaseComponent'
import {en, es} from 'vuejs-datepicker/dist/locale'

export default {
    extends: BaseComponent,
    components: {
        DatePicker
    },
    props: {
        voucherHash: {
            type: String,
            required: true
        },
        genders: {
            type: Object,
            required: true
        }
    },
    mounted() {
        this.loadIdentificationTypes()
        this.loadCountries()
    },
    computed: {
        locale() {
            const locale = document.documentElement.lang || window.navigator.language.substr(0, 2)

            return this.languages[locale] || this.languages.en
        }
    },
    data() {
        return {
            identificationTypes: [],
            countries: [],
            languages: {
                en: en,
                es: es
            },
            guest: {
                identification_type_id: "",
                dni: "",
                name: "",
                last_name: "",
                email: "",
                address: "",
                phone: "",
                profession: "",
                gender: "",
                country_id: "",
                birthdate: ""
            }
        }
    },
    methods: {
        loadIdentificationTypes() {
            axios
                .get(route('api.v1.identification_types.index'))
                .then(response => {
                    this.identificationTypes = response.data
                })
                .catch(() => {
                    this.displayServerError()
                })
        },
        loadCountries() {
            axios
                .get(route('api.v1.countries.index'))
                .then(response => {
                    this.countries = response.data
                })
                .catch(() => {
                    this.displayServerError()
                })
        },
        rules() {
            return {
                identification_type_id: ['required', 'in:identificationTypes,hash'],
                dni: ['required', 'min:5', 'max:15', 'regex:[0-9a-zA-Z\.\-]'],
                name: ['required', 'min:3', 'max:150'],
                last_name: ['required', 'min:3', 'max:150'],
                email: ['email'],
                address: ['max:191'],
                phone: ['max:20'],
                profession: ['max:100'],
                gender: ['required', 'in:genders'],
                country_id: ['required', 'in:countries,hash'],
                birthdate: ['date']
            }
        },
        create() {
            this.validate(this.guest)

            if (this.isValid()) {
                axios
                    .post(route('api.v1.guests.store'), this.guest)
                    .then(response => {
                        window.location.href = route('vouchers.guests', {
                            id: this.voucherHash,
                            guest: response.data.hash
                        })
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 422) {
                            this.pushErrors(error.response.data.errors)
                        } else {
                            this.displayServerError()
                        }
                    })
            }
        },
        reset() {
            this.guest.identification_type_id = ""
            this.guest.dni = ""
            this.guest.name = ""
            this.guest.last_name = ""
            this.guest.email = ""
            this.guest.address = ""
            this.guest.phone = ""
            this.guest.profession = ""
            this.guest.gender = ""
            this.guest.country_id = ""
            this.guest.birthdate = ""
        },
        dateFormat(date) {
            return moment(date).format('YYYY-MM-DD');
        }
    }
}
</script>
