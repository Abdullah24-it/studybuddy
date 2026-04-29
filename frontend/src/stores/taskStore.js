import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useTaskStore = defineStore('tasks', () => {
  // --- State ---
  const subjects        = ref([])
  const tasks           = ref([])
  const isLoadingSubjects = ref(false)
  const isLoadingTasks    = ref(false)
  const isLoading         = ref(false) // kept for dashboard compatibility
  const error           = ref(null)
  const subjectsMeta    = ref({})
  const tasksMeta       = ref({})

  // --- Subject Actions ---
  async function fetchSubjects(page = 1) {
    isLoadingSubjects.value = true
    isLoading.value = true
    error.value = null
    try {
      const res = await api.get('/subjects', { params: { page, limit: 10 } })
      subjects.value     = res.data.data ?? []
      subjectsMeta.value = res.data.meta ?? {}
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to load subjects'
      subjects.value = []
    } finally {
      isLoadingSubjects.value = false
      isLoading.value = false
    }
  }

  async function createSubject(payload) {
    const res = await api.post('/subjects', payload)
    subjects.value.unshift(res.data)
    return res.data
  }

  async function updateSubject(id, payload) {
    const res = await api.put(`/subjects/${id}`, payload)
    const index = subjects.value.findIndex(s => s.id === id)
    if (index !== -1) subjects.value[index] = res.data
    return res.data
  }

  async function deleteSubject(id) {
    await api.delete(`/subjects/${id}`)
    subjects.value = subjects.value.filter(s => s.id !== id)
  }

  // --- Task Actions ---
  async function fetchTasks(filters = {}) {
    isLoadingTasks.value = true
    error.value = null
    try {
      const res = await api.get('/tasks', { params: { limit: 10, ...filters } })
      tasks.value     = res.data.data ?? []
      tasksMeta.value = res.data.meta ?? {}
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to load tasks'
      tasks.value = []
    } finally {
      isLoadingTasks.value = false
    }
  }

  async function createTask(payload) {
    const res = await api.post('/tasks', payload)
    tasks.value.unshift(res.data)
    return res.data
  }

  async function updateTask(id, payload) {
    const res = await api.put(`/tasks/${id}`, payload)
    const index = tasks.value.findIndex(t => t.id === id)
    if (index !== -1) tasks.value[index] = res.data
    return res.data
  }

  async function deleteTask(id) {
    await api.delete(`/tasks/${id}`)
    tasks.value = tasks.value.filter(t => t.id !== id)
  }

  async function toggleTaskStatus(task) {
    const newStatus = task.status === 'pending' ? 'completed' : 'pending'
    return await updateTask(task.id, { ...task, status: newStatus })
  }

  return {
    subjects, tasks, isLoading, isLoadingSubjects, isLoadingTasks,
    error, subjectsMeta, tasksMeta,
    fetchSubjects, createSubject, updateSubject, deleteSubject,
    fetchTasks, createTask, updateTask, deleteTask, toggleTaskStatus
  }
})