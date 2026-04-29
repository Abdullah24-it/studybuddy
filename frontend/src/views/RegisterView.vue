<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">
        <span class="auth-logo-icon">📚</span>
        StudyBuddy
      </div>
      <p style="color:var(--sb-text-muted);font-size:0.875rem;margin-bottom:0;">
        Create your account to get started
      </p>

      <div class="auth-divider"></div>

      <div v-if="error" class="alert alert-danger mb-4" style="font-size:0.875rem;">{{ error }}</div>

      <div class="mb-3">
        <label class="form-label">Full name</label>
        <input v-model="form.name" type="text" class="form-control" placeholder="John Doe" />
      </div>
      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input v-model="form.email" type="email" class="form-control" placeholder="you@example.com" />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input v-model="form.password" type="password" class="form-control" placeholder="Min. 6 characters" />
      </div>
      <div class="mb-4">
        <label class="form-label">Confirm password</label>
        <input v-model="form.confirmPassword" type="password" class="form-control" placeholder="••••••••" />
      </div>

      <button class="btn btn-primary w-100" @click="handleRegister" :disabled="isLoading" style="padding:0.625rem;">
        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
        {{ isLoading ? 'Creating account...' : 'Create account' }}
      </button>

      <p style="text-align:center;margin-top:1.25rem;margin-bottom:0;font-size:0.875rem;color:var(--sb-text-muted);">
        Already have an account? <RouterLink to="/login">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()
const isLoading = ref(false)
const error     = ref(null)
const form      = ref({ name: '', email: '', password: '', confirmPassword: '' })

async function handleRegister() {
  error.value = null
  if (!form.value.name || !form.value.email || !form.value.password) { error.value = 'All fields are required'; return }
  if (form.value.password.length < 6) { error.value = 'Password must be at least 6 characters'; return }
  if (form.value.password !== form.value.confirmPassword) { error.value = 'Passwords do not match'; return }
  isLoading.value = true
  try { await auth.register(form.value.name, form.value.email, form.value.password) }
  catch (err) { error.value = err.response?.data?.error || 'Registration failed' }
  finally { isLoading.value = false }
}
</script>