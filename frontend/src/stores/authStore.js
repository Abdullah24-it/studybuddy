import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  // --- State ---
  // Initialize from localStorage so state survives page refresh
  const token = ref(localStorage.getItem('token') || null)
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  // --- Getters ---
  const isLoggedIn = computed(() => !!token.value)
  const isAdmin    = computed(() => user.value?.role === 'admin')

  // --- Actions ---
  async function login(email, password) {
    const response = await api.post('/auth/login', { email, password })

    token.value = response.data.token
    user.value  = response.data.user

    // Persist to localStorage so state survives refresh
    localStorage.setItem('token', token.value)
    localStorage.setItem('user', JSON.stringify(user.value))

    router.push('/dashboard')
  }

  async function register(name, email, password) {
    await api.post('/auth/register', { name, email, password })
    // After registering, automatically log in
    await login(email, password)
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }

  return { token, user, isLoggedIn, isAdmin, login, register, logout }
})