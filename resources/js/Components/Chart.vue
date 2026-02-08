<template>
    <div>
        <canvas :id="chartId" ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useDarkMode } from '../../composables/VoltPanel/useDarkMode';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    RadialLinearScale,
    LineController,
    BarController,
    PieController,
    DoughnutController,
    RadarController,
    PolarAreaController,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    LineController,
    BarController,
    PieController,
    DoughnutController,
    RadarController,
    PolarAreaController,
    Title,
    Tooltip,
    Legend,
    Filler
);

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['line', 'bar', 'pie', 'doughnut', 'radar', 'polarArea'].includes(value)
    },
    data: {
        type: Object,
        required: true
    },
    options: {
        type: Object,
        default: () => ({})
    }
});

const chartCanvas = ref(null);
const chartId = ref(`chart-${Math.random().toString(36).substr(2, 9)}`);
let chartInstance = null;

// Get dark mode state
const { isDark } = useDarkMode();

// Helper function to get color based on current dark mode state
const getCssColor = (fallbackLight, fallbackDark) => {
    // Check DOM directly for dark mode class (more reliable than reactive state during initial render)
    const isDarkMode = document.documentElement.classList.contains('dark');
    return isDarkMode ? fallbackDark : fallbackLight;
};

const createChart = () => {
    if (!chartCanvas.value) return;

    // Destroy existing chart if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');

    // Default options with dark mode support
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    color: getCssColor('#1f2937', '#e5e7eb'),
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: getCssColor('#ffffff', '#1e293b'),
                titleColor: getCssColor('#1f2937', '#e5e7eb'),
                bodyColor: getCssColor('#1f2937', '#e5e7eb'),
                borderColor: getCssColor('#e5e7eb', '#334155'),
                borderWidth: 1,
                padding: 12,
                displayColors: true,
                boxPadding: 6
            }
        },
        scales: ['line', 'bar', 'radar'].includes(props.type) ? {
            y: {
                beginAtZero: true,
                grid: {
                    color: getCssColor('#e5e7eb', '#334155')
                },
                ticks: {
                    color: getCssColor('#6b7280', '#94a3b8')
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: getCssColor('#6b7280', '#94a3b8')
                }
            }
        } : {}
    };

    // Merge default options with provided options
    const mergedOptions = {
        ...defaultOptions,
        ...props.options,
        plugins: {
            ...defaultOptions.plugins,
            ...(props.options.plugins || {})
        }
    };

    chartInstance = new ChartJS(ctx, {
        type: props.type,
        data: props.data,
        options: mergedOptions
    });
};

onMounted(() => {
    createChart();
});

// Watch for data changes
watch(() => props.data, () => {
    createChart();
}, { deep: true });

// Watch for dark mode changes
watch(isDark, () => {
    createChart();
});

// Cleanup on unmount
import { onUnmounted } from 'vue';
onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>
