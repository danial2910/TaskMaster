<template>
  <div class="task-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Loading task details...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <i class="icon-alert"></i>
      <h2>Task Not Found</h2>
      <p>{{ error }}</p>
      <router-link to="/tasks" class="btn-primary">
        Back to Tasks
      </router-link>
    </div>

    <!-- Task Detail Content -->
    <div v-else-if="task" class="task-detail-container">
      <!-- Header -->
      <div class="task-header">
        <div class="header-navigation">
          <button @click="goBack" class="back-btn">
            <i class="icon-arrow-left"></i>
            Back
          </button>
          <div class="breadcrumb">
            <router-link to="/tasks">Tasks</router-link>
            <span class="separator">/</span>
            <span>{{ task.title }}</span>
          </div>
        </div>
        
        <div class="header-actions">
          <button @click="toggleEdit" class="btn-secondary">
            <i :class="editing ? 'icon-x' : 'icon-edit'"></i>
            {{ editing ? 'Cancel' : 'Edit' }}
          </button>
          <button @click="deleteTask" class="btn-danger">
            <i class="icon-trash"></i>
            Delete
          </button>
        </div>
      </div>

      <!-- Task Content -->
      <div class="task-content">
        <!-- Status and Priority Bar -->
        <div class="status-bar">
          <div class="status-section">
            <label>Status:</label>
            <select 
              v-if="editing" 
              v-model="editForm.status"
              class="status-select"
            >
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <span v-else :class="['status-badge', task.status]">
              {{ getStatusLabel(task.status) }}
            </span>
          </div>
          
          <div class="priority-section">
            <label>Priority:</label>
            <select 
              v-if="editing" 
              v-model="editForm.priority"
              class="priority-select"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
            <span v-else :class="['priority-badge', task.priority]">
              {{ task.priority }} priority
            </span>
          </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
          <!-- Title -->
          <div class="title-section">
            <h1 v-if="!editing" class="task-title">{{ task.title }}</h1>
            <input 
              v-else 
              v-model="editForm.title"
              class="title-input"
              placeholder="Task title"
              required
            />
          </div>

          <!-- Description -->
          <div class="description-section">
            <h3>Description</h3>
            <div v-if="!editing" class="description-content">
              <p v-if="task.description" class="description-text">
                {{ task.description }}
              </p>
              <p v-else class="no-description">
                No description provided.
              </p>
            </div>
            <textarea 
              v-else
              v-model="editForm.description"
              class="description-input"
              placeholder="Task description"
              rows="6"
            ></textarea>
          </div>

          <!-- Due Date -->
          <div class="due-date-section">
            <h3>Due Date</h3>
            <div v-if="!editing" class="due-date-content">
              <span v-if="task.due_date" :class="['due-date', { 'overdue': isOverdue(task.due_date) }]">
                <i class="icon-calendar"></i>
                {{ formatDateTime(task.due_date) }}
                <span v-if="isOverdue(task.due_date)" class="overdue-label">(Overdue)</span>
              </span>
              <span v-else class="no-due-date">
                No due date set
              </span>
            </div>
            <input 
              v-else
              type="datetime-local"
              v-model="editForm.due_date"
              class="due-date-input"
            />
          </div>

          <!-- Timestamps -->
          <div class="timestamps-section">
            <div class="timestamp-item">
              <label>Created:</label>
              <span>{{ formatDateTime(task.created_at) }}</span>
            </div>
            <div class="timestamp-item">
              <label>Last Updated:</label>
              <span>{{ formatDateTime(task.updated_at) }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div v-if="editing" class="edit-actions">
            <button @click="saveChanges" class="btn-primary" :disabled="saving">
              <i class="icon-save"></i>
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
            <button @click="cancelEdit" class="btn-secondary">
              Cancel
            </button>
          </div>

          <!-- Quick Actions -->
          <div v-else class="quick-actions">
            <button 
              @click="toggleStatus"
              :class="['action-btn', task.status === 'completed' ? 'mark-pending' : 'mark-complete']"
            >
              <i :class="task.status === 'completed' ? 'icon-undo' : 'icon-check'"></i>
              {{ task.status === 'completed' ? 'Mark as Pending' : 'Mark as Complete' }}
            </button>
            
            <button @click="duplicateTask" class="action-btn duplicate">
              <i class="icon-copy"></i>
              Duplicate Task
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Delete Task</h2>
          <button @click="closeDeleteModal" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete "<strong>{{ task?.title }}</strong>"?</p>
          <p class="warning-text">This action cannot be undone.</p>
          
          <div class="modal-actions">
            <button @click="closeDeleteModal" class="btn-secondary">
              Cancel
            </button>
            <button @click="confirmDelete" class="btn-danger" :disabled="deleting">
              {{ deleting ? 'Deleting...' : 'Delete Task' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div v-if="toast.show" :class="['toast', toast.type]">
      {{ toast.message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskDetail',
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      task: null,
      loading: true,
      saving: false,
      deleting: false,
      editing: false,
      error: null,
      showDeleteModal: false,
      editForm: {
        title: '',
        description: '',
        status: '',
        priority: '',
        due_date: ''
      },
      toast: {
        show: false,
        message: '',
        type: 'success'
      }
    }
  },
  
  async mounted() {
    await this.fetchTask()
  },
  
  watch: {
    id: {
      handler: 'fetchTask',
      immediate: false
    }
  },
  
  methods: {
    async fetchTask() {
      this.loading = true
      this.error = null
      
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${this.id}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          this.task = await response.json()
        } else if (response.status === 404) {
          this.error = 'Task not found or you do not have permission to view it.'
        } else {
          this.error = 'Failed to load task details.'
        }
      } catch (error) {
        console.error('Error fetching task:', error)
        this.error = 'Network error. Please try again.'
      } finally {
        this.loading = false
      }
    },
    
    async saveChanges() {
      this.saving = true
      
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${this.id}`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.editForm)
        })
        
        if (response.ok) {
          this.task = await response.json()
          this.editing = false
          this.showToast('Task updated successfully!', 'success')
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to update task', 'error')
        }
      } catch (error) {
        console.error('Error updating task:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.saving = false
      }
    },
    
    async toggleStatus() {
      const newStatus = this.task.status === 'completed' ? 'pending' : 'completed'
      
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${this.id}/status`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ status: newStatus })
        })
        
        if (response.ok) {
          this.task = await response.json()
          this.showToast('Task status updated!', 'success')
        } else {
          this.showToast('Failed to update task status', 'error')
        }
      } catch (error) {
        console.error('Error updating task status:', error)
        this.showToast('Network error. Please try again.', 'error')
      }
    },
    
    async confirmDelete() {
      this.deleting = true
      
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${this.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          this.showToast('Task deleted successfully!', 'success')
          setTimeout(() => {
            this.$router.push('/tasks')
          }, 1500)
        } else {
          this.showToast('Failed to delete task', 'error')
        }
      } catch (error) {
        console.error('Error deleting task:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.deleting = false
        this.showDeleteModal = false
      }
    },
    
    async duplicateTask() {
      try {
        const token = localStorage.getItem('token')
        const taskData = {
          title: `${this.task.title} (Copy)`,
          description: this.task.description,
          priority: this.task.priority,
          due_date: this.task.due_date
        }
        
        const response = await fetch('http://localhost:8000/api/tasks', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(taskData)
        })
        
        if (response.ok) {
          const newTask = await response.json()
          this.showToast('Task duplicated successfully!', 'success')
          setTimeout(() => {
            this.$router.push(`/tasks/${newTask.id}`)
          }, 1500)
        } else {
          this.showToast('Failed to duplicate task', 'error')
        }
      } catch (error) {
        console.error('Error duplicating task:', error)
        this.showToast('Network error. Please try again.', 'error')
      }
    },
    
    toggleEdit() {
      if (this.editing) {
        this.cancelEdit()
      } else {
        this.startEdit()
      }
    },
    
    startEdit() {
      this.editing = true
      this.editForm = {
        title: this.task.title,
        description: this.task.description || '',
        status: this.task.status,
        priority: this.task.priority,
        due_date: this.task.due_date ? this.task.due_date.slice(0, 16) : ''
      }
    },
    
    cancelEdit() {
      this.editing = false
      this.editForm = {
        title: '',
        description: '',
        status: '',
        priority: '',
        due_date: ''
      }
    },
    
    deleteTask() {
      this.showDeleteModal = true
    },
    
    closeDeleteModal() {
      this.showDeleteModal = false
    },
    
    goBack() {
      this.$router.go(-1)
    },
    
    getStatusLabel(status) {
      const labels = {
        'pending': 'Pending',
        'in_progress': 'In Progress',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
      }
      return labels[status] || status
    },
    
    formatDateTime(dateString) {
      const date = new Date(dateString)
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    
    isOverdue(dueDate) {
      return new Date(dueDate) < new Date()
    },
    
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type }
      setTimeout(() => {
        this.toast.show = false
      }, 3000)
    }
  }
}
</script>

