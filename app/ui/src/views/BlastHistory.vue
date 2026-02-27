<template>
  <Layout>
    <div class="space-y-4">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Blast History</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View your SMS batch campaigns</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-500 dark:text-gray-400">Loading batches...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="batches.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 border border-gray-100 dark:border-gray-700 text-center">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400">No batches found</p>
        <router-link to="/blast" class="mt-4 inline-block text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium">
          Create your first batch →
        </router-link>
      </div>

      <!-- Batches Stack -->
      <div v-else class="space-y-3">
        <div
          v-for="batch in batches"
          :key="batch.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ batch.name }}</h3>
                <span
                  class="px-2.5 py-1 text-xs font-medium rounded-full"
                  :class="getStatusClass(batch.status)"
                >
                  {{ batch.status }}
                </span>
              </div>
              <p class="text-xs text-gray-400 dark:text-gray-500">
                Created: {{ formatDate(batch.created_at) }}
              </p>
            </div>
            <button
              @click="viewBatch(batch.id)"
              class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-4 gap-3 mb-4">
            <div class="text-center">
              <div class="text-lg font-bold text-gray-900 dark:text-white">{{ batch.total_recipients }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ batch.sent_count }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sent</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-red-600 dark:text-red-400">{{ batch.failed_count }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Failed</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ getProgress(batch) }}%</div>
              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Progress</div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div v-if="batch.status === 'processing' || batch.status === 'pending'" class="mt-4">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: getProgress(batch) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="flex justify-center items-center gap-2 pt-4">
        <button
          @click="loadPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95"
        >
          Previous
        </button>
        <span class="text-sm text-gray-700 dark:text-gray-300 px-4">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button
          @click="loadPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95"
        >
          Next
        </button>
      </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const batches = ref([]);
const loading = ref(false);
const pagination = ref(null);
let refreshInterval: number | null = null;

const loadBatches = async (page = 1) => {
  loading.value = true;
  try {
    const response = await api.getBatches({ page, per_page: 20 });
    batches.value = response.data.data || [];
    pagination.value = response.data;
  } catch (error) {
    console.error('Failed to load batches:', error);
  } finally {
    loading.value = false;
  }
};

const viewBatch = (id: number) => {
  // Could open a modal or navigate to detail view
  console.log('View batch:', id);
};

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
    processing: 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
    completed: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
    failed: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
  };
  return classes[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
};

const getProgress = (batch: any) => {
  if (batch.total_recipients === 0) return 0;
  const processed = batch.sent_count + batch.failed_count;
  return Math.round((processed / batch.total_recipients) * 100);
};

const loadPage = (page: number) => {
  loadBatches(page);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleString();
};

onMounted(() => {
  loadBatches();
  // Refresh every 5 seconds to update progress
  refreshInterval = window.setInterval(() => {
    loadBatches(pagination.value?.current_page || 1);
  }, 5000);
});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});
</script>
