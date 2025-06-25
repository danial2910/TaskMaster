<template>
  <div class="workspaces-container">
    <!-- Header -->
    <header class="page-header">
      <div class="header-content">
        <div class="header-left">
          <h1>My Workspaces</h1>
          <p>Organize your projects and tasks in dedicated workspaces</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="primary-btn">
            <i class="fas fa-plus"></i>
            New Workspace
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="workspaces-main">
      <!-- Loading State -->
      <div v-if="loading" class="loading-container">
        <div class="spinner"></div>
        <p>Loading workspaces...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="workspaces.length === 0" class="empty-state">
        <div class="empty-icon">🏢</div>
        <h2>No Workspaces Yet</h2>
        <p>Create your first workspace to start organizing your projects and tasks</p>
        <button @click="showCreateModal = true" class="primary-btn">
          <i class="fas fa-plus"></i>
          Create Workspace
        </button>
      </div>

      <!-- Workspaces Grid -->
      <div v-else class="workspaces-grid">
        <div 
          v-for="workspace in workspaces" 
          :key="workspace.id"
          class="workspace-card"
          :style="{ borderLeftColor: workspace.color }"
          @click="selectWorkspace(workspace)"
        >
          <div class="workspace-header">
            <div class="workspace-info">
              <div 
                class="workspace-color" 
                :style="{ backgroundColor: workspace.color }"
              ></div>
              <div>
                <h3>{{ workspace.name }}</h3>
                <p v-if="workspace.description">{{ workspace.description }}</p>
              </div>
            </div>
            <div class="workspace-menu">
              <button 
                @click.stop="editWorkspace(workspace)" 
                class="menu-btn"
                title="Edit Workspace"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button 
                @click.stop="deleteWorkspace(workspace.id)" 
                class="menu-btn delete"
                title="Delete Workspace"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>

          <div class="workspace-stats">
            <div class="stat">
              <div class="stat-icon">📁</div>
              <div class="stat-info">
                <span class="stat-number">{{ workspace.projects_count || 0 }}</span>
                <span class="stat-label">Projects</span>
              </div>
            </div>
            <div class="stat">
              <div class="stat-icon">📋</div>
              <div class="stat-info">
                <span class="stat-number">{{ workspace.tasks_count || 0 }}</span>
                <span class="stat-label">Tasks</span>
              </div>
            </div>
          </div>

          <div class="workspace-footer">
            <small class="created-date">
              Created {{ formatDate(workspace.created_at) }}
            </small>
            <button class="enter-btn">
              <i class="fas fa-arrow-right"></i>
              Enter
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Create/Edit Workspace Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModals">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Workspace' : 'Create New Workspace' }}</h3>
          <button @click="closeModals" class="close-btn">×</button>
        </div>
        
        <form @submit.prevent="showEditModal ? updateWorkspace() : createWorkspace()" class="workspace-form">
          <div class="form-group">
            <label>Workspace Name</label>
            <input 
              v-model="workspaceForm.name" 
              type="text" 
              required 
              class="form-control"
              placeholder="Enter workspace name"
              maxlength="255"
            >
          </div>

          <div class="form-group">
            <label>Description (Optional)</label>
            <textarea 
              v-model="workspaceForm.description" 
              class="form-control" 
              rows="3"
              placeholder="Describe what this workspace is for..."
              maxlength="500"
            ></textarea>
          </div>

          <div class="form-group">
            <label>Color Theme</label>
            <div class="color-picker">
              <div 
                v-for="color in colorOptions" 
                :key="color"
                class="color-option"
                :class="{ active: workspaceForm.color === color }"
                :style="{ backgroundColor: color }"
                @click="workspaceForm.color = color"
              ></div>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModals" class="secondary-btn">
              Cancel
            </button>
            <button type="submit" class="primary-btn" :disabled="formLoading">
              {{ formLoading ? 'Saving...' : (showEditModal ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast Messages -->
    <div v-if="message" :class="['toast', messageType]">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'Workspaces',
  data() {
    return {
      workspaces: [],
      loading: false,
      formLoading: false,
      message: '',
      messageType: '',
      showCreateModal: false,
      showEditModal: false,
      editingWorkspace: null,
      workspaceForm: {
        name: '',
        description: '',
        color: '#667eea'
      },
      colorOptions: [
        '#667eea', '#764ba2', '#f093fb', '#f5576c',
        '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
        '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3',
        '#ff9a9e', '#fecfef', '#ffeef3', '#667292'
      ]
    }
  },
  async mounted() {
    await this.fetchWorkspaces();
  },
  methods: {
    async fetchWorkspaces() {
      this.loading = true;
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php/api/workspaces', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.workspaces = await response.json();
        } else {
          throw new Error('Failed to fetch workspaces');
        }
      } catch (error) {
        this.showMessage('Failed to load workspaces', 'error');
        console.error('Error fetching workspaces:', error);
      } finally {
        this.loading = false;
      }
    },

    async createWorkspace() {
      this.formLoading = true;
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php/api/workspaces', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.workspaceForm)
        });

        if (response.ok) {
          const newWorkspace = await response.json();
          this.workspaces.unshift(newWorkspace);
          this.closeModals();
          this.showMessage('Workspace created successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to create workspace');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      } finally {
        this.formLoading = false;
      }
    },

    async updateWorkspace() {
      this.formLoading = true;
      try {
        const response = await fetch(`http://localhost/TaskMaster/backend/api.php/api/workspaces/${this.editingWorkspace.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.workspaceForm)
        });

        if (response.ok) {
          const updatedWorkspace = await response.json();
          const index = this.workspaces.findIndex(w => w.id === this.editingWorkspace.id);
          if (index !== -1) {
            this.workspaces.splice(index, 1, updatedWorkspace);
          }
          this.closeModals();
          this.showMessage('Workspace updated successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to update workspace');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      } finally {
        this.formLoading = false;
      }
    },

    async deleteWorkspace(workspaceId) {
      if (!confirm('Are you sure you want to delete this workspace? This will also delete all projects and tasks within it.')) {
        return;
      }

      try {
        const response = await fetch(`http://localhost/TaskMaster/backend/api.php/api/workspaces/${workspaceId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.ok) {
          this.workspaces = this.workspaces.filter(w => w.id !== workspaceId);
          this.showMessage('Workspace deleted successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to delete workspace');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      }
    },

    editWorkspace(workspace) {
      this.editingWorkspace = workspace;
      this.workspaceForm = {
        name: workspace.name,
        description: workspace.description || '',
        color: workspace.color || '#667eea'
      };
      this.showEditModal = true;
    },

    selectWorkspace(workspace) {
      // Navigate to workspace detail page or projects page
      this.$router.push(`/workspace/${workspace.id}`);
    },

    closeModals() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.editingWorkspace = null;
      this.workspaceForm = {
        name: '',
        description: '',
        color: '#667eea'
      };
    },

    formatDate(dateString) {
      if (!dateString) return 'Unknown';
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    showMessage(text, type) {
      this.message = text;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
      }, 3000);
    }
  }
}
</script>

