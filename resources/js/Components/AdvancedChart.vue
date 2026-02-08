<template>
  <div class="relative">
    <!-- Chart Header -->
    <div v-if="heading || showExport" class="flex items-center justify-between mb-4">
      <h3 v-if="heading" class="text-lg font-semibold text-foreground">{{ heading }}</h3>
      <div class="flex items-center gap-2">
        <!-- Export Button -->
        <button
          v-if="showExport"
          @click="exportChart"
          type="button"
          class="px-3 py-1.5 text-sm bg-secondary text-secondary-foreground rounded-lg hover:opacity-90 transition-opacity"
          title="Export chart as image"
        >
          <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Export
        </button>

        <!-- Refresh Button for Real-time -->
        <button
          v-if="realtime"
          @click="toggleRealtime"
          type="button"
          :class="[
            'px-3 py-1.5 text-sm rounded-lg transition-opacity',
            isRealtimeActive
              ? 'bg-primary text-primary-foreground'
              : 'bg-secondary text-secondary-foreground'
          ]"
          :title="isRealtimeActive ? 'Pause updates' : 'Resume updates'"
        >
          <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="isRealtimeActive" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Chart Canvas -->
    <div :class="['relative', containerClass]">
      <canvas :id="chartId" ref="chartCanvas"></canvas>
    </div>

    <!-- Legend (if custom position) -->
    <div v-if="customLegend" class="mt-4" ref="legendContainer"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, onUnmounted } from 'vue';
import { useDarkMode } from '../../composables/VoltPanel/useDarkMode';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  TimeScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  RadialLinearScale,
  BubbleController,
  ScatterController,
  LineController,
  BarController,
  PieController,
  DoughnutController,
  RadarController,
  PolarAreaController,
  Title,
  Tooltip,
  Legend,
  Filler,
  SubTitle
} from 'chart.js';
import 'chartjs-adapter-date-fns';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  LogarithmicScale,
  TimeScale,
  RadialLinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  BubbleController,
  ScatterController,
  LineController,
  BarController,
  PieController,
  DoughnutController,
  RadarController,
  PolarAreaController,
  Title,
  Tooltip,
  Legend,
  Filler,
  SubTitle
);

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['line', 'bar', 'pie', 'doughnut', 'radar', 'polarArea', 'scatter', 'bubble', 'mixed'].includes(value)
  },
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  },
  heading: {
    type: String,
    default: null
  },
  showExport: {
    type: Boolean,
    default: false
  },
  exportFilename: {
    type: String,
    default: 'chart'
  },
  realtime: {
    type: Boolean,
    default: false
  },
  realtimeInterval: {
    type: Number,
    default: 3000 // 3 seconds
  },
  realtimeCallback: {
    type: Function,
    default: null
  },
  customLegend: {
    type: Boolean,
    default: false
  },
  containerClass: {
    type: String,
    default: ''
  },
  colorScheme: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'pastel', 'vibrant', 'earth', 'ocean', 'sunset'].includes(value)
  }
});

const emit = defineEmits(['chart-created', 'data-updated']);

const chartCanvas = ref(null);
const legendContainer = ref(null);
const chartId = ref(`advanced-chart-${Math.random().toString(36).substr(2, 9)}`);
let chartInstance = null;
let realtimeIntervalId = null;
const isRealtimeActive = ref(false);

// Get dark mode state
const { isDark } = useDarkMode();

// Color schemes
const colorSchemes = {
  default: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444'],
  pastel: ['#a78bfa', '#fbbf24', '#34d399', '#60a5fa', '#f472b6', '#fb923c', '#a3e635'],
  vibrant: ['#ff0080', '#7928ca', '#ff0080', '#ff4d4d', '#f9cb28', '#61dafb', '#bd93f9'],
  earth: ['#8b4513', '#d2691e', '#cd853f', '#daa520', '#b8860b', '#a0522d', '#8b4513'],
  ocean: ['#006994', '#1e90ff', '#4682b4', '#5f9ea0', '#00ced1', '#48d1cc', '#40e0d0'],
  sunset: ['#ff6b6b', '#f06595', '#cc5de8', '#845ef7', '#5c7cfa', '#339af0', '#22b8cf']
};

