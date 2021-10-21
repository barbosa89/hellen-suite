import lodash from 'lodash'
import toastr from 'toastr'
import { config, mount } from '@vue/test-utils'
import BaseComponent from '../../resources/js/components/BaseComponent'
import translations from '../../resources/js/vue-i18n-locales.generated'

window._ = lodash
window.toastr = toastr

const locale = 'en'

config.mocks["$t"] = (key, params = {}) => {
    let trans = translations[locale]

    key.split('.').forEach(property => {
        trans = trans[property] || key
    })

    if (params.hasOwnProperty('attribute')) {
        Object.entries(params).forEach(([property, value]) => {
            trans = trans.replace(`{${property}}`, value)
        })
    }

    return trans
}

describe('BaseComponent.vue', () => {
    it('check on mounted the errors are empty', () => {
        const wrapper = mount(BaseComponent)

        expect(Object.keys(wrapper.vm.errors).length).toBe(0);
    })

    it('check can push errors object', () => {
        const errors = {
            field: ['error description']
        }

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushErrors(errors)

        expect(Object.keys(wrapper.vm.errors).length).toBe(1)
    })

    it('check can push an error', () => {
        const key = 'key'
        const error = 'error description'

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushError(key, error)

        expect(Object.keys(wrapper.vm.errors).length).toBe(1)
        expect(wrapper.vm.errors[key]).toBe(error)
    })

    it('check error is not duplicated on push', () => {
        const key = 'key'
        const error = 'error description'

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushError(key, error)
        wrapper.vm.pushError(key, error)

        expect(Object.keys(wrapper.vm.errors).length).toBe(1)
        expect(wrapper.vm.errors[key]).toBe(error)
    })

    it('check has error', () => {
        const key = 'key'
        const error = 'error description'

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushError(key, error)

        expect(wrapper.vm.hasError(key)).toBeTruthy()
        expect(wrapper.vm.hasError('unknown')).toBeFalsy()
    })

    it('can get an error', () => {
        const key = 'key'
        const error = 'error description'

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushError(key, error)

        expect(wrapper.vm.getError(key)).toBe(error)
    })

    it('get empty string if error does not exists', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getError('key')).toBe('')
    })

    it('can get an error from array', () => {
        const key = 'key'
        const error = 'error description'

        const wrapper = mount(BaseComponent)

        wrapper.vm.pushError(key, [error])

        expect(wrapper.vm.getError(key)).toBe(error)
    })

    it('can display toastr error', () => {
        const mock = jest.spyOn(toastr, 'error')

        const wrapper = mount(BaseComponent)

        wrapper.vm.displayServerError()

        expect(mock).toBeCalled()
    })

    it('can check empty objects', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isEmpty('')).toBeTruthy()
        expect(wrapper.vm.isEmpty([])).toBeTruthy()
        expect(wrapper.vm.isEmpty('a')).toBeFalsy()
        expect(wrapper.vm.isEmpty([1])).toBeFalsy()

    })

    it('can check null objects', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isNull(null)).toBeTruthy()
        expect(wrapper.vm.isNull('a')).toBeFalsy()
    })

    it('can check var is an object', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isObject({})).toBeTruthy()
        expect(wrapper.vm.isObject('a')).toBeFalsy()
        expect(wrapper.vm.isObject([])).toBeFalsy()
        expect(wrapper.vm.isObject('')).toBeFalsy()
        expect(wrapper.vm.isObject(1)).toBeFalsy()
        expect(wrapper.vm.isObject(null)).toBeFalsy()
    })

    it('can check var is a collection', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isCollection([{}])).toBeTruthy()
        expect(wrapper.vm.isCollection('a')).toBeFalsy()
        expect(wrapper.vm.isCollection([])).toBeFalsy()
        expect(wrapper.vm.isCollection(['a'])).toBeFalsy()
        expect(wrapper.vm.isCollection('')).toBeFalsy()
        expect(wrapper.vm.isCollection(1)).toBeFalsy()
        expect(wrapper.vm.isCollection(null)).toBeFalsy()
    })

    it('can check var is blank', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.blank([])).toBeTruthy()
        expect(wrapper.vm.blank('')).toBeTruthy()
        expect(wrapper.vm.blank(null)).toBeTruthy()
        expect(wrapper.vm.blank(undefined)).toBeTruthy()
        expect(wrapper.vm.blank({})).toBeTruthy()
        expect(wrapper.vm.blank([{}])).toBeTruthy()
        expect(wrapper.vm.blank('a')).toBeFalsy()
        expect(wrapper.vm.blank(['a'])).toBeFalsy()
        expect(wrapper.vm.blank(1)).toBeFalsy()
    })

    it('can check required values', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.required([])).toBeFalsy()
        expect(wrapper.vm.required('')).toBeFalsy()
        expect(wrapper.vm.required(null)).toBeFalsy()
        expect(wrapper.vm.required(undefined)).toBeFalsy()
        expect(wrapper.vm.required({})).toBeFalsy()
        expect(wrapper.vm.required([{}])).toBeFalsy()
        expect(wrapper.vm.required('a')).toBeTruthy()
        expect(wrapper.vm.required(['a'])).toBeTruthy()
        expect(wrapper.vm.required(1)).toBeTruthy()
        expect(wrapper.vm.required({ a: 1 })).toBeTruthy()
        expect(wrapper.vm.required([{ a: 1 }])).toBeTruthy()
    })

    it('can check min length allowed', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.min('a', 1)).toBeTruthy()
        expect(wrapper.vm.min('ab', 1)).toBeTruthy()
        expect(wrapper.vm.min('', 1)).toBeFalsy()
        expect(wrapper.vm.min(['a'], 1)).toBeTruthy()
        expect(wrapper.vm.min(['a', 'b'], 1)).toBeTruthy()
        expect(wrapper.vm.min([], 1)).toBeFalsy()
    })

    it('can check max length allowed', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.max('a', 1)).toBeTruthy()
        expect(wrapper.vm.max('', 1)).toBeTruthy()
        expect(wrapper.vm.max('ab', 1)).toBeFalsy()
        expect(wrapper.vm.max(['a'], 1)).toBeTruthy()
        expect(wrapper.vm.max([], 1)).toBeTruthy()
        expect(wrapper.vm.max(['a', 'b'], 1)).toBeFalsy()
        expect(wrapper.vm.max(null, 1)).toBeFalsy()
        expect(wrapper.vm.max(undefined, 1)).toBeFalsy()
    })

    it('can check value is in list or source', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.in('unknown', 1)).toBeFalsy()
        expect(wrapper.vm.in('errors', null)).toBeFalsy()
        expect(wrapper.vm.in('errors', undefined)).toBeFalsy()
        expect(wrapper.vm.in('errors', '')).toBeFalsy()
        expect(wrapper.vm.in('errors', {})).toBeFalsy()
        expect(wrapper.vm.in('errors', [])).toBeFalsy()
        expect(wrapper.vm.in('errors', 'key')).toBeFalsy()

        wrapper.vm.pushError('key', 'value')

        expect(wrapper.vm.in('errors', 'key')).toBeTruthy()

        wrapper.vm.myCollection = [{ count: 1 }]
        expect(wrapper.vm.in('myCollection', 2, 'count')).toBeFalsy()
        expect(wrapper.vm.in('myCollection', 1, 'count')).toBeTruthy()

        wrapper.vm.myList = [1]
        expect(wrapper.vm.in('myList', 2)).toBeFalsy()
        expect(wrapper.vm.in('myList', 1)).toBeTruthy()
    })

    it('can check if validation method is IN', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isIn('in')).toBeTruthy()
        expect(wrapper.vm.isIn('eq')).toBeFalsy()
    })

    it('can check if is valid email ', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.email('some@email.com')).toBeTruthy()
        expect(wrapper.vm.email('eq')).toBeFalsy()
        expect(wrapper.vm.email('')).toBeFalsy()
        expect(wrapper.vm.email(null)).toBeFalsy()
        expect(wrapper.vm.email(undefined)).toBeFalsy()
        expect(wrapper.vm.email([])).toBeFalsy()
        expect(wrapper.vm.email({})).toBeFalsy()
        expect(wrapper.vm.email('bad@@email.com')).toBeFalsy()
        expect(wrapper.vm.email('bad@email..com')).toBeFalsy()
        expect(wrapper.vm.email('bademail.com')).toBeFalsy()
        expect(wrapper.vm.email('bad email.com')).toBeFalsy()
        expect(wrapper.vm.email('@email.com')).toBeFalsy()
        expect(wrapper.vm.email('bad@.com')).toBeFalsy()
        expect(wrapper.vm.email('bad@email.comm')).toBeFalsy()
    })

    it('can check if is valid date ', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.date('2021-01-01')).toBeTruthy()
        expect(wrapper.vm.date('01-01-2021')).toBeTruthy()
        expect(wrapper.vm.date('eq')).toBeFalsy()
        expect(wrapper.vm.date('')).toBeFalsy()
        expect(wrapper.vm.date(null)).toBeFalsy()
        expect(wrapper.vm.date(undefined)).toBeFalsy()
        expect(wrapper.vm.date([])).toBeFalsy()
        expect(wrapper.vm.date({})).toBeFalsy()
        expect(wrapper.vm.date('20210101')).toBeFalsy()
        expect(wrapper.vm.date('01012021')).toBeFalsy()
    })

    it('can check if value match with regex ', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.regex('ab', '[a-z]')).toBeTruthy()
        expect(wrapper.vm.regex(1, '^\\d+$')).toBeTruthy()
        expect(wrapper.vm.regex('1', '^\\d+$')).toBeTruthy()
        expect(wrapper.vm.regex('', '[a-z]')).toBeFalsy()
        expect(wrapper.vm.regex('', '')).toBeFalsy()
        expect(wrapper.vm.regex('a', '^\\d+$')).toBeFalsy()
        expect(wrapper.vm.regex(null, null)).toBeFalsy()
        expect(wrapper.vm.regex(undefined, undefined)).toBeFalsy()
        expect(wrapper.vm.regex(null, undefined)).toBeFalsy()
        expect(wrapper.vm.regex(undefined, null)).toBeFalsy()
        expect(wrapper.vm.regex([], '[a-z]')).toBeFalsy()
        expect(wrapper.vm.regex({}, '[a-z]')).toBeFalsy()
    })

    it('can get empty rules object ', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.rules()).toStrictEqual({})
    })

    it('can validate data object', () => {
        const wrapper = mount(BaseComponent)

        wrapper.vm.rules = function() {
            return {
                name: ['required', 'min:3', 'max:30'],
                category: ['required', 'in:categories'],
                email: ['required', 'email'],
                age: ['min:18']
            }
        }

        wrapper.vm.categories = ['vip', 'gold']

        const data = {
            name: 'John Doe',
            category: 'vip',
            email: 'bad email',
            age: null
        }

        wrapper.vm.validate(data)

        expect(wrapper.vm.isValid()).toBeFalsy()
    })

    it('can not validate data object when rule dont exists', () => {
        const wrapper = mount(BaseComponent)

        const data = {
            name: 'John Doe'
        }

        wrapper.vm.validate(data)

        expect(wrapper.vm.isValid()).toBeTruthy()
    })

    it('can get attribute translation', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getAttribute('country_id')).toBe('country')
        expect(wrapper.vm.getAttribute('unknown')).toBe('unknown')
    })

    it('can get error message translation on validation', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getMessage('date', 'date')).toBe('The date is not a valid date.')
        expect(wrapper.vm.getMessage('unknown', 'date')).toBe('The date field is required.')
    })

    it('can get error message translation with extra parameters on validation', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getMessage('min', 'name', 'Usr', '3')).toBe('The name must be at least 3 characters.')
    })

    it('can get error message translation with array data on validation', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getMessage('min', 'categories', ['a', 'b'], '3')).toBe('The categories must have at least 3 items.')
    })

    it('can get error message translation with numeric data on validation', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.getMessage('min', 'price', 99, '100')).toBe('The price must be at least 100.')
    })

    it('check error is unprocessable entity', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isUnprocessableEntity({})).toBeFalsy()
        expect(wrapper.vm.isUnprocessableEntity({ response: {} })).toBeFalsy()
        expect(wrapper.vm.isUnprocessableEntity({ response: { status: 500 } })).toBeFalsy()
        expect(wrapper.vm.isUnprocessableEntity({ response: { status: 422 } })).toBeTruthy()
    })

    it('check is value is valid number', () => {
        const wrapper = mount(BaseComponent)

        expect(wrapper.vm.isNumber({})).toBeFalsy()
        expect(wrapper.vm.isNumber([])).toBeFalsy()
        expect(wrapper.vm.isNumber('')).toBeFalsy()
        expect(wrapper.vm.isNumber('a')).toBeFalsy()
        expect(wrapper.vm.isNumber(NaN)).toBeFalsy()
        expect(wrapper.vm.isNumber(undefined)).toBeFalsy()
        expect(wrapper.vm.isNumber(null)).toBeFalsy()

        expect(wrapper.vm.isNumber(-1)).toBeTruthy()
        expect(wrapper.vm.isNumber(0)).toBeTruthy()
        expect(wrapper.vm.isNumber(1)).toBeTruthy()
        expect(wrapper.vm.isNumber('1')).toBeTruthy()
        expect(wrapper.vm.isNumber(1.1)).toBeTruthy()
        expect(wrapper.vm.isNumber('1.1')).toBeTruthy()

    })
})