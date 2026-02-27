<template>
  <Layout>
    <div class="space-y-4">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Inbox</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ messages.length }} messages</p>
        </div>
        <div class="flex gap-2">
          <button
            @click="handleSync"
            :disabled="syncing"
            class="flex-1 sm:flex-none px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium active:scale-95 transition-all"
          >
            {{ syncing ? 'Syncing...' : 'Sync Now' }}
          </button>
          <button
            @click="handleMarkAllRead"
            class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium active:scale-95 transition-all"
          >
            Mark All Read
          </button>
        </div>
      </div>

      <!-- Sync Result Alert -->
      <div v-if="syncResult" class="p-4 rounded-xl" :class="syncResult.success ? 'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200'">
        <div class="flex items-center gap-2">
          <svg v-if="syncResult.success" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm font-medium">{{ syncResult.message || (syncResult.success ? `Synced ${syncResult.synced} new messages` : syncResult.error) }}</span>
        </div>
      </div>

      <!-- Search Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search messages..."
          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          @input="loadMessages"
        />
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-500 dark:text-gray-400">Loading messages...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="messages.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 border border-gray-100 dark:border-gray-700 text-center">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400">No messages found</p>
      </div>

      <!-- Messages Stack -->
      <div v-else class="space-y-3">
        <div
          v-for="message in messages"
          :key="message.id"
          @click="selectMessage(message)"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-[0.98] cursor-pointer"
          :class="{ 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': !message.is_read }"
        >
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ message.phone_number }}</span>
                  <span v-if="!message.is_read" class="px-2 py-0.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full">
                    New
                  </span>
                </div>
                <button
                  @click.stop="markAsRead(message.id)"
                  v-if="!message.is_read"
                  class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium"
                >
                  Mark read
                </button>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">
                {{ message.message_text }}
              </p>
              <p class="text-xs text-gray-400 dark:text-gray-500">
                {{ formatDate(message.received_at) }}
              </p>
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
import { ref, onMounted } from 'vue';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const messages = ref([]);
const loading = ref(false);
const syncing = ref(false);
const searchQuery = ref('');
const pagination = ref(null);
const syncResult = ref(null);

const loadMessages = async (page = 1) => {
  loading.value = true;
  try {
    const response = await api.getInbox({
      page,
      per_page: 20,
      search: searchQuery.value || undefined,
    });
    // API returns: { success: true, data: [...], pagination: {...} }
    // api.getInbox() already unwraps response.data, so response is the JSON body
    console.log('Inbox API Response:', response);
    messages.value = response.data || [];
    pagination.value = response.pagination || null;
    console.log('Loaded messages:', messages.value.length, 'Total:', pagination.value?.total);
  } catch (error) {
    console.error('Failed to load messages:', error);
    messages.value = [];
    pagination.value = null;
  } finally {
    loading.value = false;
  }
};

const handleSync = async () => {
  syncing.value = true;
  syncResult.value = null;
  try {
    const response = await api.syncInbox();
    syncResult.value = response;
    if (response.success) {
      await loadMessages();
      setTimeout(() => {
        syncResult.value = null;
      }, 5000);
    }
  } catch (error: any) {
    syncResult.value = {
      success: false,
      error: error.response?.data?.message || 'Sync failed',
    };
  } finally {
    syncing.value = false;
  }
};

const handleMarkAllRead = async () => {
  try {
    await api.markAllAsRead();
    await loadMessages();
  } catch (error) {
    console.error('Failed to mark all as read:', error);
  }
};

const markAsRead = async (id: number) => {
  try {
    await api.markAsRead(id);
    await loadMessages();
  } catch (error) {
    console.error('Failed to mark as read:', error);
  }
};

const selectMessage = (message: any) => {
  if (!message.is_read) {
    markAsRead(message.id);
  }
};

const loadPage = (page: number) => {
  loadMessages(page);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleString();
};

onMounted(() => {
  loadMessages();
});
</script>
