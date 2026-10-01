<script setup>
import {
    CategoryScale,
    Chart,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

Chart.register(
    CategoryScale,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

const props = defineProps({
    points: {
        type: Array,
        required: true,
    },
});

const { t, locale } = useI18n();
const canvas = ref(null);
let chart;
let themeObserver;

const dateFormatter = computed(
    () =>
        new Intl.DateTimeFormat(locale.value === 'es' ? 'es-CO' : 'en-US', {
            weekday: 'short',
            day: 'numeric',
            timeZone: 'UTC',
        }),
);

const accessibleDescription = computed(() =>
    props.points
        .map((point) =>
            t('hotels.management.forecast.accessible_point', {
                date: formatDate(point.date),
                rate: point.occupancyRate,
            }),
        )
        .join(' '),
);

function formatDate(date) {
    return dateFormatter.value.format(new Date(`${date}T00:00:00Z`));
}

function palette() {
    const dark = document.documentElement.classList.contains('dark');

    return {
        line: dark ? '#26c6da' : '#00838f',
        fill: dark ? 'rgba(38, 198, 218, 0.14)' : 'rgba(0, 131, 143, 0.12)',
        grid: dark ? 'rgba(166, 166, 166, 0.15)' : 'rgba(89, 89, 89, 0.12)',
        text: dark ? '#c7c7c7' : '#595959',
        tooltipBackground: dark ? '#f8f8f8' : '#212121',
        tooltipText: dark ? '#141414' : '#ffffff',
    };
}

function chartData() {
    const colors = palette();

    return {
        labels: props.points.map((point) => formatDate(point.date)),
        datasets: [
            {
                data: props.points.map((point) => point.occupancyRate),
                borderColor: colors.line,
                backgroundColor: colors.fill,
                borderWidth: 2,
                fill: true,
                pointBackgroundColor: colors.line,
                pointBorderColor: colors.line,
                pointHoverRadius: 5,
                pointRadius: 3,
                tension: 0.34,
            },
        ],
    };
}

function chartOptions() {
    const colors = palette();

    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ? false
            : { duration: 450 },
        interaction: { intersect: false, mode: 'index' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: colors.tooltipBackground,
                bodyColor: colors.tooltipText,
                displayColors: false,
                padding: 12,
                titleColor: colors.tooltipText,
                callbacks: {
                    label: (context) =>
                        t('hotels.management.forecast.tooltip', {
                            rate: context.parsed.y,
                        }),
                },
            },
        },
        scales: {
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: { color: colors.text, font: { size: 11, weight: 600 } },
            },
            y: {
                beginAtZero: true,
                max: 100,
                border: { display: false },
                grid: { color: colors.grid },
                ticks: {
                    color: colors.text,
                    callback: (value) => `${value}%`,
                    font: { size: 11 },
                    stepSize: 25,
                },
            },
        },
    };
}

function renderChart() {
    if (!canvas.value) {
        return;
    }

    chart?.destroy();
    chart = new Chart(canvas.value, {
        type: 'line',
        data: chartData(),
        options: chartOptions(),
    });
}

onMounted(() => {
    renderChart();
    themeObserver = new MutationObserver(renderChart);
    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
});

watch(() => props.points, renderChart, { deep: true });
watch(locale, renderChart);

onBeforeUnmount(() => {
    themeObserver?.disconnect();
    chart?.destroy();
});
</script>

<template>
    <div class="relative h-64 sm:h-72">
        <canvas ref="canvas" role="img" :aria-label="accessibleDescription" />
        <p class="sr-only">{{ accessibleDescription }}</p>
    </div>
</template>
