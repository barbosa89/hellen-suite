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
                        {{ status(record.status) }}
                    </td>
                    <td>
                        <dropdown-button>
                            <a v-if="$can('guests.show')" :href="route('guests.show', record.hash)" class="dropdown-item">
                                {{ $t('common.show') }}
                            </a>
                            <a v-if="$can('guests.edit')" :href="route('guests.edit', record.hash)" class="dropdown-item">
                                {{ $t('common.edit') }}
                            </a>
                            <a v-if="$can('guests.destroy')" href="#" :data-url="route('guests.destroy', record.hash)" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)" class="dropdown-item">
                                {{ $t('common.delete.item') }}
                            </a>
                        </dropdown-button>
                    </td>
                </template>
            </vue-table>
        </div>
    </div>
</template>

<script>
import Status from './Status'

export default {
    mixins: [
        Status
    ],
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
    }
}
</script>
