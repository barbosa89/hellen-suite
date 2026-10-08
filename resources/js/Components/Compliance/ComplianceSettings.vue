<script setup>
import CoTraSettings from '@/Components/Compliance/CoTraSettings.vue';
import GenericComplianceSettings from '@/Components/Compliance/GenericComplianceSettings.vue';
import {
    COMPLIANCE_STRATEGY_CO_TRA,
    complianceStrategyFor,
} from '@/Components/Compliance/strategies.js';
import { computed } from 'vue';

const props = defineProps({
    countryCode: {
        type: String,
        default: '',
    },
    errors: { type: Object, default: () => ({}) },
    configured: {
        type: Boolean,
        default: false,
    },
});

const complianceEnabled = defineModel('complianceEnabled', {
    type: Boolean,
    default: false,
});
const establishmentCode = defineModel('establishmentCode', {
    type: String,
    default: '',
});
const credential = defineModel('credential', {
    type: String,
    default: '',
});

const strategy = computed(() => complianceStrategyFor(props.countryCode));
const isCoTra = computed(() => strategy.value === COMPLIANCE_STRATEGY_CO_TRA);
</script>

<template>
    <CoTraSettings
        v-if="isCoTra"
        v-model:compliance-enabled="complianceEnabled"
        v-model:establishment-code="establishmentCode"
        v-model:credential="credential"
        :errors="errors"
        :configured="configured"
    />
    <GenericComplianceSettings v-else />
</template>
