import { defineStore } from 'pinia';
import api from '../services/api';

interface User {
  id: number;
  username: string;
  email?: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => {
    try {
      const userStr = localStorage.getItem('user');
      return {
        user: userStr ? JSON.parse(userStr) : null,
        token: localStorage.getItem('auth_token'),
        isAuthenticated: !!localStorage.getItem('auth_token'),
      };
    } catch (e) {
      return {
        user: null,
        token: null,
        isAuthenticated: false,
      };
    }
  },

  actions: {
    async login(username: string, password: string) {
      try {
        const response = await api.login(username, password);
        if (response.success) {
          this.user = response.user;
          this.token = response.token;
          this.isAuthenticated = true;
          return { success: true };
        }
        return { success: false, message: response.message || 'Login failed' };
      } catch (error: any) {
        return {
          success: false,
          message: error.response?.data?.message || 'Login failed',
        };
      }
    },

    async logout() {
      try {
        await api.logout();
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.token = null;
        this.isAuthenticated = false;
      }
    },

    async checkAuth() {
      try {
        const response = await api.getMe();
        if (response.success) {
          this.user = response.user;
          this.isAuthenticated = true;
          return true;
        }
      } catch (error) {
        this.logout();
        return false;
      }
    },
  },
});
