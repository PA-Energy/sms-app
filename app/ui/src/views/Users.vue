<template>
  <Layout>
    <div class="space-y-4">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ pagination?.total ?? 0 }} users</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium active:scale-95 transition-all"
        >
          + Add User
        </button>
      </div>

      <!-- Search Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search users..."
          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          @input="loadUsers"
        />
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-500 dark:text-gray-400">Loading users...</p>
        </div>
      </div>

      <!-- Users List -->
      <div v-else class="space-y-3">
        <div
          v-for="user in users"
          :key="user.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="font-semibold text-gray-900 dark:text-white">{{ user.username }}</span>
                <span
                  class="px-2 py-0.5 text-xs font-medium rounded-full"
                  :class="user.role === 'admin' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                >
                  {{ user.role === 'admin' ? 'Admin' : 'User' }}
                </span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</p>
              <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Created: {{ formatDate(user.created_at) }}
              </p>
            </div>
            <div class="flex gap-2">
              <button
                @click="editUser(user)"
                class="px-3 py-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all"
              >
                Edit
              </button>
              <button
                @click="confirmDelete(user)"
                v-if="user.role !== 'admin'"
                class="px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
              >
                Delete
              </button>
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

      <!-- Create/Edit Modal -->
      <div
        v-if="showCreateModal || editingUser"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="closeModal"
      >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ editingUser ? 'Edit User' : 'Create User' }}
          </h2>
          <form @submit.prevent="saveUser" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
              <input
                v-model="userForm.username"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
              <input
                v-model="userForm.email"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
              <input
                v-model="userForm.password"
                type="password"
                :required="!editingUser"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
              />
              <p v-if="editingUser" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current password</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
              <select
                v-model="userForm.role"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
              >
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="flex gap-2 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : (editingUser ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div
        v-if="userToDelete"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="userToDelete = null"
      >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Delete User</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Are you sure you want to delete user <strong>{{ userToDelete.username }}</strong>? This action cannot be undone.
          </p>
          <div class="flex gap-2">
            <button
              @click="userToDelete = null"
              class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Cancel
            </button>
            <button
              @click="deleteUser"
              :disabled="deleting"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Layout from '../components/Layout.vue';
import api from '../services/api';

const users = ref([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchQuery = ref('');
const pagination = ref(null);
const showCreateModal = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);
const userForm = ref({
  username: '',
  email: '',
  password: '',
  role: 'user',
});

const loadUsers = async (page = 1) => {
  loading.value = true;
  try {
    const response = await api.getUsers({
      page,
      per_page: 20,
      search: searchQuery.value || undefined,
    });
    if (response && typeof response === 'object' && response.data !== undefined && Array.isArray(response.data)) {
      users.value = response.data;
      pagination.value = response.pagination || null;
    }
  } catch (error) {
    console.error('Failed to load users:', error);
    users.value = [];
    pagination.value = null;
  } finally {
    loading.value = false;
  }
};

const saveUser = async () => {
  saving.value = true;
  try {
    if (editingUser.value) {
      const updateData: any = {
        username: userForm.value.username,
        email: userForm.value.email,
        role: userForm.value.role,
      };
      if (userForm.value.password) {
        updateData.password = userForm.value.password;
      }
      await api.updateUser(editingUser.value.id, updateData);
    } else {
      await api.createUser({
        username: userForm.value.username,
        email: userForm.value.email,
        password: userForm.value.password,
        role: userForm.value.role,
      });
    }
    closeModal();
    await loadUsers();
  } catch (error: any) {
    alert(error.response?.data?.message || 'Failed to save user');
  } finally {
    saving.value = false;
  }
};

const editUser = (user: any) => {
  editingUser.value = user;
  userForm.value = {
    username: user.username,
    email: user.email,
    password: '',
    role: user.role || 'user',
  };
};

const confirmDelete = (user: any) => {
  userToDelete.value = user;
};

const deleteUser = async () => {
  if (!userToDelete.value) return;
  deleting.value = true;
  try {
    await api.deleteUser(userToDelete.value.id);
    userToDelete.value = null;
    await loadUsers();
  } catch (error: any) {
    alert(error.response?.data?.message || 'Failed to delete user');
  } finally {
    deleting.value = false;
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  editingUser.value = null;
  userForm.value = {
    username: '',
    email: '',
    password: '',
    role: 'user',
  };
};

const loadPage = (page: number) => {
  loadUsers(page);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleString();
};

onMounted(() => {
  loadUsers();
});
</script>
