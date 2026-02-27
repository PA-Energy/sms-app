<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 py-8">
    <div class="w-full max-w-md">
      <!-- Logo/Title Card -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-2xl mb-4">
          <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
          SMS App
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Sign in to your account
        </p>
      </div>

      <!-- Login Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100 dark:border-gray-700">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Username
            </label>
            <input
              id="username"
              v-model="username"
              name="username"
              type="text"
              required
              autocomplete="username"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              placeholder="Enter your username"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Password
            </label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              autocomplete="current-password"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              placeholder="Enter your password"
            />
          </div>

          <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900 rounded-lg border border-red-200 dark:border-red-800">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm text-red-800 dark:text-red-200">{{ error }}</span>
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-base active:scale-95 transition-all flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Signing in...' : 'Sign in' }}</span>
          </button>
        </form>

        <!-- Default Credentials Hint -->
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
          <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
            Default: <span class="font-mono font-medium">admin</span> / <span class="font-mono font-medium">admin123</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const username = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  const result = await authStore.login(username.value, password.value);

  if (result.success) {
    router.push('/dashboard');
  } else {
    error.value = result.message || 'Login failed. Please try again.';
  }

  loading.value = false;
};
</script>
