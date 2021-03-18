<template>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-book"></i>
            {{ $t('notes.title') }}
        </div>
        <div class="card-body p-0">
            <template v-if="notes.length > 0">
                <vue-table :headers='headers' :user-data='notes'>
                    <template v-slot:record="{ record }">
                        <td>{{ formatDate(record.created_at) }}</td>
                        <td v-html="record.content"></td>
                        <td>{{ record.team_member_name }}</td>
                    </template>
                </vue-table>
            </template>
            <template v-else>
                <div class="p-4 d-flex justify-content-center">
                    <em class="fas fa-spinner fa-pulse"></em>
                </div>
            </template>
        </div>
        <div class="card-footer small text-muted">{{ $t('common.updated.at') }}: {{ date.format('YY-MM-DD HH:mm:ss') }}</div>
    </div>
</template>

<script>
    import VueTable from '@barbosa89/vue-table'
import moment from 'moment'

    export default {
        mounted() {
            this.queryNotes()

        },
        props: {
            hotelId: {
                type: String,
            }
        },
        watch: {
            hotelId() {
                this.queryNotes()
            }
        },
        components: {
            VueTable
        },
        data() {
            return {
                notes: [],
                date: moment(),
                headers: [
                    {
                        description: this.$root.$t('common.date')
                    },
                    {
                        description: this.$root.$t('notes.content')
                    },
                    {
                        description: this.$root.$t('common.name')
                    }
                ]
            }
        },
        methods: {
            queryNotes() {
                axios
                    .get(route('api.web.notes.index', {hotel: this.hotelId}))
                    .then(response => {
                        this.notes = response.data.notes.data
                    })
            },
            formatDate(date) {
                return moment(date).format('YY-MM-DD HH:mm:ss')
            }
        }
    }
</script>
