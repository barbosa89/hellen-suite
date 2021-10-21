<script>
export default {
    template: '<div></div>',
    data() {
        return {
            errors: {}
        }
    },
    methods: {
        isUnprocessableEntity (error) {
            return Boolean(error.response) && error.response.status === 422
        },
        pushErrors(errors) {
            this.errors = {}

            Object.assign(this.errors, errors)
        },
        pushError(key, value) {
            if (this.isUndefined(this.errors[key])) {
                this.$set(this.errors, key, value)
            }
        },
        hasError(property) {
            return this.errors.hasOwnProperty(property)
        },
        getError(property) {
            return this.hasError(property) ? this.findError(property) : ''
        },
        findError(property) {
            if (Array.isArray(this.errors[property])) {
                return this.errors[property][0]
            }

            return this.errors[property]
        },
        displayServerError() {
            toastr.error(
                this.$root.$t('common.error'),
                'Error'
            )
        },
        isUndefined(value) {
            return value === undefined
        },
        isEmpty(value) {
            return value.length === 0
        },
        isNull(value) {
            return value === null
        },
        isObject(value) {
            return !this.isNull(value)
                && !Array.isArray(value)
                && typeof value === 'object'
        },
        isCollection(value) {
            return Array.isArray(value) && this.isObject(value[0])
        },
        isNumber(value) {
            if (this.blank(value)) {
                return false
            }

            value = Number(value)

            return !Number.isNaN(value) && typeof value === 'number'
        },
        hasKeys(value) {
            return Object.keys(value).length > 0
        },
        blank(value) {
            if (this.isObject(value)) {
                return !this.hasKeys(value)
            }

            if (this.isCollection(value)) {
                return value.filter(item => this.hasKeys(item)).length === 0
            }

            return this.isUndefined(value) || this.isNull(value) || this.isEmpty(value)
        },
        required(value) {
            return !this.isUndefined(value) && !this.blank(value)
        },
        min(value, min) {
            if (this.blank(value) || this.blank(min)) {
                return false
            }

            return value.length >= min
        },
        max(value, max) {
            if (this.isUndefined(value) || this.isNull(value) || this.blank(max)) {
                return false
            }

            return value.length <= max
        },
        in(data, value, property=null) {
            if (this.isUndefined(this[data]) || this.blank(value)) {
                return false
            }

            if (this.isObject(this[data])) {
                return this[data].hasOwnProperty(value)
            }

            if (this.isCollection(this[data])) {
                let params = {}

                params[property] = value

                return _.find(this[data], params) !== undefined
            }

            return this[data].includes(value)
        },
        isIn(method) {
            return method === 'in'
        },
        email(value) {
            if (this.blank(value)) {
                return false
            }

            return value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)
        },
        date(value) {
            return new Date(value) > 0
        },
        regex(value, pattern) {
            if (this.blank(value) || this.blank(pattern)) {
                return false
            }

            const regex = new RegExp(pattern, 'g')

            return regex.test(value)
        },
        rules() {
            return {}
        },
        validate(data) {
            this.errors = {}
            const rules = this.rules()

            Object.entries(data).forEach(([key, value]) => {
                let conditions = rules[key]

                if (!this.isUndefined(conditions)) {
                    let skip = !conditions.includes('required') && this.blank(value)

                    conditions.forEach(condition => {
                        let passes = true
                        let [method, params=null] = condition.split(':')

                        if (this.isIn(method)) {
                            let [source, property=null] = params.split(',')

                            passes = this[method](source, value, property)
                        } else {
                            passes = this[method](...[value, params])
                        }

                        if (!passes && !skip) {
                            let attribute = this.getAttribute(key)
                            let message = this.getMessage(method, attribute, value, params)

                            this.pushError(key, message)
                        }
                    })
                }
            })
        },
        getAttribute(attr) {
            let key = 'validation.attributes.' + attr

            let translation = this.$root.$t(key)

            // if key is equal to translation,
            // it means that this attr has no translation
            // then, return attr
            return key === translation ? attr : translation
        },
        getMessage(method, attr, value, params=null) {
            let attributes = { attribute: attr }
            const key = 'validation.' + this.matchMethodType(method, value)
            const fallback = this.$root.$t('validation.required', attributes)

            if (!this.blank(params)) {
                attributes = Object.assign(attributes, this.combineParams(key, params))
            }

            let translation = this.$root.$t(key, attributes)

            // Same case for getAttribute method
            return key === translation ? fallback : translation
        },
        matchMethodType(method, value) {
            const types = ['between', 'max', 'min', 'size']

            if (types.includes(method)) {
                return this.findMethodType(method, value)
            }

            return method
        },
        findMethodType(method, value) {
            if (Array.isArray(value)) {
                return method + '.' + 'array'
            }

            if (this.isNumber(value)) {
                return method + '.' + 'numeric'
            }

            return method + '.' + 'string'
        },
        matchTranslationKeys(key) {
            let translation = this.$root.$t(key)

            const matches = translation.matchAll(/{([a-z}]+)}/g)

            const keys = Array.from(matches, m => m[1])

            return keys.filter(param => param !== 'attribute')
        },
        combineParams(key, params) {
            let entries = []
            let keys = this.matchTranslationKeys(key)

            params = params.split(',')

            keys.forEach((value, index) => {
                entries.push([value, params[index]])
            })

            return Object.fromEntries(entries)
        },
        isValid() {
            return Object.keys(this.errors).length === 0
        }
    }
}
</script>
