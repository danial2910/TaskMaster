<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Brand/Logo -->
      <div class="navbar-brand">
        <router-link to="/dashboard" class="brand-link">
          <div class="brand-icon">
            <i class="icon-tasks"></i>
          </div>
          <span class="brand-text">TaskManager</span>
        </router-link>
      </div>

      <!-- Desktop Navigation -->
      <div class="navbar-menu desktop-menu">
        <div class="nav-links">
          <router-link to="/dashboard" class="nav-link">
            <i class="icon-dashboard"></i>
            <span>Dashboard</span>
          </router-link>
          
          <router-link to="/tasks" class="nav-link">
            <i class="icon-list"></i>
            <span>Tasks</span>
          </router-link>
          
          <!-- Quick Add Task -->
          <button @click="showQuickAdd = true" class="nav-link quick-add-btn">
            <i class="icon-plus"></i>
            <span>New Task</span>
          </button>
        </div>

        <!-- User Menu -->
        <div class="user-menu" ref="userMenu">
          <button @click="toggleUserDropdown" class="user-button">
            <div class="user-avatar">
              <span class="avatar-text">{{ userInitials }}</span>
            </div>
            <div class="user-info">
              <span class="user-name">{{ user?.username }}</span>
              <span class="user-email">{{ user?.email }}</span>
            </div>
            <i class="dropdown-icon" :class="{ 'rotated': showUserDropdown }">▼</i>
          </button>
          
          <!-- Dropdown Menu -->
          <div v-if="showUserDropdown" class="dropdown-menu">
            <router-link to="/profile" class="dropdown-item" @click="closeUserDropdown">
              <i class="icon-user"></i>
              <span>Profile</span>
            </router-link>
            
            <div class="dropdown-divider"></div>
            
            <button @click="logout" class="dropdown-item logout-item">
              <i class="icon-logout"></i>
              <span>Logout</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="toggleMobileMenu" class="mobile-menu-btn">
        <i :class="showMobileMenu ? 'icon-x' : 'icon-menu'"></i>
      </button>
    </div>

    <!-- Mobile Navigation -->
    <div v-if="showMobileMenu" class="mobile-menu">
      <div class="mobile-nav-links">
        <router-link to="/dashboard" class="mobile-nav-link" @click="closeMobileMenu">
          <i class="icon-dashboard"></i>
          <span>Dashboard</span>
        </router-link>
        
        <router-link to="/tasks" class="mobile-nav-link" @click="closeMobileMenu">
          <i class="icon-list"></i>
          <span>Tasks</span>
        </router-link>
        
        <button @click="openQuickAddMobile" class="mobile-nav-link">
          <i class="icon-plus"></i>
          <span>New Task</span>
        </button>
        
        <router-link to="/profile" class="mobile-nav-link" @click="closeMobileMenu">
          <i class="icon-user"></i>
          <span>Profile</span>
        </router-link>
      </div>
      
      <div class="mobile-user-section">
        <div class="mobile-user-info">
          <div class="user-avatar">
            <span class="avatar-text">{{ userInitials }}</span>
          </div>
          <div class="user-details">
            <span class="user-name">{{ user?.username }}</span>
            <span class="user-email">{{ user?.email }}</span>
          </div>
        </div>
        
        <button @click="logout" class="mobile-logout-btn">
          <i class="icon-logout"></i>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Quick Add Task Modal -->
    <div v-if="showQuickAdd" class="modal-overlay" @click="closeQuickAdd">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Quick Add Task</h2>
          <button @click="closeQuickAdd" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        
        <form @submit.prevent="createTask" class="modal-body">
          <div class="form-group">
            <label for="quickTitle">Title *</label>
            <input
              type="text"
              id="quickTitle"
              v-model="quickTaskForm.title"
              placeholder="Enter task title"
              required
              ref="quickTitleInput"
            />
          </div>
          
          <div class="form-group">
            <label for="quickDescription">Description</label>
            <textarea
              id="quickDescription"
              v-model="quickTaskForm.description"
              placeholder="Enter task description (optional)"
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="quickPriority">Priority</label>
              <select id="quickPriority" v-model="quickTaskForm.priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="quickDueDate">Due Date</label>
              <input
                type="date"
                id="quickDueDate"
                v-model="quickTaskForm.due_date"
              />
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeQuickAdd" class="btn-secondary">
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
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  data() {
    return {
      showUserDropdown: false,
      showMobileMenu: false,
      showQuickAdd: false,
      creating: false,
      quickTaskForm: {
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
    
    userInitials() {
      if (!this.user?.username) return '?'
      return this.user.username.charAt(0).toUpperCase()
    }
  },
  
  mounted() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', this.handleClickOutside)
  },
  
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  
  methods: {
    toggleUserDropdown() {
      this.showUserDropdown = !this.showUserDropdown
    },
    
    closeUserDropdown() {
      this.showUserDropdown = false
    },
    
    toggleMobileMenu() {
      this.showMobileMenu = !this.showMobileMenu
    },
    
    closeMobileMenu() {
      this.showMobileMenu = false
    },
    
    openQuickAddMobile() {
      this.closeMobileMenu()
      this.showQuickAdd = true
    },
    
    closeQuickAdd() {
      this.showQuickAdd = false
      this.resetQuickTaskForm()
    },
    
    resetQuickTaskForm() {
      this.quickTaskForm = {
        title: '',
        description: '',
        priority: 'medium',
        due_date: ''
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
          body: JSON.stringify(this.quickTaskForm)
        })
        
        if (response.ok) {
          const task = await response.json()
          this.showToast('Task created successfully!', 'success')
          this.closeQuickAdd()
          
          // Emit event to parent or use event bus to update other components
          this.$emit('task-created', task)
          
          // Optionally navigate to the new task
          // this.$router.push(`/tasks/${task.id}`)
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
    
    logout() {
      // Clear authentication data
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      this.showToast('Logged out successfully', 'success')
      
      // Close menus
      this.closeUserDropdown()
      this.closeMobileMenu()
      
      // Redirect to login
      setTimeout(() => {
        this.$router.push('/login')
      }, 1000)
    },
    
    handleClickOutside(event) {
      // Close user dropdown if clicking outside
      if (this.$refs.userMenu && !this.$refs.userMenu.contains(event.target)) {
        this.closeUserDropdown()
      }
    },
    
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type }
      setTimeout(() => {
        this.toast.show = false
      }, 3000)
    }
  },
  
  watch: {
    showQuickAdd(newVal) {
      if (newVal) {
        // Focus on title input when modal opens
        this.$nextTick(() => {
          if (this.$refs.quickTitleInput) {
            this.$refs.quickTitleInput.focus()
          }
        })
      }
    }
  }
}
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 70px;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  z-index: 1000;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
}

