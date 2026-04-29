<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">My Tasks</h1>
        <p class="page-subtitle">{{ store.tasks.length }} task{{ store.tasks.length !== 1 ? 's' : '' }}</p>
      </div>
      <button class="btn btn-primary" @click="openModal()">+ Add Task</button>
    </div>

    <div class="filter-bar">
      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <select v-model="filters.subject_id" class="form-select" @change="applyFilters">
            <option value="">All Subjects</option>
            <option v-for="s in store.subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <select v-model="filters.status" class="form-select" @change="applyFilters">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="col-md-3">
          <select v-model="filters.priority" class="form-select" @change="applyFilters">
            <option value="">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-secondary w-100" @click="clearFilters">Clear</button>
        </div>
      </div>
    </div>

    <div v-if="store.error" class="alert alert-danger mb-3">{{ store.error }}</div>

    <div v-if="store.isLoadingTasks" class="text-center" style="padding:5rem 0;">
      <div class="spinner-border"></div>
    </div>

    <div v-else-if="store.tasks.length === 0" class="empty-state" style="background:var(--sb-surface);border:1px solid var(--sb-border);border-radius:var(--radius-lg);">
      <div class="empty-state__icon">✅</div>
      <div class="empty-state__title">No tasks found</div>
      <div class="empty-state__text">Try adjusting filters or add a new task</div>
    </div>

    <div v-else class="card" style="overflow:hidden;">
      <TaskItem
        v-for="task in store.tasks"
        :key="task.id"
        :task="task"
        :show-actions="true"
        @toggle="store.toggleTaskStatus(task)"
        @edit="openModal(task)"
        @delete="handleDelete"
      />
    </div>

    <div v-if="store.tasksMeta.total_pages > 1" class="d-flex justify-content-center mt-4">
      <nav><ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">← Prev</button>
        </li>
        <li v-for="p in store.tasksMeta.total_pages" :key="p" class="page-item" :class="{ active: p === currentPage }">
          <button class="page-link" @click="changePage(p)">{{ p }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === store.tasksMeta.total_pages }">
          <button class="page-link" @click="changePage(currentPage + 1)">Next →</button>
        </li>
      </ul></nav>
    </div>

    <div v-if="showModal" class="modal d-block" style="background:rgba(0,0,0,0.6);" @click.self="closeModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" style="font-size:1rem;font-weight:600;">{{ editingTask ? 'Edit Task' : 'New Task' }}</h5>
            <button class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="modalError" class="alert alert-danger mb-3" style="font-size:0.875rem;">{{ modalError }}</div>
            <div class="mb-3">
              <label class="form-label">Title *</label>
              <input v-model="form.title" type="text" class="form-control" placeholder="Task title" />
            </div>
            <div class="mb-3">
              <label class="form-label">Subject *</label>
              <select v-model="form.subject_id" class="form-select">
                <option value="">Select a subject</option>
                <option v-for="s in store.subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea v-model="form.description" class="form-control" rows="2"></textarea>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Deadline</label>
                <input v-model="form.deadline" type="date" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Priority</label>
                <select v-model="form.priority" class="form-select">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>
            <div v-if="editingTask" class="mt-3">
              <label class="form-label">Status</label>
              <select v-model="form.status" class="form-select">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" @click="handleSave" :disabled="isSaving">
              <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ editingTask ? 'Save changes' : 'Create task' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTaskStore } from '@/stores/taskStore'
import TaskItem from '@/components/TaskItem.vue'

const store       = useTaskStore()
const route       = useRoute()
const showModal   = ref(false)
const isSaving    = ref(false)
const modalError  = ref(null)
const currentPage = ref(1)
const editingTask = ref(null)

const filters = ref({ subject_id: route.query.subject_id || '', status: '', priority: '' })
const form    = ref({ title: '', subject_id: '', description: '', deadline: '', priority: 'medium', status: 'pending' })

onMounted(async () => { await store.fetchSubjects(); await store.fetchTasks(activeFilters()) })

function activeFilters() {
  return Object.fromEntries(Object.entries(filters.value).filter(([_, v]) => v !== ''))
}
async function applyFilters() { currentPage.value = 1; await store.fetchTasks({ ...activeFilters(), page: 1 }) }
async function clearFilters() { filters.value = { subject_id: '', status: '', priority: '' }; await applyFilters() }

function openModal(task = null) {
  editingTask.value = task
  form.value = task
    ? { ...task, subject_id: task.subject_id }
    : { title: '', subject_id: '', description: '', deadline: '', priority: 'medium', status: 'pending' }
  modalError.value = null; showModal.value = true
}
function closeModal() { showModal.value = false; editingTask.value = null }

async function handleSave() {
  if (!form.value.title.trim() || !form.value.subject_id) { modalError.value = 'Title and subject are required'; return }
  isSaving.value = true; modalError.value = null
  try {
    if (editingTask.value) await store.updateTask(editingTask.value.id, form.value)
    else await store.createTask(form.value)
    closeModal()
  } catch (err) { modalError.value = err.response?.data?.error || 'Something went wrong' }
  finally { isSaving.value = false }
}
async function handleDelete(id) {
  if (!confirm('Delete this task?')) return
  await store.deleteTask(id)
}
async function changePage(page) {
  if (page < 1 || page > store.tasksMeta.total_pages) return
  currentPage.value = page; await store.fetchTasks({ ...activeFilters(), page })
}
</script>