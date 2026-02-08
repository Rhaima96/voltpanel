<template>
  <div class="space-y-6">
    <!-- Comments Header -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-card-foreground flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        Comments
        <span class="px-2 py-0.5 text-xs font-medium bg-muted rounded-full text-muted-foreground">
          {{ comments.length }}
        </span>
      </h3>
    </div>

    <!-- Add Comment Form -->
    <div class="bg-muted/20 rounded-lg p-4 border border-border">
      <textarea
        v-model="newComment"
        placeholder="Add a comment... (Use @username to mention someone)"
        rows="3"
        class="w-full px-3 py-2 bg-background border border-input rounded-lg text-foreground placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 resize-none"
        @keydown.meta.enter="addComment"
        @keydown.ctrl.enter="addComment"
      ></textarea>
      <div class="flex items-center justify-between mt-3">
        <p class="text-xs text-muted-foreground">
          💡 Tip: Use @username to mention someone
        </p>
        <button
          type="button"
          @click="addComment"
          :disabled="!newComment.trim() || submitting"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm font-medium"
        >
          {{ submitting ? 'Posting...' : 'Post Comment' }}
        </button>
      </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
      <TransitionGroup
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-for="comment in comments"
          :key="comment.id"
          class="bg-card border border-border rounded-lg p-4 hover:shadow-md transition-all"
        >
          <div class="flex items-start gap-3">
            <!-- Avatar -->
            <img
              :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random`"
              :alt="comment.user.name"
              class="w-10 h-10 rounded-full ring-2 ring-border flex-shrink-0"
            />

            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="font-semibold text-card-foreground">
                  {{ comment.user.name }}
                </span>
                <span class="text-xs text-muted-foreground">
                  {{ formatDate(comment.created_at) }}
                </span>
              </div>

              <p class="text-sm text-card-foreground whitespace-pre-wrap break-words" v-html="highlightMentions(comment.content)"></p>

              <!-- Actions -->
              <div class="flex items-center gap-3 mt-2">
                <button
                  type="button"
                  v-if="canDelete(comment)"
                  @click="confirmDelete(comment)"
                  class="text-xs text-destructive hover:underline flex items-center gap-1"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <!-- Empty State -->
      <div
        v-if="comments.length === 0"
        class="text-center py-12 bg-muted/20 rounded-lg border border-dashed border-border"
      >
        <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="text-sm text-muted-foreground font-medium">No comments yet</p>
        <p class="text-xs text-muted-foreground mt-1">Be the first to share your thoughts!</p>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="deleteConfirmation.show"
      :title="deleteConfirmation.title"
      :message="deleteConfirmation.message"
      confirm-text="Delete"
      cancel-text="Cancel"
      type="danger"
      @confirm="performDelete"
      @close="closeDeleteConfirmation"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'
import ConfirmationModal from './ConfirmationModal.vue'
import { useToast } from '../../composables/VoltPanel/useToast'
import { useRouting } from '../../composables/VoltPanel/useRouting'

const { route } = useRouting()
const toast = useToast()
const page = usePage()

const props = defineProps({
  commentableType: {
    type: String,
    required: true
  },
  commentableId: {
    type: Number,
    required: true
  },
  initialComments: {
    type: Array,
    default: () => []
  }
})

const comments = ref([...props.initialComments].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)))
const newComment = ref('')
const submitting = ref(false)
const deleteConfirmation = ref({
  show: false,
  commentId: null,
  title: 'Delete Comment',
  message: ''
})

const addComment = async () => {
  if (!newComment.value.trim()) return

  submitting.value = true
  try {
    const response = await axios.post(route('voltpanel.api.comments.store'), {
      commentable_type: props.commentableType,
      commentable_id: props.commentableId,
      content: newComment.value
    })

    comments.value.unshift(response.data)
    newComment.value = ''
    toast.success('Comment added successfully')
  } catch (error) {
    console.error('Failed to add comment:', error)
    toast.error('Failed to add comment: ' + (error.response?.data?.message || error.message))
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (comment) => {
  deleteConfirmation.value = {
    show: true,
    commentId: comment.id,
    title: 'Delete Comment',
    message: 'Are you sure you want to delete this comment? This action cannot be undone.'
  }
}

const performDelete = async () => {
  try {
    const commentIdToDelete = deleteConfirmation.value.commentId
    await axios.delete(route('voltpanel.api.comments.destroy', { id: commentIdToDelete }))

    // Remove from local array using non-strict equality to handle type mismatches
    comments.value = comments.value.filter(c => c.id != commentIdToDelete)

    closeDeleteConfirmation()
    toast.success('Comment deleted successfully')
  } catch (error) {
    console.error('Failed to delete comment:', error)
    toast.error('Failed to delete comment: ' + (error.response?.data?.message || error.message))
  }
}

const closeDeleteConfirmation = () => {
  deleteConfirmation.value = {
    show: false,
    commentId: null,
    title: 'Delete Comment',
    message: ''
  }
}

const canDelete = (comment) => {
  return comment.user_id === page.props.auth?.user?.id
}

const formatDate = (date) => {
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (days > 7) {
    return d.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: d.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    })
  } else if (days > 0) {
    return `${days} day${days > 1 ? 's' : ''} ago`
  } else if (hours > 0) {
    return `${hours} hour${hours > 1 ? 's' : ''} ago`
  } else if (minutes > 0) {
    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
  } else {
    return 'Just now'
  }
}

const highlightMentions = (content) => {
  // Escape HTML to prevent XSS
  const escaped = content
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')

  // Highlight mentions
  return escaped.replace(/@(\w+)/g, '<span class="text-primary font-semibold">@$1</span>')
}
</script>
