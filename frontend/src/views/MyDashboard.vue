<template>
  <div class="dashboard-layout">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
        <div class="logo">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h2>TaskFlow</h2>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <a href="#" class="nav-item active">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Dashboard
        </a>
        <a href="#" class="nav-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Tasks
        </a>
        <a href="#" class="nav-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Projects
        </a>
        <a href="#" class="nav-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 20V10M18 20V4M6 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Analytics
        </a>
      </nav>
      
      <div class="sidebar-footer">
        <div class="user-profile">
          <div class="user-avatar">
            <img src="https://via.placeholder.com/40" alt="User" />
          </div>
          <div class="user-info">
            <p class="user-name">{{ currentUser?.username || 'User' }}</p>
            <p class="user-role">Administrator</p>
          </div>
        </div>
        <button @click="logout" class="logout-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Logout
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <header class="dashboard-header">
        <div class="header-content">
          <div class="header-left">
            <h1>Task Management Dashboard</h1>
            <p class="header-subtitle">Manage your tasks efficiently and stay productive</p>
          </div>
          <div class="header-actions">
            <button @click="showAddModal = true" class="add-task-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Add New Task
            </button>
          </div>
        </div>
      </header>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon pending">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
              <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>{{ pendingTasks }}</h3>
            <p>Pending Tasks</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon progress">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>{{ inProgressTasks }}</h3>
            <p>In Progress</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon completed">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>{{ completedTasks }}</h3>
            <p>Completed</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon total">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>{{ totalTasks }}</h3>
            <p>Total Tasks</p>
          </div>
        </div>
      </div>

      <!-- Tasks Table -->
      <div class="tasks-section">
        <div class="section-header">
          <h2>Your Tasks</h2>
          <div class="filters">
            <select v-model="statusFilter" @change="filterTasks" class="filter-select">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
            <select v-model="priorityFilter" @change="filterTasks" class="filter-select">
              <option value="">All Priority</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>
        </div>
        
        <div class="tasks-table-container">
          <table class="tasks-table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in filteredTasks" :key="task.id" class="task-row">
                <td class="task-info">
                  <div class="task-title">{{ task.title }}</div>
                  <div class="task-description">{{ task.description }}</div>
                </td>
                <td>
                  <select 
                    v-model="task.status" 
                    @change="updateTask(task)" 
                    class="status-select"
                    :class="`status-${task.status}`"
                  >
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                  </select>
                </td>
                <td>
                  <span class="priority-badge" :class="`priority-${task.priority}`">
                    {{ task.priority }}
                  </span>
                </td>
                <td class="due-date">
                  {{ formatDate(task.due_date) }}
                </td>
                <td class="actions">
                  <button @click="viewTask(task)" class="action-btn view-btn" title="View">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                  <button @click="editTask(task)" class="action-btn edit-btn" title="Edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                  <button @click="confirmDelete(task)" class="action-btn delete-btn" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-if="filteredTasks.length === 0" class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3>No tasks found</h3>
            <p>Create your first task to get started!</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Task Modal -->
    <div v-if="showAddModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Add New Task</h3>
          <button @click="closeModal" class="close-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="addTask" class="task-form">
          <div class="form-group">
            <label for="title">Task Title</label>
            <input 
              v-model="newTask.title" 
              type="text" 
              id="title" 
              placeholder="Enter task title" 
              required 
            />
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea 
              v-model="newTask.description" 
              id="description" 
              placeholder="Enter task description"
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="priority">Priority</label>
              <select v-model="newTask.priority" id="priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="due_date">Due Date</label>
              <input v-model="newTask.due_date" type="date" id="due_date" />
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeModal" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="isLoading">
              <span v-if="!isLoading">Create Task</span>
              <span v-else>Creating...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Task Modal -->
    <div v-if="editMode" class="modal-overlay" @click="cancelEdit">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Edit Task</h3>
          <button @click="cancelEdit" class="close-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="saveEdit" class="task-form">
          <div class="form-group">
            <label for="edit-title">Task Title</label>
            <input 
              v-model="editTaskData.title" 
              type="text" 
              id="edit-title" 
              placeholder="Enter task title" 
              required 
            />
          </div>
          
          <div class="form-group">
            <label for="edit-description">Description</label>
            <textarea 
              v-model="editTaskData.description" 
              id="edit-description" 
              placeholder="Enter task description"
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="edit-priority">Priority</label>
              <select v-model="editTaskData.priority" id="edit-priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="edit-status">Status</label>
              <select v-model="editTaskData.status" id="edit-status">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="edit-due-date">Due Date</label>
            <input v-model="editTaskData.due_date" type="date" id="edit-due-date" />
          </div>
          
          <div class="form-actions">
            <button type="button" @click="cancelEdit" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="isLoading">
              <span v-if="!isLoading">Save Changes</span>
              <span v-else>Saving...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Task Modal -->
    <div v-if="singleTask" class="modal-overlay" @click="closeSingleTask">
      <div class="modal-content view-modal" @click.stop>
        <div class="modal-header">
          <h3>Task Details</h3>
          <button @click="closeSingleTask" class="close-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        
        <div class="task-details">
          <div class="detail-row">
            <label>Title:</label>
            <span>{{ singleTask.title }}</span>
          </div>
          <div class="detail-row">
            <label>Description:</label>
            <span>{{ singleTask.description || 'No description provided' }}</span>
          </div>
          <div class="detail-row">
            <label>Status:</label>
            <span class="status-badge" :class="`status-${singleTask.status}`">
              {{ formatStatus(singleTask.status) }}
            </span>
          </div>
          <div class="detail-row">
            <label>Priority:</label>
            <span class="priority-badge" :class="`priority-${singleTask.priority}`">
              {{ singleTask.priority }}
            </span>
          </div>
          <div class="detail-row">
            <label>Due Date:</label>
            <span>{{ formatDate(singleTask.due_date) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="cancelDelete">
      <div class="modal-content delete-modal" @click.stop>
        <div class="modal-header">
          <h3>Confirm Delete</h3>
        </div>
        <div class="delete-content">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="#ef4444" stroke-width="2"/>
            <line x1="15" y1="9" x2="9" y2="15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
            <line x1="9" y1="9" x2="15" y2="15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Are you sure you want to delete "{{ taskToDelete?.title }}"?</p>
          <p class="warning">This action cannot be undone.</p>
        </div>
        <div class="form-actions">
          <button @click="cancelDelete" class="cancel-btn">Cancel</button>
          <button @click="deleteTask(taskToDelete.id)" class="delete-confirm-btn">Delete Task</button>
        </div>
      </div>
    </div>

    <!-- Toast Messages -->
    <transition name="toast">
      <div v-if="successMessage" class="toast success-toast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ successMessage }}
      </div>
    </transition>

    <transition name="toast">
      <div v-if="errorMessage" class="toast error-toast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'MyDashboard',
  data() {
    return {
      tasks: [],
      newTask: { title: '', description: '', priority: 'medium', due_date: '' },
      editMode: false,
      editTaskData: {},
      errorMessage: '',
      successMessage: '',
      singleTask: null,
      showAddModal: false,
      showDeleteModal: false,
      taskToDelete: null,
      isLoading: false,
      statusFilter: '',
      priorityFilter: '',
      currentUser: null,
    };
  },
  computed: {
    filteredTasks() {
      let filtered = this.tasks;
      
      if (this.statusFilter) {
        filtered = filtered.filter(task => task.status === this.statusFilter);
      }
      
      if (this.priorityFilter) {
        filtered = filtered.filter(task => task.priority === this.priorityFilter);
      }
      
      return filtered;
    },
    pendingTasks() {
      return this.tasks.filter(task => task.status === 'pending').length;
    },
    inProgressTasks() {
      return this.tasks.filter(task => task.status === 'in_progress').length;
    },
    completedTasks() {
      return this.tasks.filter(task => task.status === 'completed').length;
    },
    totalTasks() {
      return this.tasks.length;
    }
  },
  methods: {
    async fetchTasks() {
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch('http://localhost:8000/api/tasks', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (res.ok) {
          this.tasks = data;
        } else {
          this.showError(data.error || 'Failed to fetch tasks');
        }
      } catch {
        this.showError('Network error. Please try again.');
      }
    },
    async fetchSingleTask(id) {
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (res.ok) {
          this.singleTask = data;
        } else {
          this.showError(data.error || 'Task not found');
        }
      } catch {
        this.showError('Network error. Please try again.');
      }
    },
    async updateTask(task) {
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(task)
        });
        const data = await res.json();
        if (res.ok) {
          this.showSuccess(data.message || 'Task updated successfully');
        } else {
          this.showError(data.error || 'Failed to update task');
        }
      } catch {
        this.showError('Network error. Please try again.');
      }
    },
    async addTask() {
      this.isLoading = true;
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch('http://localhost:8000/api/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.newTask)
        });
        const data = await res.json();
        if (res.ok) {
          this.showSuccess(data.message || 'Task created successfully');
          this.newTask = { title: '', description: '', priority: 'medium', due_date: '' };
          this.showAddModal = false;
          this.fetchTasks();
        } else {
          this.showError(data.error || 'Failed to add task');
        }
      } catch {
        this.showError('Network error. Please try again.');
      } finally {
        this.isLoading = false;
      }
    },
    editTask(task) {
      this.editMode = true;
      this.editTaskData = { ...task };
    },
    async saveEdit() {
      this.isLoading = true;
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${this.editTaskData.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.editTaskData)
        });
        const data = await res.json();
        if (res.ok) {
          this.showSuccess(data.message || 'Task updated successfully');
          this.editMode = false;
          this.fetchTasks();
        } else {
          this.showError(data.error || 'Failed to update task');
        }
      } catch {
        this.showError('Network error. Please try again.');
      } finally {
        this.isLoading = false;
      }
    },
    async deleteTask(id) {
      this.isLoading = true;
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${id}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (res.ok) {
          this.showSuccess(data.message || 'Task deleted successfully');
          this.fetchTasks();
        } else {
          this.showError(data.error || 'Failed to delete task');
        }
      } catch {
        this.showError('Network error. Please try again.');
      } finally {
        this.isLoading = false;
        this.showDeleteModal = false;
        this.taskToDelete = null;
      }
    },
    viewTask(task) {
      this.singleTask = task;
    },
    confirmDelete(task) {
      this.taskToDelete = task;
      this.showDeleteModal = true;
    },
    cancelDelete() {
      this.showDeleteModal = false;
      this.taskToDelete = null;
    },
    cancelEdit() {
      this.editMode = false;
      this.editTaskData = {};
    },
    closeModal() {
      this.showAddModal = false;
      this.newTask = { title: '', description: '', priority: 'medium', due_date: '' };
    },
    closeSingleTask() {
      this.singleTask = null;
    },
    filterTasks() {
      // This method is called when filters change
    },
    formatDate(date) {
      if (!date) return 'No due date';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    formatStatus(status) {
      return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    },
    showSuccess(message) {
      this.successMessage = message;
      this.errorMessage = '';
      setTimeout(() => {
        this.successMessage = '';
      }, 4000);
    },
    showError(message) {
      this.errorMessage = message;
      this.successMessage = '';
      setTimeout(() => {
        this.errorMessage = '';
      }, 4000);
    },
    logout() {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('user_info');
      this.$router.push('/');
    }
  },
  mounted() {
    this.fetchTasks();
    // Get current user info
    const userInfo = localStorage.getItem('user_info');
    if (userInfo) {
      this.currentUser = JSON.parse(userInfo);
    }
  }
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Sidebar Styles */
.sidebar {
  width: 280px;
  background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 100;
}

