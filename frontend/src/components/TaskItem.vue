<template>
  <div class="task-row">
    <input
      type="checkbox"
      class="form-check-input mt-0 flex-shrink-0"
      :checked="task.status === 'completed'"
      @change="$emit('toggle')"
    />
    <div class="flex-grow-1">
      <div class="task-row__title" :class="{ 'task-row__title--done': task.status === 'completed' }">
        {{ task.title }}
      </div>
      <div class="task-row__meta">
        <span class="task-row__meta-item"><span>📖</span> {{ task.subject_name ?? 'No subject' }}</span>
        <span v-if="task.deadline" class="task-row__meta-item" :class="{ 'text-overdue': isOverdue }">
          <span>📅</span> {{ formatDate(task.deadline) }}
          <span v-if="isOverdue" style="font-size:0.75rem;">(Overdue)</span>
        </span>
      </div>
    </div>

    <span class="badge flex-shrink-0" :class="priorityClass">{{ task.priority }}</span>

    <div v-if="showActions" class="task-row__actions">
      <button class="btn btn-sm btn-outline-secondary" @click="$emit('edit', task)">✏️</button>
      <button class="btn btn-sm btn-outline-danger"    @click="$emit('delete', task.id)">🗑️</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  task:        { type: Object,  required: true },
  showActions: { type: Boolean, default: false }
})
defineEmits(['toggle', 'edit', 'delete'])

const isOverdue = computed(() =>
  props.task.status === 'pending' &&
  props.task.deadline &&
  new Date(props.task.deadline) < new Date()
)
const priorityClass = computed(() => ({
  'badge-high':   props.task.priority === 'high',
  'badge-medium': props.task.priority === 'medium',
  'badge-low':    props.task.priority === 'low',
}))
function formatDate(date) {
  return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>