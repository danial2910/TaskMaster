<template>
  <div class="profile-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1>Profile Settings</h1>
          <p>Manage your account information and preferences</p>
        </div>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-container">
      <div class="profile-grid">
        <!-- User Information Card -->
        <div class="profile-card">
          <div class="card-header">
            <h2>User Information</h2>
            <button @click="toggleEditProfile" class="btn-secondary">
              <i :class="editingProfile ? 'icon-x' : 'icon-edit'"></i>
              {{ editingProfile ? 'Cancel' : 'Edit' }}
            </button>
          </div>
          
          <div class="card-content">
            <!-- Avatar Section -->
            <div class="avatar-section">
              <div class="avatar">
                <span class="avatar-text">{{ getInitials(user?.username) }}</span>
              </div>
              <div class="avatar-info">
                <h3>{{ user?.username }}</h3>
                <p>{{ user?.email }}</p>
              </div>
            </div>

            <!-- Profile Form -->
            <form @submit.prevent="updateProfile" class="profile-form">
              <div class="form-group">
                <label for="username">Username</label>
                <input
                  type="text"
                  id="username"
                  v-model="profileForm.username"
                  :readonly="!editingProfile"
                  :class="{ 'readonly': !editingProfile }"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  v-model="profileForm.email"
                  :readonly="!editingProfile"
                  :class="{ 'readonly': !editingProfile }"
                  required
                />
              </div>
              
              <div v-if="editingProfile" class="form-actions">
                <button type="submit" class="btn-primary" :disabled="savingProfile">
                  <i class="icon-save"></i>
                  {{ savingProfile ? 'Saving...' : 'Save Changes' }}
                </button>
                <button type="button" @click="cancelEditProfile" class="btn-secondary">
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Change Password Card -->
        <div class="profile-card">
          <div class="card-header">
            <h2>Change Password</h2>
          </div>
          
          <div class="card-content">
            <form @submit.prevent="changePassword" class="password-form">
              <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input
                  type="password"
                  id="currentPassword"
                  v-model="passwordForm.currentPassword"
                  placeholder="Enter current password"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="newPassword">New Password</label>
                <input
                  type="password"
                  id="newPassword"
                  v-model="passwordForm.newPassword"
                  placeholder="Enter new password"
                  required
                  minlength="6"
                />
                <small class="form-help">Password must be at least 6 characters long</small>
              </div>
              
              <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input
                  type="password"
                  id="confirmPassword"
                  v-model="passwordForm.confirmPassword"
                  placeholder="Confirm new password"
                  required
                />
                <span v-if="passwordMismatch" class="error-text">Passwords do not match</span>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn-primary" :disabled="savingPassword || passwordMismatch">
                  <i class="icon-lock"></i>
                  {{ savingPassword ? 'Changing...' : 'Change Password' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Account Statistics -->
        <div class="profile-card">
          <div class="card-header">
            <h2>Account Statistics</h2>
          </div>
          
          <div class="card-content">
            <div v-if="loadingStats" class="loading-stats">
              <div class="spinner"></div>
              <p>Loading statistics...</p>
            </div>
            
            <div v-else class="stats-grid">
              <div class="stat-item">
                <div class="stat-icon tasks">
                  <i class="icon-list"></i>
                </div>
                <div class="stat-content">
                  <h4>Total Tasks</h4>
                  <span class="stat-number">{{ stats.totalTasks }}</span>
                </div>
              </div>
              
              <div class="stat-item">
                <div class="stat-icon completed">
                  <i class="icon-check"></i>
                </div>
                <div class="stat-content">
                  <h4>Completed</h4>
                  <span class="stat-number">{{ stats.completedTasks }}</span>
                </div>
              </div>
              
              <div class="stat-item">
                <div class="stat-icon pending">
                  <i class="icon-clock"></i>
                </div>
                <div class="stat-content">
                  <h4>Pending</h4>
                  <span class="stat-number">{{ stats.pendingTasks }}</span>
                </div>
              </div>
              
              <div class="stat-item">
                <div class="stat-icon joined">
                  <i class="icon-calendar"></i>
                </div>
                <div class="stat-content">
                  <h4>Member Since</h4>
                  <span class="stat-date">{{ formatJoinDate(user?.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Actions -->
        <div class="profile-card danger-card">
          <div class="card-header">
            <h2>Danger Zone</h2>
          </div>
          
          <div class="card-content">
            <div class="danger-section">
              <div class="danger-info">
                <h4>Delete Account</h4>
                <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
              </div>
              <button @click="showDeleteModal = true" class="btn-danger">
                <i class="icon-trash"></i>
                Delete Account
              </button>
            </div>
            
            <div class="danger-section">
              <div class="danger-info">
                <h4>Logout from All Devices</h4>
                <p>Sign out from all devices and invalidate all active sessions.</p>
              </div>
              <button @click="logoutAllDevices" class="btn-warning">
                <i class="icon-logout"></i>
                Logout All
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Delete Account</h2>
          <button @click="closeDeleteModal" class="close-btn">
            <i class="icon-x"></i>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="warning-icon">
            <i class="icon-warning"></i>
          </div>
          
          <h3>Are you absolutely sure?</h3>
          <p>This action <strong>cannot be undone</strong>. This will permanently delete your account and remove all your data from our servers.</p>
          
          <div class="confirmation-section">
            <label for="confirmDelete">Please type your username to confirm:</label>
            <input
              type="text"
              id="confirmDelete"
              v-model="deleteConfirmation"
              :placeholder="user?.username"
              class="confirmation-input"
            />
          </div>
          
          <div class="modal-actions">
            <button @click="closeDeleteModal" class="btn-secondary">
              Cancel
            </button>
            <button 
              @click="deleteAccount" 
              class="btn-danger"
              :disabled="deleteConfirmation !== user?.username || deleting"
            >
              {{ deleting ? 'Deleting...' : 'Delete Account' }}
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
  name: 'Profile',
  data() {
    return {
      editingProfile: false,
      savingProfile: false,
      savingPassword: false,
      loadingStats: true,
      deleting: false,
      showDeleteModal: false,
      deleteConfirmation: '',
      profileForm: {
        username: '',
        email: ''
      },
      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      stats: {
        totalTasks: 0,
        completedTasks: 0,
        pendingTasks: 0
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
    
    passwordMismatch() {
      return this.passwordForm.newPassword && 
             this.passwordForm.confirmPassword && 
             this.passwordForm.newPassword !== this.passwordForm.confirmPassword
    }
  },
  
  async mounted() {
    this.initializeProfile()
    await this.fetchStats()
  },
  
  methods: {
    initializeProfile() {
      if (this.user) {
        this.profileForm = {
          username: this.user.username,
          email: this.user.email
        }
      }
    },
    
    async fetchStats() {
      this.loadingStats = true
      try {
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/tasks', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          const tasks = await response.json()
          this.stats = {
            totalTasks: tasks.length,
            completedTasks: tasks.filter(task => task.status === 'completed').length,
            pendingTasks: tasks.filter(task => task.status === 'pending').length
          }
        }
      } catch (error) {
        console.error('Error fetching stats:', error)
      } finally {
        this.loadingStats = false
      }
    },
    
    async updateProfile() {
      this.savingProfile = true
      
      try {
        // Note: This endpoint would need to be implemented in your backend
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/user/profile', {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.profileForm)
        })
        
        if (response.ok) {
          const updatedUser = await response.json()
          
          // Update localStorage
          localStorage.setItem('user', JSON.stringify(updatedUser))
          
          this.editingProfile = false
          this.showToast('Profile updated successfully!', 'success')
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to update profile', 'error')
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.savingProfile = false
      }
    },
    
    async changePassword() {
      if (this.passwordMismatch) return
      
      this.savingPassword = true
      
      try {
        // Note: This endpoint would need to be implemented in your backend
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/user/change-password', {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            currentPassword: this.passwordForm.currentPassword,
            newPassword: this.passwordForm.newPassword
          })
        })
        
        if (response.ok) {
          this.passwordForm = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
          }
          this.showToast('Password changed successfully!', 'success')
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to change password', 'error')
        }
      } catch (error) {
        console.error('Error changing password:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.savingPassword = false
      }
    },
    
    async deleteAccount() {
      this.deleting = true
      
      try {
        // Note: This endpoint would need to be implemented in your backend
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/user/account', {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          // Clear all user data
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          
          this.showToast('Account deleted successfully', 'success')
          
          setTimeout(() => {
            this.$router.push('/login')
          }, 2000)
        } else {
          const error = await response.json()
          this.showToast(error.error || 'Failed to delete account', 'error')
        }
      } catch (error) {
        console.error('Error deleting account:', error)
        this.showToast('Network error. Please try again.', 'error')
      } finally {
        this.deleting = false
        this.closeDeleteModal()
      }
    },
    
    logoutAllDevices() {
      // Clear local storage
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      this.showToast('Logged out from all devices', 'success')
      
      setTimeout(() => {
        this.$router.push('/login')
      }, 1500)
    },
    
    toggleEditProfile() {
      if (this.editingProfile) {
        this.cancelEditProfile()
      } else {
        this.editingProfile = true
      }
    },
    
    cancelEditProfile() {
      this.editingProfile = false
      this.initializeProfile() // Reset form to original values
    },
    
    closeDeleteModal() {
      this.showDeleteModal = false
      this.deleteConfirmation = ''
    },
    
    getInitials(name) {
      if (!name) return '?'
      return name.charAt(0).toUpperCase()
    },
    
    formatJoinDate(dateString) {
      if (!dateString) return 'Unknown'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long'
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
.profile-page {
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

.profile-container {
  max-width: 1200px;
  margin: 0 auto;
}

.profile-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
}

.profile-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.danger-card {
  border: 1px solid #fecaca;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.card-header h2 {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.card-content {
  padding: 1.5rem;
}

.avatar-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.avatar-text {
  color: white;
  font-size: 1.5rem;
  font-weight: 700;
}

.avatar-info h3 {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 0.25rem 0;
}

.avatar-info p {
  color: #6b7280;
  margin: 0;
}

.profile-form, .password-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  color: #374151;
  font-weight: 500;
  font-size: 0.875rem;
}

.form-group input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input.readonly {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}

.form-help {
  color: #6b7280;
  font-size: 0.75rem;
}

.error-text {
  color: #ef4444;
  font-size: 0.75rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
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

.btn-danger:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.btn-warning {
  background: #f59e0b;
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

.btn-warning:hover {
  background: #d97706;
}

.loading-stats {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
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
  flex-shrink: 0;
}

.stat-icon.tasks {
  background: #6366f1;
}

.stat-icon.completed {
  background: #10b981;
}

.stat-icon.pending {
  background: #f59e0b;
}

.stat-icon.joined {
  background: #8b5cf6;
}

.stat-content h4 {
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  margin: 0 0 0.25rem 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-number {
  color: #1f2937;
  font-size: 2rem;
  font-weight: 700;
}

.stat-date {
  color: #1f2937;
  font-size: 1rem;
  font-weight: 600;
}

.danger-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border: 1px solid #fee2e2;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.danger-section:last-child {
  margin-bottom: 0;
}

.danger-info h4 {
  color: #dc2626;
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 0.25rem 0;
}

.danger-info p {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
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
  text-align: center;
}

.warning-icon {
  margin-bottom: 1rem;
}

.warning-icon i {
  font-size: 3rem;
  color: #dc2626;
}

.modal-body h3 {
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.modal-body p {
  color: #6b7280;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.confirmation-section {
  text-align: left;
  margin-bottom: 1.5rem;
}

.confirmation-section label {
  display: block;
  color: #374151;
  font-weight: 500;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.confirmation-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  box-sizing: border-box;
}

.confirmation-input:focus {
  outline: none;
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
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
.icon-edit::before { content: '✏️'; }
.icon-x::before { content: '✕'; }
.icon-save::before { content: '💾'; }
.icon-lock::before { content: '🔒'; }
.icon-list::before { content: '📋'; }
.icon-check::before { content: '✅'; }
.icon-clock::before { content: '⏰'; }
.icon-calendar::before { content: '📅'; }
.icon-trash::before { content: '🗑️'; }
.icon-logout::before { content: '🚪'; }
.icon-warning::before { content: '⚠️'; }

/* Responsive Design */
@media (max-width: 1024px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .profile-page {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .profile-grid {
    grid-template-columns: 1fr;
  }
  
  .avatar-section {
    flex-direction: column;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .danger-section {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .modal {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}

@media (max-width: 480px) {
  .card-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}</style>