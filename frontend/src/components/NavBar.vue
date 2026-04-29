<template>
  <nav class="sb-nav">
    <div class="container sb-nav__inner">
      <RouterLink class="sb-nav__brand" to="/dashboard">
        <span class="sb-nav__brand-icon">📚</span>
        StudyBuddy
      </RouterLink>

      <div class="sb-nav__links">
        <RouterLink class="sb-nav__link" to="/dashboard" active-class="sb-nav__link--active">Dashboard</RouterLink>
        <RouterLink class="sb-nav__link" to="/subjects"  active-class="sb-nav__link--active">Subjects</RouterLink>
        <RouterLink class="sb-nav__link" to="/tasks"     active-class="sb-nav__link--active">Tasks</RouterLink>
      </div>

      <div class="sb-nav__right">
        <div class="sb-nav__user">
          <span class="sb-nav__avatar">{{ initials }}</span>
          <span class="sb-nav__name">{{ auth.user?.name }}</span>
          <span v-if="auth.isAdmin" class="badge" style="background:rgba(139,92,246,0.2);color:#a78bfa;font-size:0.65rem;">Admin</span>
        </div>
        <button class="sb-nav__logout" @click="auth.logout()">Sign out</button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()
const initials = computed(() => {
  const name = auth.user?.name || ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
})
</script>

<style scoped>
.sb-nav {
  background: var(--sb-surface);
  border-bottom: 1px solid var(--sb-border);
  position: sticky;
  top: 0;
  z-index: 100;
}
.sb-nav__inner {
  display: flex;
  align-items: center;
  gap: 2rem;
  height: 56px;
}
.sb-nav__brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 700;
  font-size: 1rem;
  color: var(--sb-text);
  text-decoration: none;
  letter-spacing: -0.02em;
  flex-shrink: 0;
}
.sb-nav__links {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  flex: 1;
}
.sb-nav__link {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--sb-text-muted);
  text-decoration: none;
  padding: 0.35rem 0.75rem;
  border-radius: var(--radius-sm);
  transition: all var(--transition);
}
.sb-nav__link:hover { color: var(--sb-text); background: var(--sb-surface-2); }
.sb-nav__link--active { color: var(--sb-text); background: var(--sb-surface-2); }
.sb-nav__right {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-shrink: 0;
}
.sb-nav__user {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.sb-nav__avatar {
  width: 28px; height: 28px;
  background: var(--sb-accent-dim);
  color: var(--sb-accent);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
}
.sb-nav__name { font-size: 0.875rem; font-weight: 500; color: var(--sb-text); }
.sb-nav__logout {
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--sb-text-muted);
  background: transparent;
  border: 1px solid var(--sb-border);
  border-radius: var(--radius-sm);
  padding: 0.3rem 0.75rem;
  cursor: pointer;
  transition: all var(--transition);
}
.sb-nav__logout:hover { color: var(--sb-text); border-color: var(--sb-text-dim); }
@media (max-width: 600px) {
  .sb-nav__name { display: none; }
}
</style>