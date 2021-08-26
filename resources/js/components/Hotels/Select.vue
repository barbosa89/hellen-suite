<template>
    <select name="hotel" id="hotel" class="form-control" @change="selectedHotel" v-model="hotel">
        <option :value="''" disabled selected>{{ $t('hotels.choose') }}</option>
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
                hotels: [],
                hotel: ''
            }
        },
        methods: {
            loadHotels() {
                axios
                    .post(route('hotels.assigned'))
                    .then(response => {
                        if (response.data.hotels.length) {
                            this.hotels = response.data.hotels
                        }
                    })
                    .finally(() => {
                        this.selectFirst()
                    })
            },
            selectFirst() {
                if (this.hotels.length) {
                    this.hotel = _.first(this.hotels).hash

                    this.selectedHotel()
                }
            },
            selectedHotel() {
                this.$emit('hotel', this.hotel)
                this.$root.$emit('hotel-selected', this.hotel)
            }
        },
    }
</script>
