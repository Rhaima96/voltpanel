<template>
    <div class="relative">
        <!-- Search Button -->
        <button
            @click="open = true"
            class="flex items-center space-x-2 px-3 py-2 text-sm text-muted-foreground bg-accent hover:bg-accent/80 rounded-lg transition-all duration-200 hover:shadow-md group"
        >
            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="hidden md:inline font-medium">Search</span>
            <kbd class="hidden md:inline px-2 py-0.5 text-xs bg-muted rounded border border-border font-mono">
                ⌘K
            </kbd>
        </button>

        <!-- Search Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="open"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <!-- Backdrop with blur -->
                    <div
                        class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                        @click="open = false"
                    ></div>

                    <!-- Modal Container -->
                    <div class="flex min-h-screen items-start justify-center p-4 pt-20" @click.self="open = false">
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 scale-95 -translate-y-4"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 -translate-y-4"
                        >
                            <div
                                v-if="open"
                                class="relative w-full max-w-2xl bg-card rounded-xl shadow-2xl border border-border overflow-hidden"
                                @click.stop
                            >
                                <!-- Search Input with Icon -->
                                <div class="relative border-b border-border">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input
                                        ref="searchInput"
                                        v-model="query"
                                        type="text"
                                        placeholder="Search for anything..."
                                        class="w-full pl-12 pr-20 py-4 bg-transparent text-lg focus:outline-none text-card-foreground placeholder:text-muted-foreground"
                                        @input="search"
                                    />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center space-x-2">
                                        <kbd class="px-2 py-1 text-xs bg-muted text-muted-foreground rounded border border-border font-mono">
                                            ESC
                                        </kbd>
                                    </div>
                                </div>

                                <!-- Results -->
                                <div class="max-h-96 overflow-y-auto">
                                    <!-- Loading State -->
                                    <div v-if="loading" class="p-12 text-center">
                                        <div class="inline-flex items-center justify-center">
                                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <p class="mt-3 text-sm text-muted-foreground">Searching...</p>
                                    </div>

                                    <!-- No Results -->
                                    <div v-else-if="results.length === 0 && query.length >= 2" class="p-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-muted mb-4">
                                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-card-foreground">No results found</p>
                                        <p class="mt-1 text-xs text-muted-foreground">Try searching with different keywords</p>
                                    </div>

                                    <!-- Empty State -->
                                    <div v-else-if="query.length < 2" class="p-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-card-foreground">Start searching</p>
                                        <p class="mt-1 text-xs text-muted-foreground">Type at least 2 characters to begin</p>
                                    </div>

                                    <!-- Results List -->
                                    <div v-else class="p-3 space-y-6">
                                        <div
                                            v-for="group in results"
                                            :key="group.resource"
                                            class="space-y-1"
                                        >
                                            <div class="px-3 py-2 text-xs font-bold text-primary uppercase tracking-wider flex items-center">
                                                <div class="w-1 h-4 bg-primary rounded-full mr-2"></div>
                                                {{ group.label }}
                                            </div>
                                            <Link
                                                v-for="record in group.records"
                                                :key="record.id"
                                                :href="record.url"
                                                class="flex items-center px-3 py-3 rounded-lg hover:bg-accent/50 transition-all duration-150 group border border-transparent hover:border-border"
                                                @click="open = false"
                                            >
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-card-foreground group-hover:text-primary transition-colors">
                                                        {{ record.title }}
                                                    </span>
                                                </div>
                                                <svg class="w-4 h-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer with keyboard hints -->
                                <div v-if="results.length > 0" class="border-t border-border bg-muted/30 px-4 py-3">
                                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-1">
                                                <kbd class="px-1.5 py-0.5 bg-background rounded border border-border font-mono">↑</kbd>
                                                <kbd class="px-1.5 py-0.5 bg-background rounded border border-border font-mono">↓</kbd>
                                                <span class="ml-1">to navigate</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <kbd class="px-1.5 py-0.5 bg-background rounded border border-border font-mono">↵</kbd>
                                                <span class="ml-1">to select</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <kbd class="px-1.5 py-0.5 bg-background rounded border border-border font-mono">ESC</kbd>
                                            <span class="ml-1">to close</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useRouting } from '../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const open = ref(false);
const query = ref('');
const results = ref([]);
const loading = ref(false);
const searchInput = ref(null);

let searchTimeout = null;

const search = () => {
    clearTimeout(searchTimeout);

    if (query.value.length < 2) {
        results.value = [];
        return;
    }

    loading.value = true;

    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get(route('voltpanel.global-search'), {
                params: { query: query.value },
            });
            results.value = response.data.results;
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            loading.value = false;
        }
    }, 300);
};

const handleKeydown = (event) => {
    // Cmd+K or Ctrl+K
    if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
        event.preventDefault();
        open.value = !open.value;
    }

    // Escape
    if (event.key === 'Escape' && open.value) {
        open.value = false;
    }
};

watch(open, async (value) => {
    if (value) {
        await nextTick();
        searchInput.value?.focus();
    } else {
        query.value = '';
        results.value = [];
    }
});

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>
