<template>
  <Layout>
    <div class="space-y-4">
      <!-- Page Title -->
      <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your SMS activity</p>
      </div>

      <!-- Stats Cards Stack -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ stats.totalInbox }}</div>
          <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Total Inbox</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ stats.unreadInbox }}</div>
          <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Unread</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ stats.sentMessages }}</div>
          <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Sent</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ stats.totalBatches }}</div>
          <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Batches</div>
        </div>
      </div>

      <!-- Quick Actions Cards Stack -->
      <div class="mt-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
          <router-link
            to="/compose"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95"
          >
            <div class="flex items-start gap-4">
              <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Send SMS</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Compose a new message</p>
              </div>
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </router-link>

          <router-link
            to="/blast"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95"
          >
            <div class="flex items-start gap-4">
              <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">SMS Blast</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Send to multiple recipients</p>
              </div>
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </router-link>

          <router-link
            to="/inbox"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95"
          >
            <div class="flex items-start gap-4">
              <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">View Inbox</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Check received messages</p>
              </div>
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const stats = ref({
  totalInbox: 0,
  unreadInbox: 0,
  sentMessages: 0,
  totalBatches: 0,
});

onMounted(async () => {
  try {
    const [inboxRes, outboxRes, batchesRes] = await Promise.all([
      api.getInbox({ per_page: 1 }),
      api.getOutbox({ per_page: 1 }),
      api.getBatches({ per_page: 1 }),
    ]);

    stats.value = {
      totalInbox: inboxRes.data?.total || 0,
      unreadInbox: inboxRes.data?.total || 0,
      sentMessages: outboxRes.data?.total || 0,
      totalBatches: batchesRes.data?.total || 0,
    };
  } catch (error) {
    console.error('Failed to load dashboard stats:', error);
  }
});
</script>
