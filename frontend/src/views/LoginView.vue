<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">
        <span class="auth-logo-icon">📚</span>
        StudyBuddy
      </div>
      <p style="color:var(--sb-text-muted);font-size:0.875rem;margin-bottom:0;">
        Welcome back — sign in to continue
      </p>

      <div class="auth-divider"></div>

      <div v-if="error" class="alert alert-danger mb-4" style="font-size:0.875rem;">{{ error }}</div>

      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input v-model="form.email" type="email" class="form-control" placeholder="you@example.com" @keyup.enter="handleLogin" />
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <input v-model="form.password" type="password" class="form-control" placeholder="••••••••" @keyup.enter="handleLogin" />
      </div>

      <button class="btn btn-primary w-100" @click="handleLogin" :disabled="isLoading" style="padding:0.625rem;">
        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
        {{ isLoading ? 'Signing in...' : 'Sign in' }}
      </button>

      <p style="text-align:center;margin-top:1.25rem;margin-bottom:0;font-size:0.875rem;color:var(--sb-text-muted);">
        No account? <RouterLink to="/register">Create one</RouterLink>
      </p>

      <div class="credentials-hint">
        <div class="credentials-hint__title">🔑 Demo credentials</div>
        <div class="credentials-hint__row" @click="fillCredentials('student@studybuddy.com', 'password123')">
          <span class="credentials-hint__badge credentials-hint__badge--student">Student</span>
          <span class="credentials-hint__info">student@studybuddy.com · password123</span>
        </div>
        <div class="credentials-hint__row" @click="fillCredentials('admin@studybuddy.com', 'password123')">
          <span class="credentials-hint__badge credentials-hint__badge--admin">Admin</span>
          <span class="credentials-hint__info">admin@studybuddy.com · password123</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const auth      = useAuthStore()
const isLoading = ref(false)
const error     = ref(null)
const form      = ref({ email: '', password: '' })

async function handleLogin() {
  if (!form.value.email || !form.value.password) { error.value = 'Please fill in all fields'; return }
  isLoading.value = true; error.value = null
  try { await auth.login(form.value.email, form.value.password) }
  catch (err) { error.value = err.response?.data?.error || 'Login failed. Please try again.' }
  finally { isLoading.value = false }
}

function fillCredentials(email, password) {
  form.value.email    = email
  form.value.password = password
}
</script>

<style scoped>
.credentials-hint {
  margin-top: 1.5rem;
  border: 1px dashed var(--sb-border);
  border-radius: var(--radius-sm);
  padding: 0.875rem 1rem;
  background: var(--sb-surface-2);
}
.credentials-hint__title {
  font-size: 0.775rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--sb-text-muted);
  margin-bottom: 0.625rem;
}
.credentials-hint__row {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.4rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.15s ease;
}
.credentials-hint__row:hover { background: var(--sb-border); }
.credentials-hint__row + .credentials-hint__row { margin-top: 0.25rem; }
.credentials-hint__badge {
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.2em 0.5em;
  border-radius: 4px;
  flex-shrink: 0;
}
.credentials-hint__badge--student { background: var(--sb-accent-dim); color: var(--sb-accent); }
.credentials-hint__badge--admin   { background: rgba(139,92,246,0.15); color: #7c3aed; }
.credentials-hint__info {
  font-size: 0.8125rem;
  color: var(--sb-text-muted);
  font-family: monospace;
}
</style>