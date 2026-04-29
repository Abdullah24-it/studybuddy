import { createRouter, createWebHistory } from 'vue-router'

import LoginView     from '@/views/LoginView.vue'
import RegisterView  from '@/views/RegisterView.vue'
import DashboardView from '@/views/DashboardView.vue'
import SubjectsView  from '@/views/SubjectsView.vue'
import TasksView     from '@/views/TasksView.vue'

const routes = [
  { path: '/',          redirect: '/dashboard' },
  { path: '/login',     component: LoginView,     meta: { guestOnly: true } },
  { path: '/register',  component: RegisterView,  meta: { guestOnly: true } },
  { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/subjects',  component: SubjectsView,  meta: { requiresAuth: true } },
  { path: '/tasks',     component: TasksView,     meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard — import store inside callback to avoid circular dependency
// (authStore imports router, so importing authStore at the top would cause a loop)
router.beforeEach(async (to) => {
  // Lazy import: must be called here, not at the top of the file
  const { useAuthStore } = await import('@/stores/authStore')
  const auth = useAuthStore()

  // Route requires login but user isn't logged in → redirect to login
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return '/login'
  }

  // Route is for guests only (login/register) but user IS logged in → redirect to dashboard
  if (to.meta.guestOnly && auth.isLoggedIn) {
    return '/dashboard'
  }
})

export default router