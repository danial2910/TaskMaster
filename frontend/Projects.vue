<template>
  <div class="projects-container">
    <!-- Header -->
    <header class="page-header">
      <div class="header-content">
        <div class="header-left">
          <nav class="breadcrumb">
            <router-link to="/workspaces" class="breadcrumb-link">Workspaces</router-link>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">{{ currentWorkspace?.name || 'Projects' }}</span>
          </nav>
          <h1>Projects</h1>
          <p>Manage and organize your projects within this workspace</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="primary-btn">
            <i class="fas fa-plus"></i>
            New Project
          </button>
        </div>
      </div>
    </header>

    <!-- Workspace Info Banner -->
    <div v-if="currentWorkspace" class="workspace-banner">
      <div class="workspace-info">
        <div class="workspace-color" :style="{ backgroundColor: currentWorkspace.color }"></div>
        <div>
          <h3>{{ currentWorkspace.name }}</h3>
          <p v-if="currentWorkspace.description">{{ currentWorkspace.description }}</p>
        </div>
      </div>
      <div class="workspace-stats">
        <div class="stat">
          <span class="stat-number">{{ projects.length }}</span>
          <span class="stat-label">Projects</span>
        </div>
        <div class="stat">
          <span class="stat-number">{{ totalTasks }}</span>
          <span class="stat-label">Total Tasks</span>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="projects-main">
      <!-- Loading State -->
      <div v-if="loading" class="loading-container">
        <div class="spinner"></div>
        <p>Loading projects...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="projects.length === 0" class="empty-state">
        <div class="empty-icon">📁</div>
        <h2>No Projects Yet</h2>
        <p>Create your first project to start organizing your tasks</p>
        <button @click="showCreateModal = true" class="primary-btn">
          <i class="fas fa-plus"></i>
          Create Project
        </button>
      </div>

      <!-- Projects Grid -->
      <div v-else class="projects-grid">
        <div 
          v-for="project in projects" 
          :key="project.id"
          class="project-card"
          :style="{ borderLeftColor: project.color }"
          @click="selectProject(project)"
        >
          <div class="project-header">
            <div class="project-info">
              <div class="project-color" :style="{ backgroundColor: project.color }"></div>
              <div>
                <h3>{{ project.name }}</h3>
                <p v-if="project.description">{{ project.description }}</p>
                <small class="workspace-name">{{ project.workspace_name }}</small>
              </div>
            </div>
            <div class="project-menu">
              <button 
                @click.stop="editProject(project)" 
                class="menu-btn"
                title="Edit Project"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button 
                @click.stop="deleteProject(project.id)" 
                class="menu-btn delete"
                title="Delete Project"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>

          <div class="project-progress">
            <div class="progress-header">
              <span class="progress-label">Progress</span>
              <span class="progress-percentage">{{ project.progress }}%</span>
            </div>
            <div class="progress-bar">
              <div 
                class="progress-fill" 
                :style="{ 
                  width: project.progress + '%',
                  backgroundColor: project.color 
                }"
              ></div>
            </div>
          </div>

          <div class="project-stats">
            <div class="stat">
              <div class="stat-icon">📋</div>
              <div class="stat-info">
                <span class="stat-number">{{ project.tasks_count || 0 }}</span>
                <span class="stat-label">Tasks</span>
              </div>
            </div>
            <div class="stat">
              <div class="stat-icon">✅</div>
              <div class="stat-info">
                <span class="stat-number">{{ Math.round((project.tasks_count || 0) * (project.progress / 100)) }}</span>
                <span class="stat-label">Completed</span>
              </div>
            </div>
          </div>

          <div class="project-footer">
            <small class="created-date">
              Created {{ formatDate(project.created_at) }}
            </small>
            <button class="view-tasks-btn">
              <i class="fas fa-tasks"></i>
              View Tasks
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Create/Edit Project Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModals">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Project' : 'Create New Project' }}</h3>
          <button @click="closeModals" class="close-btn">×</button>
        </div>
        
        <form @submit.prevent="showEditModal ? updateProject() : createProject()" class="project-form">
          <div class="form-group">
            <label>Project Name</label>
            <input 
              v-model="projectForm.name" 
              type="text" 
              required 
              class="form-control"
              placeholder="Enter project name"
              maxlength="255"
            >
          </div>

          <div class="form-group">
            <label>Description (Optional)</label>
            <textarea 
              v-model="projectForm.description" 
              class="form-control" 
              rows="3"
              placeholder="Describe what this project is about..."
              maxlength="500"
            ></textarea>
          </div>

          <div class="form-group" v-if="!showEditModal">
            <label>Workspace</label>
            <select v-model="projectForm.workspace_id" required class="form-control">
              <option value="">Select a workspace</option>
              <option 
                v-for="workspace in workspaces" 
                :key="workspace.id" 
                :value="workspace.id"
              >
                {{ workspace.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Color Theme</label>
            <div class="color-picker">
              <div 
                v-for="color in colorOptions" 
                :key="color"
                class="color-option"
                :class="{ active: projectForm.color === color }"
                :style="{ backgroundColor: color }"
                @click="projectForm.color = color"
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
  name: 'Projects',
  data() {
    return {
      projects: [],
      workspaces: [],
      currentWorkspace: null,
      loading: false,
      formLoading: false,
      message: '',
      messageType: '',
      showCreateModal: false,
      showEditModal: false,
      editingProject: null,
      projectForm: {
        name: '',
        description: '',
        workspace_id: '',
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
  computed: {
    totalTasks() {
      return this.projects.reduce((sum, project) => sum + (project.tasks_count || 0), 0);
    }
  },
  async mounted() {
    await this.loadData();
  },
  methods: {
    async loadData() {
      await Promise.all([
        this.fetchProjects(),
        this.fetchWorkspaces()
      ]);
      
      // If we have a workspace ID in the route, set it as current
      if (this.$route.params.workspaceId) {
        this.currentWorkspace = this.workspaces.find(w => w.id == this.$route.params.workspaceId);
        this.projectForm.workspace_id = this.$route.params.workspaceId;
      }
    },

    async fetchProjects() {
      this.loading = true;
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php/api/projects', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.projects = await response.json();
        } else {
          throw new Error('Failed to fetch projects');
        }
      } catch (error) {
        this.showMessage('Failed to load projects', 'error');
        console.error('Error fetching projects:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchWorkspaces() {
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php/api/workspaces', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.workspaces = await response.json();
        }
      } catch (error) {
        console.error('Error fetching workspaces:', error);
      }
    },

    async createProject() {
      this.formLoading = true;
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php/api/projects', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.projectForm)
        });

        if (response.ok) {
          const newProject = await response.json();
          this.projects.unshift(newProject);
          this.closeModals();
          this.showMessage('Project created successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to create project');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      } finally {
        this.formLoading = false;
      }
    },

    async updateProject() {
      this.formLoading = true;
      try {
        const response = await fetch(`http://localhost/TaskMaster/backend/api.php/api/projects/${this.editingProject.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({
            name: this.projectForm.name,
            description: this.projectForm.description,
            color: this.projectForm.color
          })
        });

        if (response.ok) {
          const updatedProject = await response.json();
          const index = this.projects.findIndex(p => p.id === this.editingProject.id);
          if (index !== -1) {
            this.projects.splice(index, 1, updatedProject);
          }
          this.closeModals();
          this.showMessage('Project updated successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to update project');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      } finally {
        this.formLoading = false;
      }
    },

    async deleteProject(projectId) {
      if (!confirm('Are you sure you want to delete this project? This will also delete all tasks within it.')) {
        return;
      }

      try {
        const response = await fetch(`http://localhost/TaskMaster/backend/api.php/api/projects/${projectId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.ok) {
          this.projects = this.projects.filter(p => p.id !== projectId);
          this.showMessage('Project deleted successfully', 'success');
        } else {
          const error = await response.json();
          throw new Error(error.error || 'Failed to delete project');
        }
      } catch (error) {
        this.showMessage(error.message, 'error');
      }
    },

    editProject(project) {
      this.editingProject = project;
      this.projectForm = {
        name: project.name,
        description: project.description || '',
        workspace_id: project.workspace_id,
        color: project.color || '#667eea'
      };
      this.showEditModal = true;
    },

    selectProject(project) {
      // Navigate to project tasks page
      this.$router.push(`/project/${project.id}/tasks`);
    },

    closeModals() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.editingProject = null;
      this.projectForm = {
        name: '',
        description: '',
        workspace_id: this.$route.params.workspaceId || '',
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
.projects-container {
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

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.breadcrumb-link {
  color: #667eea;
  text-decoration: none;
  transition: color 0.3s ease;
}

.breadcrumb-link:hover {
  color: #5a6fd8;
}

.breadcrumb-separator {
  margin: 0 0.5rem;
  color: #999;
}

.breadcrumb-current {
  color: #666;
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

.workspace-banner {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  margin: 0 2rem;
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.workspace-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.workspace-color {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  flex-shrink: 0;
}

.workspace-info h3 {
  margin: 0 0 0.25rem 0;
  color: #2c3e50;
  font-size: 1.2rem;
  font-weight: 600;
}

.workspace-info p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.workspace-stats {
  display: flex;
  gap: 2rem;
}

.stat {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

.stat-label {
  font-size: 0.8rem;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.projects-main {
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

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.project-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #667eea;
  transition: all 0.3s ease;
  cursor: pointer;
}

.project-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.project-info {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  flex: 1;
}

.project-color {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.project-info h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 1.3rem;
  font-weight: 600;
}

.project-info p {
  margin: 0 0 0.25rem 0;
  color: #666;
  font-size: 0.9rem;
  line-height: 1.4;
}

.workspace-name {
  color: #999;
  font-size: 0.8rem;
  font-style: italic;
}

.project-menu {
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.project-card:hover .project-menu {
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

.project-progress {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(102, 126, 234, 0.05);
  border-radius: 12px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-label {
  font-size: 0.9rem;
  color: #666;
  font-weight: 600;
}

.progress-percentage {
  font-size: 0.9rem;
  color: #2c3e50;
  font-weight: 700;
}

.progress-bar {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  height: 8px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.3s ease;
}

.project-stats {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.project-stats .stat {
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

.project-stats .stat-number {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  display: block;
}

.project-stats .stat-label {
  font-size: 0.8rem;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.project-footer {
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

.view-tasks-btn {
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

.view-tasks-btn:hover {
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

.project-form {
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
  
  .workspace-banner {
    margin: 0 1rem;
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .workspace-stats {
    justify-content: center;
  }
  
  .projects-main {
    padding: 1rem;
  }
  
  .projects-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .project-card {
    padding: 1.5rem;
  }
  
  .project-stats {
    flex-direction: column;
    gap: 1rem;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .project-form {
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
  
  .breadcrumb {
    font-size: 0.8rem;
  }
  
  .project-card {
    padding: 1rem;
  }
  
  .project-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .project-menu {
    opacity: 1;
    align-self: flex-end;
  }
  
  .workspace-stats {
    flex-direction: column;
    gap: 0.5rem;
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
  .project-card, .workspace-banner {
    background: rgba(44, 62, 80, 0.95);
    color: #ecf0f1;
  }
  
  .project-info h3, .workspace-info h3 {
    color: #ecf0f1;
  }
  
  .project-info p, .workspace-info p {
    color: #bdc3c7;
  }
  
  .stat-number {
    color: #ecf0f1;
  }
  
  .stat-label, .progress-label {
    color: #bdc3c7;
  }
  
  .progress-percentage {
    color: #ecf0f1;
  }
  
  .created-date, .workspace-name {
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