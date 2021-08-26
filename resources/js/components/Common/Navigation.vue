<template>
    <nav class="navbar navbar-expand-lg navbar-light app-nav border-bottom mb-3 pb-3">
        <a class="navbar-brand text-muted" :href="url">
            {{ title }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <form v-if="hotelSelect" class="form-inline my-2 my-lg-0" :action="url" method="get">
                <div class="input-group">
                    <hotel-select></hotel-select>
                </div>
            </form>

            <slot></slot>
        </div>
    </nav>
</template>

<script>
import HotelSelect from '../Hotels/Select'

export default {
    props: {
        url: {
            type: String,
        },
        title: {
            type: String,
        },
        hotelSelect: {
            type: Boolean,
            default: false
        }
    },
    components: {
        HotelSelect
    }
}
</script>
