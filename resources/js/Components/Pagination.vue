<template>
    <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            <Link
                v-if="links[0].url"
                :href="links[0].url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                Previous
            </Link>
            <Link
                v-if="links[links.length - 1].url"
                :href="links[links.length - 1].url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                Next
            </Link>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Showing results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <component
                        v-for="(link, index) in links"
                        :key="index"
                        :is="link.url ? Link : 'span'"
                        :href="link.url"
                        :class="[
                            link.active
                                ? 'z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 text-primary-600 dark:text-primary-300'
                                : link.url
                                    ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                                    : 'bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-600 cursor-not-allowed',
                            index === 0 ? 'rounded-l-md' : '',
                            index === links.length - 1 ? 'rounded-r-md' : '',
                        ]"
                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