.navbar-brand {
  flex-shrink: 0;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: #1f2937;
  font-weight: 700;
  font-size: 1.25rem;
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.brand-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.desktop-menu {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  text-decoration: none;
  color: #6b7280;
  font-weight: 500;
  transition: all 0.2s ease;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
}

.nav-link:hover,
.nav-link.router-link-active {
  background: #f3f4f6;
  color: #667eea;
}

.quick-add-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white !important;
}

.quick-add-btn:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  color: white !important;
}

.user-menu {
  position: relative;
}

.user-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  background: none;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.user-button:hover {
  background: #f3f4f6;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.avatar-text {
  color: white;
  font-weight: 700;
  font-size: 1rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-width: 0;
}

.user-name {
  color: #1f2937;
  font-weight: 600;
  font-size: 0.875rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 120px;
}

.user-email {
  color: #6b7280;
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 120px;
}

.dropdown-icon {
  color: #6b7280;
  font-size: 0.75rem;
  transition: transform 0.2s ease;
}

.dropdown-icon.rotated {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  min-width: 200px;
  margin-top: 0.5rem;
  z-index: 1001;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: #374151;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background 0.2s ease;
}

.dropdown-item:hover {
  background: #f3f4f6;
}

.dropdown-item:first-child {
  border-radius: 8px 8px 0 0;
}

.dropdown-item:last-child {
  border-radius: 0 0 8px 8px;
}

.logout-item {
  color: #dc2626;
}

.logout-item:hover {
  background: #fee2e2;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.5rem 0;
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
}

.mobile-menu {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.mobile-nav-links {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  text-decoration: none;
  color: #374151;
  background: none;
  border: none;
  cursor: pointer;
  width: 100%;
  border-radius: 8px;
  margin-bottom: 0.5rem;
  transition: background 0.2s ease;
  font-size: 1rem;
}

.mobile-nav-link:hover,
.mobile-nav-link.router-link-active {
  background: #f3f4f6;
  color: #667eea;
}

.mobile-user-section {
  padding: 1rem;
}

.mobile-user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.mobile-logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
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
  z-index: 1100;
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
  box-sizing: border-box;
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

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
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
  top: 5rem;
  right: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  z-index: 1200;
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
.icon-tasks::before { content: '📋'; }
.icon-dashboard::before { content: '🏠'; }
.icon-list::before { content: '📝'; }
.icon-plus::before { content: '+'; }
.icon-user::before { content: '👤'; }
.icon-logout::before { content: '🚪'; }
.icon-menu::before { content: '☰'; }
.icon-x::before { content: '✕'; }

/* Responsive Design */
@media (max-width: 1024px) {
  .navbar-container {
    padding: 0 1rem;
  }
  
  .user-info {
    display: none;
  }
}

@media (max-width: 768px) {
  .desktop-menu {
    display: none;
  }
  
  .mobile-menu-btn {
    display: block;
  }
  
  .navbar-container {
    padding: 0 1rem;
  }
  
  .brand-text {
    display: none;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .navbar-container {
    padding: 0 0.75rem;
  }
  
  .brand-link {
    font-size: 1rem;
  }
  
  .brand-icon {
    width: 36px;
    height: 36px;
  }
}

/* Animation for mobile menu */
.mobile-menu {
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Scrollbar styling for modal */
.modal::-webkit-scrollbar {
  width: 6px;
}

.modal::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.modal::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.modal::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>