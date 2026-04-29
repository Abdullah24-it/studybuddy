<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Welcome back, {{ auth.user?.name }}</h1>
        <p class="page-subtitle">Here's your study overview</p>
      </div>
    </div>

    <div v-if="loadError" class="alert alert-warning mb-4" style="font-size:0.875rem;">
      Could not load data. Please check your connection and refresh.
    </div>

    <div v-if="isLoading" class="text-center" style="padding:5rem 0;">
      <div class="spinner-border"></div>
    </div>

    <template v-else>
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card--blue">
            <div class="stat-card__label">Total Subjects</div>
            <div class="stat-card__value">{{ store.subjects.length }}</div>
            <span class="stat-card__icon">📖</span>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card--amber">
            <div class="stat-card__label">Pending Tasks</div>
            <div class="stat-card__value">{{ pendingCount }}</div>
            <span class="stat-card__icon">⏳</span>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card--green">
            <div class="stat-card__label">Completed Tasks</div>
            <div class="stat-card__value">{{ completedCount }}</div>
            <span class="stat-card__icon">✅</span>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card--red">
            <div class="stat-card__label">Overdue Tasks</div>
            <div class="stat-card__value">{{ overdueCount }}</div>
            <span class="stat-card__icon">🚨</span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
          <span>⏰</span>
          <h6 class="mb-0" style="font-weight:600;">Upcoming Deadlines</h6>
        </div>
        <div v-if="upcomingTasks.length === 0" class="empty-state">
          <div class="empty-state__icon">🎉</div>
          <div class="empty-state__title">All clear!</div>
          <div class="empty-state__text">No upcoming deadlines right now</div>
        </div>
        <div v-else>
          <TaskItem
            v-for="task in upcomingTasks"
            :key="task.id"
            :task="task"
            @toggle="store.toggleTaskStatus(task)"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useTaskStore } from '@/stores/taskStore'
import TaskItem from '@/components/TaskItem.vue'

const auth      = useAuthStore()
const store     = useTaskStore()
const isLoading = ref(true)
const loadError = ref(false)

const pendingCount   = computed(() => store.tasks.filter(t => t.status === 'pending').length)
const completedCount = computed(() => store.tasks.filter(t => t.status === 'completed').length)
const overdueCount   = computed(() => store.tasks.filter(t =>
  t.status === 'pending' && t.deadline && new Date(t.deadline) < new Date()
).length)
const upcomingTasks  = computed(() =>
  store.tasks
    .filter(t => t.status === 'pending' && t.deadline)
    .sort((a, b) => new Date(a.deadline) - new Date(b.deadline))
    .slice(0, 5)
)

onMounted(async () => {
  try { await Promise.allSettled([store.fetchSubjects(), store.fetchTasks()]) }
  catch { loadError.value = true }
  finally { isLoading.value = false }
})
</script>