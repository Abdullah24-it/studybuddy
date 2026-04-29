<template>
  <div class="subject-card">
    <div class="subject-card__header">
      <h5 class="subject-card__name">{{ subject.name }}</h5>
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary"
          style="padding:0.2rem 0.5rem;font-size:1rem;line-height:1;"
          data-bs-toggle="dropdown">⋮</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><button class="dropdown-item" @click="$emit('edit', subject)">✏️ Edit</button></li>
          <li><button class="dropdown-item text-danger" @click="$emit('delete', subject.id)">🗑️ Delete</button></li>
        </ul>
      </div>
    </div>
    <p class="subject-card__desc">{{ subject.description || 'No description' }}</p>
    <div class="subject-card__footer">
      <div class="d-flex flex-column gap-1">
        <span class="subject-card__date">{{ formatDate(subject.created_at) }}</span>
        <span v-if="subject.owner_name" class="subject-card__owner">👤 {{ subject.owner_name }}</span>
      </div>
      <RouterLink :to="`/tasks?subject_id=${subject.id}`" class="btn btn-sm btn-outline-primary">
        View Tasks →
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
defineProps({ subject: { type: Object, required: true } })
defineEmits(['edit', 'delete'])
function formatDate(date) {
  return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.subject-card__owner {
  font-size: 0.8rem;
  color: var(--sb-text-muted);
}
</style>