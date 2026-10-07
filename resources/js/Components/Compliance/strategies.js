export const COMPLIANCE_STRATEGY_CO_TRA = 'co-tra';
export const COMPLIANCE_STRATEGY_GENERIC = 'generic';

export const COMPLIANCE_COUNTRIES = [
    { code: 'CO', name: 'Colombia' },
    { code: 'ES', name: 'España' },
    { code: 'MX', name: 'México' },
    { code: 'AR', name: 'Argentina' },
    { code: 'CL', name: 'Chile' },
    { code: 'PE', name: 'Perú' },
    { code: 'EC', name: 'Ecuador' },
    { code: 'BR', name: 'Brasil' },
    { code: 'US', name: 'Estados Unidos' },
    { code: 'PT', name: 'Portugal' },
    { code: 'IT', name: 'Italia' },
    { code: 'FR', name: 'Francia' },
    { code: 'DE', name: 'Alemania' },
];

export const COMPLIANCE_TIMEZONES = [
    'America/Bogota',
    'America/Mexico_City',
    'America/Lima',
    'America/Santiago',
    'America/Buenos_Aires',
    'America/Guayaquil',
    'America/Sao_Paulo',
    'Europe/Madrid',
    'UTC',
];

export function complianceStrategyFor(countryCode) {
    return countryCode === 'CO'
        ? COMPLIANCE_STRATEGY_CO_TRA
        : COMPLIANCE_STRATEGY_GENERIC;
}
