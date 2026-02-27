<template>
  <div id="app">
    <router-view v-if="mounted" />
    <div v-else class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="text-lg text-gray-600 dark:text-gray-400">Loading...</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();
const mounted = ref(false);

onMounted(async () => {
  try {
    if (authStore.isAuthenticated) {
      await authStore.checkAuth();
    }
  } catch (error) {
    console.error('Auth check error:', error);
  } finally {
    mounted.value = true;
  }
});
</script>