<style scoped>
.workspaces-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.page-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 2rem;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left h1 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 2.5rem;
  font-weight: 700;
}

.header-left p {
  margin: 0;
  color: #666;
  font-size: 1.1rem;
}

.primary-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.workspaces-main {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  color: white;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: white;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h2 {
  margin: 0 0 1rem 0;
  font-size: 2rem;
  font-weight: 600;
}

.empty-state p {
  margin: 0 0 2rem 0;
  font-size: 1.1rem;
  opacity: 0.9;
}

.workspaces-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.workspace-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #667eea;
  transition: all 0.3s ease;
  cursor: pointer;
}

.workspace-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.workspace-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.workspace-info {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  flex: 1;
}

.workspace-color {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.workspace-info h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 1.3rem;
  font-weight: 600;
}

.workspace-info p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
  line-height: 1.4;
}

.workspace-menu {
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.workspace-card:hover .workspace-menu {
  opacity: 1;
}

.menu-btn {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.menu-btn:hover {
  background: rgba(102, 126, 234, 0.2);
  transform: scale(1.1);
}

.menu-btn.delete {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c;
}

.menu-btn.delete:hover {
  background: rgba(231, 76, 60, 0.2);
}

.workspace-stats {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.stat {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: rgba(102, 126, 234, 0.05);
  border-radius: 12px;
  flex: 1;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
}

.stat-label {
  font-size: 0.8rem;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.workspace-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.created-date {
  color: #999;
  font-size: 0.8rem;
}

.enter-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.enter-btn:hover {
  transform: translateX(3px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
  backdrop-filter: blur(5px);
}

.modal-content {
  background: white;
  border-radius: 20px;
  padding: 0;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 2rem;
  border-radius: 20px 20px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 1.5rem;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.workspace-form {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2c3e50;
  font-weight: 600;
  font-size: 0.95rem;
}

.form-control {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e9ecef;
  border-radius: 12px;
  font-size: 1rem;
  box-sizing: border-box;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.8);
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  background: white;
}

.color-picker {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.color-option {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  border: 3px solid transparent;
  transition: all 0.3s ease;
}

.color-option:hover {
  transform: scale(1.1);
}

.color-option.active {
  border-color: white;
  box-shadow: 0 0 0 2px #2c3e50;
  transform: scale(1.15);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

.secondary-btn {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.secondary-btn:hover {
  background: #5a6268;
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Toast Messages */
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  color: white;
  font-weight: 600;
  z-index: 3000;
  animation: toastSlideIn 0.3s ease-out;
  max-width: 300px;
}

@keyframes toastSlideIn {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.toast.success {
  background: #2ecc71;
}

.toast.error {
  background: #e74c3c;
}

.toast.warning {
  background: #f39c12;
}

.toast.info {
  background: #3498db;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-header {
    padding: 1.5rem 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .header-left h1 {
    font-size: 2rem;
  }
  
  .workspaces-main {
    padding: 1rem;
  }
  
  .workspaces-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .workspace-card {
    padding: 1.5rem;
  }
  
  .workspace-stats {
    flex-direction: column;
    gap: 1rem;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .workspace-form {
    padding: 1.5rem;
  }
  
  .color-picker {
    grid-template-columns: repeat(6, 1fr);
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .header-left h1 {
    font-size: 1.7rem;
  }
  
  .header-left p {
    font-size: 1rem;
  }
  
  .workspace-card {
    padding: 1rem;
  }
  
  .workspace-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .workspace-menu {
    opacity: 1;
    align-self: flex-end;
  }
  
  .color-picker {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .color-option {
    width: 28px;
    height: 28px;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .workspace-card {
    background: rgba(44, 62, 80, 0.95);
    color: #ecf0f1;
  }
  
  .workspace-info h3 {
    color: #ecf0f1;
  }
  
  .workspace-info p {
    color: #bdc3c7;
  }
  
  .stat-number {
    color: #ecf0f1;
  }
  
  .stat-label {
    color: #bdc3c7;
  }
  
  .created-date {
    color: #95a5a6;
  }
  
  .modal-content {
    background: #2c3e50;
    color: #ecf0f1;
  }
  
  .form-control {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #ecf0f1;
  }
  
  .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #667eea;
  }
}</style>