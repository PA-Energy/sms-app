import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('../views/Login.vue'),
      meta: { requiresAuth: false },
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/inbox',
      name: 'Inbox',
      component: () => import('../views/Inbox.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/compose',
      name: 'Compose',
      component: () => import('../views/Compose.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/blast',
      name: 'Blast',
      component: () => import('../views/Blast.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/blast-history',
      name: 'BlastHistory',
      component: () => import('../views/BlastHistory.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/users',
      name: 'Users',
      component: () => import('../views/Users.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
});

router.beforeEach((to, from, next) => {
  try {
    const authStore = useAuthStore();

    // If going to root, redirect based on auth
    if (to.path === '/') {
      if (authStore.isAuthenticated) {
        next('/dashboard');
      } else {
        next('/login');
      }
      return;
    }

    // Check auth requirements
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      next('/login');
    } else if (to.path === '/login' && authStore.isAuthenticated) {
      next('/dashboard');
    } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
      // Redirect non-admin users trying to access admin routes
      next('/dashboard');
    } else {
      next();
    }
  } catch (error) {
    console.error('Router error:', error);
    next('/login');
  }
});

export default router;