.sidebar-header {
  padding: 32px 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo svg {
  color: #60a5fa;
}

.logo h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
}

.sidebar-nav {
  flex: 1;
  padding: 24px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  color: #cbd5e1;
  text-decoration: none;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-item.active {
  background: rgba(96, 165, 250, 0.2);
  color: #60a5fa;
  border-left-color: #60a5fa;
}

.sidebar-footer {
  padding: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.user-avatar img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-info .user-name {
  font-weight: 600;
  color: white;
  margin-bottom: 2px;
}

.user-info .user-role {
  font-size: 0.875rem;
  color: #94a3b8;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 12px 16px;
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.3);
  color: #fef2f2;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 32px;
  max-width: calc(100vw - 280px);
}

/* Header */
.dashboard-header {
  margin-bottom: 32px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
  background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.header-subtitle {
  color: #64748b;
  font-size: 1rem;
}

.add-task-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.add-task-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
  transition: all 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-icon.pending {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-icon.progress {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-icon.completed {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon.total {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.stat-content h3 {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.stat-content p {
  color: #64748b;
  font-weight: 500;
}

/* Tasks Section */
.tasks-section {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-bottom: 1px solid #f1f5f9;
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.filters {
  display: flex;
  gap: 12px;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  min-width: 120px;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table */
.tasks-table-container {
  overflow-x: auto;
}

.tasks-table {
  width: 100%;
  border-collapse: collapse;
}

.tasks-table th {
  background: #f8fafc;
  padding: 16px 24px;
  text-align: left;
  font-weight: 600;
  color: #475569;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.task-row {
  border-bottom: 1px solid #f1f5f9;
  transition: all 0.2s ease;
}

.task-row:hover {
  background: #f8fafc;
}

.tasks-table td {
  padding: 16px 24px;
  vertical-align: middle;
}

.task-info .task-title {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.task-info .task-description {
  color: #64748b;
  font-size: 0.875rem;
}

.status-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  font-size: 0.875rem;
  min-width: 120px;
}

.status-select.status-pending {
  border-color: #f59e0b;
  color: #92400e;
}

.status-select.status-in_progress {
  border-color: #3b82f6;
  color: #1e40af;
}

.status-select.status-completed {
  border-color: #10b981;
  color: #065f46;
}

.priority-badge, .status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.priority-high {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.priority-medium {
  background: #fffbeb;
  color: #92400e;
  border: 1px solid #fcd34d;
}

.priority-low {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #86efac;
}

.status-pending {
  background: #fffbeb;
  color: #92400e;
  border: 1px solid #fcd34d;
}

.status-in_progress {
  background: #eff6ff;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.status-completed {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #86efac;
}

.due-date {
  color: #64748b;
  font-size: 0.875rem;
}

.actions {
  display: flex;
  gap: 8px;
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

.view-btn {
  background: #eff6ff;
  color: #1d4ed8;
}

.view-btn:hover {
  background: #dbeafe;
}

.edit-btn {
  background: #f0fdf4;
  color: #166534;
}

.edit-btn:hover {
  background: #dcfce7;
}

.delete-btn {
  background: #fef2f2;
  color: #dc2626;
}

.delete-btn:hover {
  background: #fee2e2;
}

/* Empty State */
.empty-state {
  padding: 64px 32px;
  text-align: center;
  color: #64748b;
}

.empty-state svg {
  margin-bottom: 16px;
  color: #cbd5e1;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 8px;
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
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-bottom: 1px solid #f1f5f9;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
}

.close-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f8fafc;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: #e2e8f0;
  color: #374151;
}

/* Form Styles */
.task-form {
  padding: 32px;
}

.form-group {
  margin-bottom: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 32px;
}

.cancel-btn {
  padding: 12px 24px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.cancel-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.submit-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.delete-confirm-btn {
  padding: 12px 24px;
  background: #dc2626;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.delete-confirm-btn:hover {
  background: #b91c1c;
}

/* View Modal */
.view-modal {
  max-width: 600px;
}

.task-details {
  padding: 32px;
}

.detail-row {
  display: flex;
  margin-bottom: 16px;
  gap: 16px;
}

.detail-row label {
  font-weight: 600;
  color: #374151;
  min-width: 100px;
}

.detail-row span {
  color: #64748b;
  flex: 1;
}

/* Delete Modal */
.delete-modal {
  max-width: 400px;
}

.delete-content {
  padding: 32px;
  text-align: center;
}

.delete-content svg {
  margin-bottom: 16px;
}

.delete-content p {
  margin-bottom: 8px;
  color: #374151;
}

.delete-content .warning {
  color: #dc2626;
  font-size: 0.875rem;
}

/* Toast Messages */
.toast {
  position: fixed;
  top: 24px;
  right: 24px;
  padding: 16px 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 500;
  z-index: 1100;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  max-width: 400px;
}

.success-toast {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #86efac;
}

.error-toast {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

/* Toast Transitions */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .main-content {
    margin-left: 0;
    max-width: 100vw;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .header-content {
    flex-direction: column;
    align-items: stretch;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 16px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .tasks-table-container {
    font-size: 0.875rem;
  }
  
  .tasks-table th,
  .tasks-table td {
    padding: 12px 16px;
  }
  
  .modal-content {
    width: 95%;
    margin: 16px;
  }
  
  .task-form {
    padding: 24px;
  }
}
</style>
