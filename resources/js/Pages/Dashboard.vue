<template>
    <PanelLayout>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ title }}
            </h1>
        </div>

        <!-- Widgets Grid -->
        <div v-if="widgets.length > 0" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <div
                v-for="(widget, index) in widgets"
                :key="index"
                :class="getColumnSpanClass(widget.columnSpan)"
            >
                <!-- Stats Widget -->
                <div
                    v-if="widget.type === 'StatsOverviewWidget'"
                    class="h-full bg-card rounded-lg shadow p-6 hover:shadow-lg transition-shadow flex flex-col border border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-muted-foreground">
                                {{ widget.data.heading }}
                            </p>
                            <p class="text-3xl font-bold mt-2">
                                {{ widget.data.value }}
                            </p>
                            <p v-if="widget.data.description" class="text-sm text-muted-foreground mt-2">
                                {{ widget.data.description }}
                            </p>
                        </div>
                        <div v-if="widget.data.icon" class="flex-shrink-0">
                            <Icon :name="widget.data.icon" class="w-12 h-12" :class="getIconColorClass(widget.data.color)" />
                        </div>
                    </div>
                    <Link
                        v-if="widget.data.url"
                        :href="widget.data.url"
                        class="inline-flex items-center mt-4 text-sm font-medium text-primary hover:underline"
                    >
                        View details
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Chart Widget -->
                <div
                    v-else-if="widget.type === 'ChartWidget'"
                    class="h-full bg-card rounded-lg shadow  hover:shadow-lg p-6 flex flex-col border border-gray-200 dark:border-gray-700"
                >
                    <h3 v-if="widget.data.heading" class="text-lg font-semibold mb-4">
                        {{ widget.data.heading }}
                    </h3>
                    <p v-if="widget.data.description" class="text-sm text-muted-foreground mb-4">
                        {{ widget.data.description }}
                    </p>
                    <div class="mt-4">
                        <Chart
                            v-if="widget.data.chartData"
                            :type="widget.data.type || 'line'"
                            :data="widget.data.chartData"
                            :options="widget.data.options || {}"
                        />
                        <div v-else class="text-sm text-muted-foreground">
                            <p>No chart data available</p>
                        </div>
                    </div>
                </div>

                <!-- Advanced Chart Widget -->
                <div
                    v-else-if="isAdvancedChartWidget(widget)"
                    class="h-full bg-card rounded-lg shadow hover:shadow-lg p-6 flex flex-col border border-gray-200 dark:border-gray-700"
                >
                    <AdvancedChart
                        v-if="widget.data.chartData"
                        :type="widget.data.type || 'line'"
                        :data="widget.data.chartData"
                        :heading="widget.data.heading"
                        :description="widget.data.description"
                        :options="widget.data.options || {}"
                        :show-export="widget.data.showExport || false"
                        :export-filename="widget.data.exportFilename || 'chart'"
                        :realtime="widget.data.realtime || false"
                        :realtime-interval="widget.data.realtimeInterval || 3000"
                        :realtime-callback="widget.data.realtimeCallback || null"
                        :color-scheme="widget.data.colorScheme || 'default'"
                    />
                    <div v-else class="text-sm text-muted-foreground">
                        <p>No chart data available</p>
                    </div>
                </div>

                <!-- Activity Widget -->
                <div
                    v-else-if="widget.type === 'ActivityLogWidget'"
                    class="h-full bg-card rounded-lg shadow hover:shadow-lg p-6 flex flex-col border border-gray-200 dark:border-gray-700"
                >
                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        <div
                            v-for="activity in widget.data.activities"
                            :key="activity.id"
                            class="flex items-start space-x-3 text-sm"
                        >
                            <div class="flex-1">
                                <p class="font-medium">{{ activity.description }}</p>
                                <p class="text-muted-foreground">
                                    by {{ activity.causer }} · {{ activity.created_at }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Base/Custom Widget -->
                <div
                    v-else
                    class="h-full bg-card rounded-lg shadow hover:shadow-lg p-6 flex flex-col border border-gray-200 dark:border-gray-700"
                >
                    <h3 v-if="widget.data.heading" class="text-lg font-semibold mb-4">
                        {{ widget.data.heading }}
                    </h3>
                    <pre class="text-sm text-muted-foreground">{{ JSON.stringify(widget.data, null, 2) }}</pre>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-lg">
                Welcome to VoltPanel!
            </div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Start by creating your first resource or adding widgets to your dashboard.
            </p>
        </div>
    </PanelLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PanelLayout from '../../layouts/VoltPanel/PanelLayout.vue';
import Icon from '../../components/VoltPanel/Icon.vue';
import Chart from '../../components/VoltPanel/Chart.vue';
import AdvancedChart from '../../components/VoltPanel/AdvancedChart.vue';

defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    widgets: {
        type: Array,
        default: () => [],
    },
});

const isAdvancedChartWidget = (widget) => {
    const advancedChartTypes = [
        'AdvancedChartWidget',
        'TimeSeriesChartWidget',
        'StatsChartWidget',
        'AdvanceChartdWidget'
    ];

    // Check if widget type matches known chart widget types
    if (advancedChartTypes.includes(widget.type)) {
        return true;
    }

    // Fallback: Check if it has chartData (for widgets that extend base Widget class)
    if (widget.type === 'Widget' && widget.data.chartData) {
        return true;
    }

    return false;
};

const getColumnSpanClass = (columnSpan) => {
    const spanMap = {
        1: 'col-span-1',
        2: 'col-span-2',
        3: 'col-span-3',
        4: 'col-span-4',
        5: 'col-span-5',
        6: 'col-span-6',
    };
    return spanMap[columnSpan] || 'col-span-2';
};

const getIconColorClass = (color) => {
    const colorMap = {
        primary: 'text-primary',
        success: 'text-green-600 dark:text-green-400',
        danger: 'text-red-600 dark:text-red-400',
        warning: 'text-yellow-600 dark:text-yellow-400',
        info: 'text-blue-600 dark:text-blue-400',
        indigo: 'text-indigo-600 dark:text-indigo-400',
        purple: 'text-purple-600 dark:text-purple-400',
        pink: 'text-pink-600 dark:text-pink-400',
        cyan: 'text-cyan-600 dark:text-cyan-400',
        gray: 'text-gray-600 dark:text-gray-400',
    };
    return colorMap[color] || 'text-primary';
};
</script>
