<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">My Subjects</h1>
        <p class="page-subtitle">{{ store.subjects.length }} subject{{ store.subjects.length !== 1 ? 's' : '' }}</p>
      </div>
      <button class="btn btn-primary" @click="openModal()">+ Add Subject</button>
    </div>

    <div v-if="store.error" class="alert alert-danger mb-4">{{ store.error }}</div>

    <div v-if="store.isLoadingSubjects" class="text-center" style="padding:5rem 0;">
      <div class="spinner-border"></div>
    </div>

    <div v-else-if="store.subjects.length === 0" class="empty-state">
      <div class="empty-state__icon">📖</div>
      <div class="empty-state__title">No subjects yet</div>
      <div class="empty-state__text">Click "Add Subject" to get started</div>
    </div>

    <div v-else class="row g-3">
      <div v-for="subject in store.subjects" :key="subject.id" class="col-md-6 col-lg-4">
        <SubjectCard :subject="subject" @edit="openModal(subject)" @delete="handleDelete(subject.id)" />
      </div>
    </div>

    <div v-if="store.subjectsMeta.total_pages > 1" class="d-flex justify-content-center mt-4">
      <nav><ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">← Prev</button>
        </li>
        <li v-for="p in store.subjectsMeta.total_pages" :key="p" class="page-item" :class="{ active: p === currentPage }">
          <button class="page-link" @click="changePage(p)">{{ p }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === store.subjectsMeta.total_pages }">
          <button class="page-link" @click="changePage(currentPage + 1)">Next →</button>
        </li>
      </ul></nav>
    </div>

    <div v-if="showModal" class="modal d-block" style="background:rgba(0,0,0,0.6);" @click.self="closeModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" style="font-size:1rem;font-weight:600;">{{ editingSubject ? 'Edit Subject' : 'New Subject' }}</h5>
            <button class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="modalError" class="alert alert-danger mb-3" style="font-size:0.875rem;">{{ modalError }}</div>
            <div class="mb-3">
              <label class="form-label">Subject name *</label>
              <input v-model="form.name" type="text" class="form-control" placeholder="e.g. Mathematics" />
            </div>
            <div>
              <label class="form-label">Description</label>
              <textarea v-model="form.description" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" @click="handleSave" :disabled="isSaving">
              <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ editingSubject ? 'Save changes' : 'Create subject' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import SubjectCard from '@/components/SubjectCard.vue'

const store          = useTaskStore()
const showModal      = ref(false)
const isSaving       = ref(false)
const modalError     = ref(null)
const currentPage    = ref(1)
const editingSubject = ref(null)
const form           = ref({ name: '', description: '' })

onMounted(() => store.fetchSubjects())

function openModal(subject = null) {
  editingSubject.value = subject
  form.value = subject ? { name: subject.name, description: subject.description || '' } : { name: '', description: '' }
  modalError.value = null; showModal.value = true
}
function closeModal() { showModal.value = false; editingSubject.value = null }

async function handleSave() {
  if (!form.value.name.trim()) { modalError.value = 'Subject name is required'; return }
  isSaving.value = true; modalError.value = null
  try {
    if (editingSubject.value) await store.updateSubject(editingSubject.value.id, form.value)
    else await store.createSubject(form.value)
    closeModal()
  } catch (err) {
    modalError.value = err.response?.data?.error || 'Something went wrong'
  } finally { isSaving.value = false }
}
async function handleDelete(id) {
  if (!confirm('Delete this subject? All its tasks will also be deleted.')) return
  await store.deleteSubject(id)
}
async function changePage(page) {
  if (page < 1 || page > store.subjectsMeta.total_pages) return
  currentPage.value = page; await store.fetchSubjects(page)
}
</script>