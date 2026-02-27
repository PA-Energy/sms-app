<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-20 sm:pb-0">
    <!-- Mobile Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:hidden z-50">
      <div class="flex justify-around items-center h-16">
        <router-link
          to="/dashboard"
          class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 dark:text-gray-400"
          active-class="text-indigo-600 dark:text-indigo-400"
        >
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="text-xs">Home</span>
        </router-link>
        <router-link
          to="/inbox"
          class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 dark:text-gray-400 relative"
          active-class="text-indigo-600 dark:text-indigo-400"
        >
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span class="text-xs">Inbox</span>
        </router-link>
        <router-link
          to="/compose"
          class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 dark:text-gray-400"
          active-class="text-indigo-600 dark:text-indigo-400"
        >
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
          <span class="text-xs">Send</span>
        </router-link>
        <router-link
          to="/blast"
          class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 dark:text-gray-400"
          active-class="text-indigo-600 dark:text-indigo-400"
        >
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="text-xs">Blast</span>
        </router-link>
        <router-link
          to="/blast-history"
          class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 dark:text-gray-400"
          active-class="text-indigo-600 dark:text-indigo-400"
        >
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-xs">History</span>
        </router-link>
      </div>
    </nav>

    <!-- Desktop Top Navigation -->
    <nav class="hidden sm:block bg-white dark:bg-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-900 dark:text-white">SMS App</h1>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/dashboard"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                Dashboard
              </router-link>
              <router-link
                to="/inbox"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                Inbox
              </router-link>
              <router-link
                to="/compose"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                Compose
              </router-link>
              <router-link
                to="/blast"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                Blast
              </router-link>
              <router-link
                to="/blast-history"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                History
              </router-link>
              <router-link
                v-if="authStore.isAdmin"
                to="/users"
                class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900 dark:text-white"
              >
                Users
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <span class="text-sm text-gray-700 dark:text-gray-300 mr-4">
              {{ authStore.user?.username }}
            </span>
            <button
              @click="handleLogout"
              class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-sm font-medium"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Header -->
    <div class="sm:hidden bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
      <div class="flex justify-between items-center px-4 h-14">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">SMS App</h1>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ authStore.user?.username }}</span>
          <button
            @click="handleLogout"
            class="text-gray-500 dark:text-gray-400"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
