<template>
  <div class="task-list-page">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1>My Tasks</h1>
          <p>Manage and organize your tasks</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="btn-primary">
            <i class="icon-plus"></i>
            New Task
          </button>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
      <div class="search-box">
        <i class="icon-search"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search tasks..."
          class="search-input"
        />
      </div>
      
      <div class="filters">
        <select v-model="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        
        <select v-model="priorityFilter" class="filter-select">
          <option value="">All Priority</option>
          <option value="urgent">Urgent</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>
        
        <select v-model="sortBy" class="filter-select">
          <option value="created_at">Date Created</option>
          <option value="due_date">Due Date</option>
          <option value="priority">Priority</option>
          <option value="title">Title</option>
        </select>
        
        <button @click="clearFilters" class="btn-secondary">
          Clear Filters
        </button>
      </div>
    </div>

    <!-- Task Stats -->
    <div class="task-stats">
      <div class="stat-item">
        <span class="stat-label">Total:</span>
        <span class="stat-value">{{ filteredTasks.length }}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Pending:</span>
        <span class="stat-value pending">{{ pendingCount }}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">In Progress:</span>
        <span class="stat-value in-progress">{{ inProgressCount }}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Completed:</span>
        <span class="stat-value completed">{{ completedCount }}</span>
      </div>
    </div>

    <!-- Task List -->
    <div class="tasks-container">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading tasks...</p>
      </div>
      
      <div v-else-if="filteredTasks.length === 0" class="empty-state">
        <i class="icon-inbox"></i>
        <h3>{{ searchQuery || statusFilter || priorityFilter ? 'No matching tasks' : 'No tasks yet' }}</h3>
        <p>{{ searchQuery || statusFilter || priorityFilter ? 'Try adjusting your filters' : 'Create your first task to get started!' }}</p>
        <button v-if="!searchQuery && !statusFilter && !priorityFilter" @click="showCreateModal = true" class="btn-primary">
          Create Task
        </button>
      </div>
      
      <div v-else class="task-grid">
        <div 
          v-for="task in paginatedTasks" 
          :key="task.id" 
          class="task-card"
          @click="viewTask(task.id)"
        >
          <div class="task-header">
            <div class="task-status">
              <span :class="['status-badge', task.status]">
                {{ getStatusLabel(task.status) }}
              </span>
            </div>
            <div class="task-actions" @click.stop>
              <button 
                @click="toggleTaskStatus(task)"
                :class="['action-btn', task.status === 'completed' ? 'undo' : 'complete']"
                :title="task.status === 'completed' ? 'Mark as pending' : 'Mark as complete'"
              >
                <i :class="task.status === 'completed' ? 'icon-undo' : 'icon-check'"></i>
              </button>
              <button 
                @click="editTask(task)"
                class="action-btn edit"
                title="Edit task"
              >
                <i class="icon-edit"></i>
              </button>
              <button 
                @click="deleteTask(task)"
                class="action-btn delete"
                title="Delete task"
              >
                <i class="icon-trash"></i>
              </button>
            </div>
          </div>
          
          <div class="task-content">
            <h3 class="task-title">{{ task.title }}</h3>
            <p v-if="task.description" class="task-description">
              {{ truncateText(task.description, 150) }}
            </p>
            
            <div class="task-meta">
              <span :class="['priority-badge', task.priority]">
                {{ task.priority }} priority
              </span>
              
              <span v-if="task.due_date" class="due-date" :class="{ 'overdue': isOverdue(task.due_date) }">
                Due: {{ formatDate(task.due_date) }}
              </span>
              
              <span class="created-date">
                Created: {{ formatDate(task.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="pagination-btn"
        >
          Previous
        </button>
        
        <span class="pagination-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        
        <button 
          @click="currentPage++" 
          :disabled="currentPage === totalPages"
          class="pagination-btn"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Create Task Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>{{ editingTask ? 'Edit Task' : 'Create New Task' }}</h2>
          <button @click="closeCreateModal" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        <form @submit.prevent="saveTask" class="modal-body">
          <div class="form-group">
            <label for="taskTitle">Title *</label>
            <input
              type="text"
              id="taskTitle"
              v-model="taskForm.title"
              placeholder="Enter task title"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="taskDescription">Description</label>
            <textarea
              id="taskDescription"
              v-model="taskForm.description"
              placeholder="Enter task description"
              rows="4"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="taskStatus">Status</label>
              <select id="taskStatus" v-model="taskForm.status">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="taskPriority">Priority</label>
              <select id="taskPriority" v-model="taskForm.priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="taskDueDate">Due Date</label>
            <input
              type="datetime-local"
              id="taskDueDate"
              v-model="taskForm.due_date"
            />
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeCreateModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary" :disabled="saving">
              {{ saving ? 'Saving...' : (editingTask ? 'Update Task' : 'Create Task') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal small" @click.stop>
        <div class="modal-header">
          <h2>Delete Task</h2>
          <button @click="closeDeleteModal" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete "<strong>{{ taskToDelete?.title }}</strong>"?</p>
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
  name: 'TaskList',
  data() {
    return {
      loading: true,
      saving: false,
      deleting: false,
      tasks: [],
      searchQuery: '',
      statusFilter: '',
      priorityFilter: '',
      sortBy: 'created_at',
      currentPage: 1,
      itemsPerPage: 12,
      showCreateModal: false,
      showDeleteModal: false,
      editingTask: null,
      taskToDelete: null,
      taskForm: {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium',
        due_date: ''
      },
      toast: {
        show: false,
        message: '',
        type: 'success'
      }
    }
  },
  
  computed: {
    filteredTasks() {
      let filtered = [...this.tasks]
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(task => 
          task.title.toLowerCase().includes(query) ||
          (task.description && task.description.toLowerCase().includes(query))
        )
      }
      
      // Status filter
      if (this.statusFilter) {
        filtered = filtered.filter(task => task.status === this.statusFilter)
      }
      
      // Priority filter
      if (this.priorityFilter) {
        filtered = filtered.filter(task => task.priority === this.priorityFilter)
      }
      
      // Sort
      filtered.sort((a, b) => {
        if (this.sortBy === 'priority') {
          const priorityOrder = { urgent: 4, high: 3, medium: 2, low: 1 }
          return priorityOrder[b.priority] - priorityOrder[a.priority]
        } else if (this.sortBy === 'title') {
          return a.title.localeCompare(b.title)
        } else {
          return new Date(b[this.sortBy]) - new Date(a[this.sortBy])
        }
      })
      
      return filtered
    },
    
    paginatedTasks() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredTasks.slice(start, end)
    },
    
    totalPages() {
      return Math.ceil(this.filteredTasks.length / this.itemsPerPage)
    },
    
    pendingCount() {
      return this.filteredTasks.filter(task => task.status === 'pending').length
    },
    
    inProgressCount() {
      return this.filteredTasks.filter(task => task.status === 'in_progress').length
    },
    
    completedCount() {
      return this.filteredTasks.filter(task => task.status === 'completed').length
    }
  },
  
  async mounted() {
    await this.fetchTasks()
  },
  
  watch: {
    filteredTasks() {
      // Reset to first page when filters change
      this.currentPage = 1
    }
  },
  
  methods: {
    async fetchTasks() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/tasks', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          this.tasks = await response.json()
        } else {
          this.showToast('Failed to load tasks', 'error')
        }
      } catch (error) {
        console.error('Error fetching tasks:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.loading = false
      }
    },
    
    async saveTask() {
      this.saving = true
      try {
        const token = localStorage.getItem('token')
        const url = this.editingTask 
          ? `http://localhost:8000/api/tasks/${this.editingTask.id}`
          : 'http://localhost:8000/api/tasks'
        
        const method = this.editingTask ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
          method,
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.taskForm)
        })
        
        if (response.ok) {
          const task = await response.json()
          
          if (this.editingTask) {
            const index = this.tasks.findIndex(t => t.id === this.editingTask.id)
            if (index !== -1) {
              this.tasks.splice(index, 1, task)
            }
            this.showToast('Task updated successfully!', 'success')
          } else {
            this.tasks.unshift(task)
            this.showToast('Task created successfully!', 'success')
          }
          
          this.closeCreateModal()
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to save task', 'error')
        }
      } catch (error) {
        console.error('Error saving task:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.saving = false
      }
    },
    
    async toggleTaskStatus(task) {
      const newStatus = task.status === 'completed' ? 'pending' : 'completed'
      
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${task.id}/status`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ status: newStatus })
        })
        
        if (response.ok) {
          const updatedTask = await response.json()
          const index = this.tasks.findIndex(t => t.id === task.id)
          if (index !== -1) {
            this.tasks.splice(index, 1, updatedTask)
          }
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
      if (!this.taskToDelete) return
      
      this.deleting = true
      try {
        const token = localStorage.getItem('token')
        const response = await fetch(`http://localhost:8000/api/tasks/${this.taskToDelete.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          this.tasks = this.tasks.filter(t => t.id !== this.taskToDelete.id)
          this.showToast('Task deleted successfully!', 'success')
          this.closeDeleteModal()
        } else {
          this.showToast('Failed to delete task', 'error')
        }
      } catch (error) {
        console.error('Error deleting task:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.deleting = false
      }
    },
    
    viewTask(taskId) {
      this.$router.push(`/tasks/${taskId}`)
    },
    
    editTask(task) {
      this.editingTask = task
      this.taskForm = {
        title: task.title,
        description: task.description || '',
        status: task.status,
        priority: task.priority,
        due_date: task.due_date ? task.due_date.slice(0, 16) : ''
      }
      this.showCreateModal = true
    },
    
    deleteTask(task) {
      this.taskToDelete = task
      this.showDeleteModal = true
    },
    
    closeCreateModal() {
      this.showCreateModal = false
      this.editingTask = null
      this.taskForm = {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium',
        due_date: ''
      }
    },
    
    closeDeleteModal() {
      this.showDeleteModal = false
      this.taskToDelete = null
    },
    
    clearFilters() {
      this.searchQuery = ''
      this.statusFilter = ''
      this.priorityFilter = ''
      this.sortBy = 'created_at'
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
    
    truncateText(text, length) {
      return text.length > length ? text.substring(0, length) + '...' : text
    },
    
    formatDate(dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
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
.task-list-page {
  min-height: 100vh;
  background: #f8fafc;
  padding: 2rem;
}

.page-header {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
  padding: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-section h1 {
  color: #1f2937;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.title-section p {
  color: #6b7280;
  font-size: 1rem;
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

.filters-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
  padding: 1.5rem;
}

.search-box {
  position: relative;
  margin-bottom: 1rem;
}

.search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filters {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
}

.filter-select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  background: white;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.task-stats {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
}

.stat-value {
  font-weight: 600;
  font-size: 1.125rem;
}

.stat-value.pending { color: #f59e0b; }
.stat-value.in-progress { color: #3b82f6; }
.stat-value.completed { color: #10b981; }

.tasks-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  min-height: 400px;
}

.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  text-align: center;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state i {
  font-size: 3rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-state h3 {
  color: #374151;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.task-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.task-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.task-card:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
  transform: translateY(-1px);
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
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

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.action-btn.complete {
  background: #d1fae5;
  color: #059669;
}

.action-btn.undo {
  background: #fef3c7;
  color: #d97706;
}

.action-btn.edit {
  background: #e0e7ff;
  color: #5b21b6;
}

.action-btn.delete {
  background: #fee2e2;
  color: #dc2626;
}

.action-btn:hover {
  transform: translateY(-1px);
}

.task-title {
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.task-description {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1rem;
}

.task-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  font-size: 0.75rem;
}

.priority-badge {
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-weight: 500;
  text-transform: capitalize;
  width: fit-content;
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

.due-date {
  color: #6b7280;
}

.due-date.overdue {
  color: #dc2626;
  font-weight: 600;
}

.created-date {
  color: #9ca3af;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-btn {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
}

.pagination-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
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
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal.small {
  max-width: 400px;
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

.form-group {
  margin-bottom: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  display: block;
  color: #374151;
  font-weight: 500;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.btn-danger {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-danger:hover {
  background: #b91c1c;
}

.btn-danger:disabled {
  background: #d1d5db;
  cursor: not-allowed;
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
.icon-plus::before { content: '+'; }
.icon-search::before { content: '🔍'; }
.icon-inbox::before { content: '📥'; }
.icon-check::before { content: '✅'; }
.icon-undo::before { content: '↶'; }
.icon-edit::before { content: '✏️'; }
.icon-trash::before { content: '🗑️'; }
.icon-x::before { content: '✕'; }

/* Responsive Design */
@media (max-width: 1024px) {
  .task-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 768px) {
  .task-list-page {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .filters {
    flex-direction: column;
    align-items: stretch;
  }
  
  .task-stats {
    justify-content: center;
    gap: 1rem;
  }
  
  .task-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}
</style>