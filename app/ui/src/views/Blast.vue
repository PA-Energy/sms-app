<template>
  <Layout>
    <div class="space-y-4 max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">SMS Blast</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Send messages to multiple recipients</p>
      </div>

      <!-- Form Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 sm:p-6 border border-gray-100 dark:border-gray-700 space-y-5">
        <!-- Input Method Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
          <div class="flex gap-2">
            <button
              @click="inputMethod = 'manual'"
              :class="inputMethod === 'manual' ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'"
              class="px-4 py-2 font-medium text-sm transition-colors"
            >
              Manual Entry
            </button>
            <button
              @click="inputMethod = 'csv'"
              :class="inputMethod === 'csv' ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'"
              class="px-4 py-2 font-medium text-sm transition-colors"
            >
              CSV Upload
            </button>
          </div>
        </div>

        <!-- Batch Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Batch Name
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="My SMS Blast"
          />
        </div>

        <!-- Message -->
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
          <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ form.message.length }}/160 characters
          </div>
        </div>

        <!-- Manual Entry -->
        <div v-if="inputMethod === 'manual'">
          <label for="phoneNumbers" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Phone Numbers <span class="text-gray-400">(one per line)</span>
          </label>
          <textarea
            id="phoneNumbers"
            v-model="form.phoneNumbers"
            rows="8"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm resize-none"
            placeholder="+1234567890&#10;+0987654321&#10;+1122334455"
          ></textarea>
          <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ phoneNumbersArray.length }} phone number(s)
          </div>
        </div>

        <!-- CSV Upload -->
        <div v-if="inputMethod === 'csv'">
          <label for="csvFile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            CSV File <span class="text-gray-400">(first column should contain phone numbers)</span>
          </label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600 dark:text-gray-400">
                <label for="csvFile" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                  <span>Upload a file</span>
                  <input id="csvFile" type="file" accept=".csv,.txt" @change="handleFileSelect" class="sr-only" />
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">CSV, TXT up to 2MB</p>
              <p v-if="csvFile" class="text-sm text-indigo-600 dark:text-indigo-400 mt-2">{{ csvFile.name }}</p>
            </div>
          </div>
        </div>

        <!-- GoIP Line -->
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
            <span class="text-sm text-green-800 dark:text-green-200">SMS blast created successfully! Redirecting...</span>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          @click="handleSubmit"
          :disabled="loading || (inputMethod === 'manual' && phoneNumbersArray.length === 0) || (inputMethod === 'csv' && !csvFile)"
          class="w-full py-3.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-base active:scale-95 transition-all flex items-center justify-center gap-2"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Creating...' : 'Create Blast' }}</span>
        </button>
      </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const router = useRouter();
const inputMethod = ref<'manual' | 'csv'>('manual');
const csvFile = ref<File | null>(null);

const form = ref({
  name: '',
  message: '',
  phoneNumbers: '',
  line: null as number | null,
});

const loading = ref(false);
const error = ref('');
const success = ref(false);

const phoneNumbersArray = computed(() => {
  if (inputMethod.value === 'manual') {
    return form.value.phoneNumbers
      .split('\n')
      .map(p => p.trim())
      .filter(p => p.length > 0);
  }
  return [];
});

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    csvFile.value = target.files[0];
  }
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';
  success.value = false;

  try {
    let response;

    if (inputMethod.value === 'csv' && csvFile.value) {
      response = await api.uploadCsvBlast(
        form.value.name,
        form.value.message,
        csvFile.value,
        form.value.line || undefined
      );
    } else {
      response = await api.createBlast(
        form.value.name,
        form.value.message,
        phoneNumbersArray.value,
        form.value.line || undefined
      );
    }

    if (response.success) {
      success.value = true;
      setTimeout(() => {
        router.push('/blast-history');
      }, 1500);
    } else {
      error.value = response.message || 'Failed to create SMS blast';
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to create SMS blast';
  } finally {
    loading.value = false;
  }
};
</script>
