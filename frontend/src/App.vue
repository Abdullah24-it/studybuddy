<template>
  <div>
    <NavBar v-if="auth.isLoggedIn" />
    <main class="container py-4">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
</script>