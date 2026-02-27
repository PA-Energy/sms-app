<template>
  <Layout>
    <div class="space-y-4 max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Compose SMS</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Send a single message</p>
      </div>

      <!-- Form Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 sm:p-6 border border-gray-100 dark:border-gray-700 space-y-5">
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Phone Number
          </label>
          <input
            id="phone"
            v-model="form.phoneNumber"
            type="tel"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="+1234567890"
          />
        </div>

        <div>
          <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Message
          </label>
          <textarea
            id="message"
            v-model="form.message"
            rows="6"
            required
            maxlength="160"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
            placeholder="Enter your message here..."
          ></textarea>
          <div class="mt-2 flex justify-between items-center">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ form.message.length }}/160 characters
            </div>
            <div v-if="form.message.length > 140" class="text-sm text-amber-600 dark:text-amber-400">
              {{ 160 - form.message.length }} remaining
            </div>
          </div>
        </div>

        <div>
          <label for="line" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            GoIP Line <span class="text-gray-400">(Optional)</span>
          </label>
          <input
            id="line"
            v-model.number="form.line"
            type="number"
            min="1"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="1"
          />
        </div>

        <!-- Error Alert -->
        <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900 rounded-lg border border-red-200 dark:border-red-800">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-red-800 dark:text-red-200">{{ error }}</span>
          </div>
        </div>

        <!-- Success Alert -->
        <div v-if="success" class="p-4 bg-green-50 dark:bg-green-900 rounded-lg border border-green-200 dark:border-green-800">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-green-800 dark:text-green-200">SMS sent successfully!</span>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          @click="handleSend"
          :disabled="loading || !form.phoneNumber || !form.message"
          class="w-full py-3.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-base active:scale-95 transition-all flex items-center justify-center gap-2"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Sending...' : 'Send SMS' }}</span>
        </button>
      </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const form = ref({
  phoneNumber: '',
  message: '',
  line: null as number | null,
});

const loading = ref(false);
const error = ref('');
const success = ref(false);

const handleSend = async () => {
  loading.value = true;
  error.value = '';
  success.value = false;

  try {
    const response = await api.sendSms(
      form.value.phoneNumber,
      form.value.message,
      form.value.line || undefined
    );

    if (response.success) {
      success.value = true;
      form.value = {
        phoneNumber: '',
        message: '',
        line: null,
      };
      setTimeout(() => {
        success.value = false;
      }, 3000);
    } else {
      error.value = response.message || 'Failed to send SMS';
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to send SMS';
  } finally {
    loading.value = false;
  }
};
</script>
