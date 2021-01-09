<template>
    <div>
        <div class="row mb-4">
            <div class="col-12">
                <hotel-select @hotel='hotelId = $event'></hotel-select>
            </div>
        </div>

        <template v-if="hotelId.length > 0">
            <icon-cards :hotel-id='hotelId'></icon-cards>
        </template>
    </div>
</template>

<script>
    import IconCards from './IconCards'

    export default {
        data() {
            return {
                hotelId: ''
            }
        },
        components: {
            IconCards,
        }
    }
</script>
