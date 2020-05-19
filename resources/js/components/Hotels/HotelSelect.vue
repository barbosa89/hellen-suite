<template>
    <select name="hotel" id="hotel" class="form-control" @change="selectedHotel" v-model="hotel">
        <option :value="''" disabled selected>{{ $t('common.chooseOption') }}</option>
        <option v-for="(hotel, index) in hotels" :key="index" :value="hotel.hash">
            {{ hotel.business_name }}
        </option>
    </select>
</template>

<script>
    export default {
        mounted() {
            this.loadHotels()
        },
        data() {
            return {
                hotels: Array,
                hotel: ''
            }
        },
        methods: {
            loadHotels() {
                axios.get('/hotels/assigned')
                    .then(response => {
                        if (response.data.length) {
                            this.hotels = response.data
                        } else {
                            // Redirect if the parent user has not created hotels
                            window.location.href = '/home'
                        }
                    })
            },
            selectedHotel() {
                this.$emit('hotel', this.hotel)
            }
        },
    }
</script>