<style scoped>
.task-detail-page {
  min-height: 100vh;
  background: #f8fafc;
  padding: 2rem;
}

.loading-container, .error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container i {
  font-size: 3rem;
  color: #ef4444;
  margin-bottom: 1rem;
}

.error-container h2 {
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.error-container p {
  color: #6b7280;
  margin-bottom: 2rem;
}

.task-detail-container {
  max-width: 800px;
  margin: 0 auto;
}

.task-header {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem 2rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-navigation {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.back-btn {
  background: none;
  border: none;
  color: #667eea;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  padding: 0.25rem 0;
  transition: color 0.2s ease;
}

.back-btn:hover {
  color: #5a67d8;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.breadcrumb a {
  color: #667eea;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.separator {
  color: #d1d5db;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s ease;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-danger {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s ease;
}

.btn-danger:hover {
  background: #b91c1c;
}

.task-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.status-bar {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  padding: 1.5rem 2rem;
  display: flex;
  gap: 2rem;
  align-items: center;
}

.status-section, .priority-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.status-section label, .priority-section label {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.status-badge {
  padding: 0.375rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.in_progress {
  background: #dbeafe;
  color: #2563eb;
}

.status-badge.completed {
  background: #d1fae5;
  color: #059669;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.priority-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: capitalize;
}

.priority-badge.urgent {
  background: #fee2e2;
  color: #dc2626;
}

.priority-badge.high {
  background: #fef3c7;
  color: #d97706;
}

.priority-badge.medium {
  background: #dbeafe;
  color: #2563eb;
}

.priority-badge.low {
  background: #f3f4f6;
  color: #6b7280;
}

.status-select, .priority-select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  background: white;
}

.main-content {
  padding: 2rem;
}

.title-section {
  margin-bottom: 2rem;
}

.task-title {
  color: #1f2937;
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.3;
  margin: 0;
}

.title-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #d1d5db;
  border-radius: 8px;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.title-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.description-section, .due-date-section {
  margin-bottom: 2rem;
}

.description-section h3, .due-date-section h3 {
  color: #374151;
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.description-text {
  color: #4b5563;
  font-size: 1rem;
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
}

.no-description {
  color: #9ca3af;
  font-style: italic;
  margin: 0;
}

.description-input {
  width: 100%;
  padding: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  line-height: 1.5;
  resize: vertical;
  box-sizing: border-box;
}

.description-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.due-date {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #4b5563;
  font-size: 1rem;
}

.due-date.overdue {
  color: #dc2626;
  font-weight: 600;
}

.overdue-label {
  background: #fee2e2;
  color: #dc2626;
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.no-due-date {
  color: #9ca3af;
  font-style: italic;
}

.due-date-input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
}

.due-date-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.timestamps-section {
  background: #f9fafb;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.timestamp-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.timestamp-item:last-child {
  border-bottom: none;
}

.timestamp-item label {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.timestamp-item span {
  color: #6b7280;
  font-size: 0.875rem;
}

.edit-actions {
  display: flex;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.quick-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.action-btn {
  background: white;
  border: 2px solid #e5e7eb;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}

.action-btn.mark-complete {
  border-color: #10b981;
  color: #10b981;
}

.action-btn.mark-complete:hover {
  background: #10b981;
  color: white;
}

.action-btn.mark-pending {
  border-color: #f59e0b;
  color: #f59e0b;
}

.action-btn.mark-pending:hover {
  background: #f59e0b;
  color: white;
}

.action-btn.duplicate {
  border-color: #6366f1;
  color: #6366f1;
}

.action-btn.duplicate:hover {
  background: #6366f1;
  color: white;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0.25rem;
}

.modal-body {
  padding: 1.5rem;
}

.warning-text {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

/* Toast Notifications */
.toast {
  position: fixed;
  top: 1rem;
  right: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  z-index: 1100;
  animation: slideIn 0.3s ease;
}

.toast.success {
  background: #10b981;
}

.toast.error {
  background: #ef4444;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Icons */
.icon-alert::before { content: '⚠️'; }
.icon-arrow-left::before { content: '←'; }
.icon-edit::before { content: '✏️'; }
.icon-trash::before { content: '🗑️'; }
.icon-x::before { content: '✕'; }
.icon-save::before { content: '💾'; }
.icon-check::before { content: '✅'; }
.icon-undo::before { content: '↶'; }
.icon-copy::before { content: '📋'; }
.icon-calendar::before { content: '📅'; }

/* Responsive Design */
@media (max-width: 768px) {
  .task-detail-page {
    padding: 1rem;
  }
  
  .task-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .header-actions {
    justify-content: center;
  }
  
  .status-bar {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .status-section, .priority-section {
    justify-content: space-between;
  }
  
  .main-content {
    padding: 1.5rem;
  }
  
  .task-title {
    font-size: 1.5rem;
  }
  
  .edit-actions, .quick-actions {
    flex-direction: column;
  }
  
  .timestamp-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}

@media (max-width: 480px) {
  .header-actions {
    flex-direction: column;
  }
  
  .modal {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}</style>