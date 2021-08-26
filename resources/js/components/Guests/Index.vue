<template>
    <div class="row">
        <div class="col-md-12">
            <vue-table
                :headers="headers"
                :url="route('api.v1.guests.index')"
                search-icon="fa fa-search">
                <template v-slot:record="{ record }">
                    <td>
                        <a :href="route('guests.show', record.hash)">
                            {{ record.full_name }}
                        </a>
                    </td>
                    <td>
                            <a :href="route('guests.show', record.hash)">
                                {{ record.dni }}
                            </a>
                    </td>
                    <td>
                        Status
                    </td>
                    <td>

                    </td>
                </template>
            </vue-table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            url: route('guests.index'),
                headers: [
                    {
                        description: this.$root.$t('common.name')
                    },
                    {
                        description: this.$root.$t('common.idNumber')
                    },
                    {
                        description: this.$root.$t('common.status')
                    },
                    {
                        description: this.$root.$t('common.options')
                    },
                ]
        }
    },
}
</script>