// Helper function to get colors
const getChartColors = () => {
  return colorSchemes[props.colorScheme] || colorSchemes.default;
};

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

  // Apply color scheme to datasets if not already defined
  const processedData = { ...props.data };
  const colors = getChartColors();

  if (processedData.datasets) {
    const isPieType = ['pie', 'doughnut', 'polarArea'].includes(props.type);

    processedData.datasets = processedData.datasets.map((dataset, index) => {
      if (!dataset.backgroundColor) {
        if (isPieType) {
          // Pie/doughnut/polarArea charts need an array of colors (one per segment)
          const dataLength = dataset.data?.length || 0;
          const bgColors = [];
          const borderColors = [];
          for (let i = 0; i < dataLength; i++) {
            bgColors.push(colors[i % colors.length]);
            borderColors.push('#fff');
          }
          return {
            ...dataset,
            backgroundColor: bgColors,
            borderColor: borderColors,
            borderWidth: 2,
          };
        } else {
          const color = colors[index % colors.length];
          return {
            ...dataset,
            backgroundColor: props.type === 'line' ? `${color}33` : color,
            borderColor: color,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
          };
        }
      }
      return dataset;
    });
  }

  // Default options with dark mode support
  const defaultOptions = {
    responsive: true,
    maintainAspectRatio: true,
    interaction: {
      mode: 'index',
      intersect: false,
    },
    plugins: {
      legend: {
        display: !props.customLegend,
        position: 'bottom',
        labels: {
          color: getCssColor('#1f2937', '#e5e7eb'),
          padding: 15,
          font: {
            size: 12
          },
          usePointStyle: true,
          pointStyle: 'circle'
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
        boxPadding: 6,
        callbacks: {
          label: function(context) {
            let label = context.dataset.label || '';
            if (label) {
              label += ': ';
            }
            // For pie/doughnut/polarArea, use context.raw; for others use context.parsed.y
            const value = ['pie', 'doughnut', 'polarArea'].includes(context.chart.config.type)
              ? context.raw
              : context.parsed?.y;
            if (value !== null && value !== undefined) {
              label += new Intl.NumberFormat().format(value);
            }
            return label;
          }
        }
      },
      title: {
        display: false
      }
    },
    animation: {
      duration: 750,
      easing: 'easeInOutQuart'
    }
  };

  // Add scales for non-circular charts
  if (['line', 'bar', 'scatter', 'bubble'].includes(props.type)) {
    defaultOptions.scales = {
      y: {
        beginAtZero: true,
        grid: {
          color: getCssColor('#e5e7eb', '#334155'),
          drawBorder: false
        },
        ticks: {
          color: getCssColor('#6b7280', '#94a3b8'),
          callback: function(value) {
            return new Intl.NumberFormat().format(value);
          }
        }
      },
      x: {
        grid: {
          display: props.type !== 'bar',
          color: getCssColor('#e5e7eb', '#334155'),
          drawBorder: false
        },
        ticks: {
          color: getCssColor('#6b7280', '#94a3b8')
        }
      }
    };
  }

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
    type: props.type === 'mixed' ? 'bar' : props.type,
    data: processedData,
    options: mergedOptions
  });

  emit('chart-created', chartInstance);

  // Generate custom legend if needed
  if (props.customLegend && legendContainer.value) {
    legendContainer.value.innerHTML = chartInstance.generateLegend();
  }
};

// Export chart as image
const exportChart = () => {
  if (!chartInstance) return;

  const url = chartInstance.toBase64Image();
  const link = document.createElement('a');
  link.download = `${props.exportFilename}.png`;
  link.href = url;
  link.click();
};

// Real-time updates
const startRealtime = () => {
  if (!props.realtimeCallback) return;

  isRealtimeActive.value = true;
  realtimeIntervalId = setInterval(async () => {
    const newData = await props.realtimeCallback();
    if (newData && chartInstance) {
      chartInstance.data = newData;
      chartInstance.update('none'); // Update without animation for real-time
      emit('data-updated', newData);
    }
  }, props.realtimeInterval);
};

const stopRealtime = () => {
  isRealtimeActive.value = false;
  if (realtimeIntervalId) {
    clearInterval(realtimeIntervalId);
    realtimeIntervalId = null;
  }
};

const toggleRealtime = () => {
  if (isRealtimeActive.value) {
    stopRealtime();
  } else {
    startRealtime();
  }
};

onMounted(() => {
  createChart();

  if (props.realtime && props.realtimeCallback) {
    startRealtime();
  }
});

// Watch for data changes
watch(() => props.data, () => {
  if (!isRealtimeActive.value) {
    createChart();
  }
}, { deep: true });

// Watch for dark mode changes
watch(isDark, () => {
  createChart();
});

// Cleanup on unmount
onUnmounted(() => {
  stopRealtime();
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>
