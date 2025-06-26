<template>
  <div class="dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
      <div class="header-content">
        <div class="welcome-section">
          <h1>Welcome back, {{ user?.username }}!</h1>
          <p>Here's what's happening with your tasks today</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="btn-primary">
            <i class="icon-plus"></i>
            New Task
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon pending">
          <i class="icon-clock"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.pending }}</h3>
          <p>Pending Tasks</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon in-progress">
          <i class="icon-play"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.inProgress }}</h3>
          <p>In Progress</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon completed">
          <i class="icon-check"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.completed }}</h3>
          <p>Completed</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon total">
          <i class="icon-list"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.total }}</h3>
          <p>Total Tasks</p>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
      <!-- Recent Tasks -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Recent Tasks</h2>
          <router-link to="/tasks" class="view-all-link">View All</router-link>
        </div>
        <div class="card-content">
          <div v-if="loading" class="loading">
            <div class="spinner"></div>
            <p>Loading tasks...</p>
          </div>
          
          <div v-else-if="recentTasks.length === 0" class="empty-state">
            <i class="icon-inbox"></i>
            <h3>No tasks yet</h3>
            <p>Create your first task to get started!</p>
            <button @click="showCreateModal = true" class="btn-secondary">
              Create Task
            </button>
          </div>
          
          <div v-else class="task-list">
            <div 
              v-for="task in recentTasks" 
              :key="task.id" 
              class="task-item"
              @click="viewTask(task.id)"
            >
              <div class="task-status">
                <span :class="['status-badge', task.status]">
                  {{ getStatusLabel(task.status) }}
                </span>
              </div>
              <div class="task-content">
                <h4>{{ task.title }}</h4>
                <p v-if="task.description">{{ truncateText(task.description, 100) }}</p>
                <div class="task-meta">
                  <span class="priority" :class="task.priority">
                    {{ task.priority }} priority
                  </span>
                  <span v-if="task.due_date" class="due-date">
                    Due: {{ formatDate(task.due_date) }}
                  </span>
                </div>
              </div>
              <div class="task-actions">
                <button 
                  @click.stop="updateTaskStatus(task, 'completed')"
                  v-if="task.status !== 'completed'"
                  class="action-btn complete"
                  title="Mark as complete"
                >
                  <i class="icon-check"></i>
                </button>
                <button 
                  @click.stop="editTask(task)"
                  class="action-btn edit"
                  title="Edit task"
                >
                  <i class="icon-edit"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions & Overview -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Quick Actions</h2>
        </div>
        <div class="card-content">
          <div class="quick-actions">
            <button @click="showCreateModal = true" class="quick-action-btn">
              <i class="icon-plus"></i>
              <span>Create Task</span>
            </button>
            <router-link to="/tasks" class="quick-action-btn">
              <i class="icon-list"></i>
              <span>View All Tasks</span>
            </router-link>
            <router-link to="/profile" class="quick-action-btn">
              <i class="icon-user"></i>
              <span>Profile Settings</span>
            </router-link>
          </div>

          <!-- Priority Breakdown -->
          <div class="priority-breakdown">
            <h3>Tasks by Priority</h3>
            <div class="priority-list">
              <div class="priority-item">
                <span class="priority-label urgent">Urgent</span>
                <span class="priority-count">{{ priorityStats.urgent }}</span>
              </div>
              <div class="priority-item">
                <span class="priority-label high">High</span>
                <span class="priority-count">{{ priorityStats.high }}</span>
              </div>
              <div class="priority-item">
                <span class="priority-label medium">Medium</span>
                <span class="priority-count">{{ priorityStats.medium }}</span>
              </div>
              <div class="priority-item">
                <span class="priority-label low">Low</span>
                <span class="priority-count">{{ priorityStats.low }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Task Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Create New Task</h2>
          <button @click="closeModal" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        <form @submit.prevent="createTask" class="modal-body">
          <div class="form-group">
            <label for="taskTitle">Title *</label>
            <input
              type="text"
              id="taskTitle"
              v-model="newTask.title"
              placeholder="Enter task title"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="taskDescription">Description</label>
            <textarea
              id="taskDescription"
              v-model="newTask.description"
              placeholder="Enter task description"
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="taskPriority">Priority</label>
              <select id="taskPriority" v-model="newTask.priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="taskDueDate">Due Date</label>
              <input
                type="datetime-local"
                id="taskDueDate"
                v-model="newTask.due_date"
              />
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary" :disabled="creating">
              {{ creating ? 'Creating...' : 'Create Task' }}
            </button>
          </div>
        </form>
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
  name: 'Dashboard',
  data() {
    return {
      loading: true,
      creating: false,
      showCreateModal: false,
      tasks: [],
      newTask: {
        title: '',
        description: '',
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
    user() {
      const userData = localStorage.getItem('user')
      return userData ? JSON.parse(userData) : null
    },
    
    recentTasks() {
      return this.tasks.slice(0, 5)
    },
    
    stats() {
      const pending = this.tasks.filter(task => task.status === 'pending').length
      const inProgress = this.tasks.filter(task => task.status === 'in_progress').length
      const completed = this.tasks.filter(task => task.status === 'completed').length
      const total = this.tasks.length
      
      return { pending, inProgress, completed, total }
    },
    
    priorityStats() {
      const urgent = this.tasks.filter(task => task.priority === 'urgent').length
      const high = this.tasks.filter(task => task.priority === 'high').length
      const medium = this.tasks.filter(task => task.priority === 'medium').length
      const low = this.tasks.filter(task => task.priority === 'low').length
      
      return { urgent, high, medium, low }
    }
  },
  
  async mounted() {
    await this.fetchTasks()
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
    
    async createTask() {
      this.creating = true
      try {
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/tasks', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.newTask)
        })
        
        if (response.ok) {
          const task = await response.json()
          this.tasks.unshift(task)
          this.closeModal()
          this.showToast('Task created successfully!', 'success')
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to create task', 'error')
        }
      } catch (error) {
        console.error('Error creating task:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.creating = false
      }
    },
    
    async updateTaskStatus(task, newStatus) {
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
    
    viewTask(taskId) {
      this.$router.push(`/tasks/${taskId}`)
    },
    
    editTask(task) {
      // You can implement inline editing or navigate to edit page
      this.$router.push(`/tasks/${task.id}`)
    },
    
    closeModal() {
      this.showCreateModal = false
      this.newTask = {
        title: '',
        description: '',
        priority: 'medium',
        due_date: ''
      }
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
        year: 'numeric'
      })
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
.dashboard {
  min-height: 100vh;
  background: #f8fafc;
  padding: 2rem;
}

.dashboard-header {
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

.welcome-section h1 {
  color: #1f2937;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.welcome-section p {
  color: #6b7280;
  font-size: 1rem;
}

.header-actions .btn-primary {
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.stat-icon.pending { background: #f59e0b; }
.stat-icon.in-progress { background: #3b82f6; }
.stat-icon.completed { background: #10b981; }
.stat-icon.total { background: #6366f1; }

.stat-content h3 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.stat-content p {
  color: #6b7280;
  margin: 0;
  font-size: 0.875rem;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.dashboard-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h2 {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.view-all-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
}

.view-all-link:hover {
  text-decoration: underline;
}

.card-content {
  padding: 1.5rem;
}

.loading {
  text-align: center;
  padding: 2rem;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 2rem;
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

.task-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-item {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.task-item:hover {
  border-color: #667eea;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.task-status {
  flex-shrink: 0;
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

.task-content {
  flex: 1;
}

.task-content h4 {
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
}

.task-content p {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 0.5rem 0;
}

.task-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.75rem;
}

.priority {
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.priority.urgent {
  background: #fee2e2;
  color: #dc2626;
}

.priority.high {
  background: #fef3c7;
  color: #d97706;
}

.priority.medium {
  background: #dbeafe;
  color: #2563eb;
}

.priority.low {
  background: #f3f4f6;
  color: #6b7280;
}

.due-date {
  color: #6b7280;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
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

.action-btn.edit {
  background: #e0e7ff;
  color: #5b21b6;
}

.action-btn:hover {
  transform: translateY(-1px);
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.quick-action-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  text-decoration: none;
  color: #374151;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.quick-action-btn:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.priority-breakdown h3 {
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.priority-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.priority-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: #f9fafb;
  border-radius: 6px;
}

.priority-label {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.priority-count {
  font-weight: 600;
  color: #374151;
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
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
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
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-secondary:hover {
  background: #e5e7eb;
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

/* Icons (you can replace with your preferred icon library) */
.icon-plus::before { content: '+'; }
.icon-clock::before { content: '⏰'; }
.icon-play::before { content: '▶️'; }
.icon-check::before { content: '✅'; }
.icon-list::before { content: '📋'; }
.icon-inbox::before { content: '📥'; }
.icon-edit::before { content: '✏️'; }
.icon-x::before { content: '✕'; }
.icon-user::before { content: '👤'; }

/* Responsive Design */
@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .dashboard {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .task-item {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .task-actions {
    justify-content: flex-end;
  }
}
</style>