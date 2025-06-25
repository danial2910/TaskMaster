<template>
  <div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="header-content">
        <h1>TaskMaster Dashboard</h1>
        <div class="user-info">
          <span>Welcome, {{ user.username }}!</span>
          <button @click="logout" class="logout-btn">Logout</button>
        </div>
      </div>
    </header>

    <!-- Navigation Tabs -->
    <nav class="dashboard-nav">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="['nav-tab', { active: activeTab === tab.id }]"
      >
        <i :class="tab.icon"></i>
        {{ tab.name }}
        <span v-if="tab.count !== undefined" class="count-badge">{{ tab.count }}</span>
      </button>
    </nav>

    <!-- Main Content -->
    <main class="dashboard-main">
      <!-- Overview Tab -->
      <div v-if="activeTab === 'overview'" class="tab-content">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
              <h3>{{ totalTasks }}</h3>
              <p>Total Tasks</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
              <h3>{{ completedTasks }}</h3>
              <p>Completed</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
              <h3>{{ pendingTasks }}</h3>
              <p>Pending</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📁</div>
            <div class="stat-info">
              <h3>{{ totalProjects }}</h3>
              <p>Projects</p>
            </div>
          </div>
        </div>

        <!-- Recent Tasks -->
        <div class="recent-section">
          <h2>Recent Tasks</h2>
          <div class="task-list">
            <div 
              v-for="task in recentTasks" 
              :key="task.id"
              class="task-item"
              :class="{ completed: task.status === 'completed' }"
            >
              <div class="task-info">
                <h4>{{ task.title }}</h4>
                <p>{{ task.description }}</p>
                <small>Due: {{ formatDate(task.due_date) }}</small>
              </div>
              <div class="task-actions">
                <span :class="['status-badge', task.status]">{{ task.status }}</span>
                <button @click="toggleTaskStatus(task)" class="action-btn">
                  {{ task.status === 'completed' ? '↶' : '✓' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tasks Tab -->
      <div v-if="activeTab === 'tasks'" class="tab-content">
        <div class="section-header">
          <h2>My Tasks</h2>
          <button @click="showCreateTask = true" class="primary-btn">
            <i class="fas fa-plus"></i> New Task
          </button>
        </div>

        <!-- Task Filters -->
        <div class="filters">
          <select v-model="taskFilter" @change="filterTasks">
            <option value="all">All Tasks</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
          <select v-model="projectFilter" @change="filterTasks">
            <option value="">All Projects</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>

        <!-- Tasks List -->
        <div class="tasks-grid">
          <div 
            v-for="task in filteredTasks" 
            :key="task.id"
            class="task-card"
            :class="{ completed: task.status === 'completed' }"
          >
            <div class="task-header">
              <h3>{{ task.title }}</h3>
              <div class="task-menu">
                <button @click="editTask(task)" class="edit-btn">✏️</button>
                <button @click="deleteTask(task.id)" class="delete-btn">🗑️</button>
              </div>
            </div>
            <p class="task-description">{{ task.description }}</p>
            <div class="task-meta">
              <span class="priority" :class="task.priority">{{ task.priority }}</span>
              <span class="due-date">Due: {{ formatDate(task.due_date) }}</span>
            </div>
            <div class="task-footer">
              <select 
                v-model="task.status" 
                @change="updateTaskStatus(task)"
                class="status-select"
              >
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
              <button @click="viewTaskComments(task)" class="comments-btn">
                💬 {{ task.comments_count || 0 }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Projects Tab -->
      <div v-if="activeTab === 'projects'" class="tab-content">
        <div class="section-header">
          <h2>My Projects</h2>
          <button @click="showCreateProject = true" class="primary-btn">
            <i class="fas fa-plus"></i> New Project
          </button>
        </div>

        <div class="projects-grid">
          <div v-for="project in projects" :key="project.id" class="project-card">
            <div class="project-header">
              <h3>{{ project.name }}</h3>
              <div class="project-menu">
                <button @click="editProject(project)" class="edit-btn">✏️</button>
                <button @click="deleteProject(project.id)" class="delete-btn">🗑️</button>
              </div>
            </div>
            <p class="project-description">{{ project.description }}</p>
            <div class="project-stats">
              <div class="stat">
                <span class="stat-label">Tasks:</span>
                <span class="stat-value">{{ project.tasks_count || 0 }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Progress:</span>
                <div class="progress-bar">
                  <div 
                    class="progress-fill" 
                    :style="{ width: project.progress + '%' }"
                  ></div>
                </div>
                <span class="stat-value">{{ project.progress }}%</span>
              </div>
            </div>
            <div class="project-footer">
              <span class="created-date">Created: {{ formatDate(project.created_at) }}</span>
              <button @click="viewProjectTasks(project)" class="view-tasks-btn">
                View Tasks
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Comments Tab -->
      <div v-if="activeTab === 'comments'" class="tab-content">
        <h2>Recent Comments</h2>
        <div class="comments-list">
          <div v-for="comment in comments" :key="comment.id" class="comment-item">
            <div class="comment-header">
              <strong>{{ comment.task_title }}</strong>
              <small>{{ formatDate(comment.created_at) }}</small>
            </div>
            <p class="comment-text">{{ comment.content }}</p>
            <div class="comment-actions">
              <button @click="editComment(comment)" class="edit-btn">Edit</button>
              <button @click="deleteComment(comment.id)" class="delete-btn">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Create Task Modal -->
    <div v-if="showCreateTask" class="modal-overlay" @click="showCreateTask = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Create New Task</h3>
          <button @click="showCreateTask = false" class="close-btn">×</button>
        </div>
        <form @submit.prevent="createTask" class="task-form">
          <div class="form-group">
            <label>Title:</label>
            <input v-model="newTask.title" type="text" required class="form-control">
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="newTask.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Priority:</label>
              <select v-model="newTask.priority" class="form-control">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            <div class="form-group">
              <label>Project:</label>
              <select v-model="newTask.project_id" class="form-control">
                <option value="">No Project</option>
                <option v-for="project in projects" :key="project.id" :value="project.id">
                  {{ project.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Due Date:</label>
            <input v-model="newTask.due_date" type="datetime-local" class="form-control">
          </div>
          <div class="modal-actions">
            <button type="button" @click="showCreateTask = false" class="secondary-btn">Cancel</button>
            <button type="submit" class="primary-btn" :disabled="loading">
              {{ loading ? 'Creating...' : 'Create Task' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Create Project Modal -->
    <div v-if="showCreateProject" class="modal-overlay" @click="showCreateProject = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Create New Project</h3>
          <button @click="showCreateProject = false" class="close-btn">×</button>
        </div>
        <form @submit.prevent="createProject" class="project-form">
          <div class="form-group">
            <label>Project Name:</label>
            <input v-model="newProject.name" type="text" required class="form-control">
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="newProject.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="modal-actions">
            <button type="button" @click="showCreateProject = false" class="secondary-btn">Cancel</button>
            <button type="submit" class="primary-btn" :disabled="loading">
              {{ loading ? 'Creating...' : 'Create Project' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner"></div>
    </div>

    <!-- Toast Messages -->
    <div v-if="message" :class="['toast', messageType]">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user') || '{}'),
      activeTab: 'overview',
      loading: false,
      message: '',
      messageType: '',
      
      // Data
      tasks: [],
      projects: [],
      comments: [],
      
      // Filters
      taskFilter: 'all',
      projectFilter: '',
      
      // Modals
      showCreateTask: false,
      showCreateProject: false,
      
      // Forms
      newTask: {
        title: '',
        description: '',
        priority: 'medium',
        project_id: '',
        due_date: ''
      },
      newProject: {
        name: '',
        description: ''
      },
      
      // Tabs configuration
      tabs: [
        { id: 'overview', name: 'Overview', icon: 'fas fa-chart-dashboard' },
        { id: 'tasks', name: 'Tasks', icon: 'fas fa-tasks', count: 0 },
        { id: 'projects', name: 'Projects', icon: 'fas fa-folder', count: 0 },
        { id: 'comments', name: 'Comments', icon: 'fas fa-comments', count: 0 }
      ]
    }
  },
  computed: {
    totalTasks() {
      return this.tasks.length;
    },
    completedTasks() {
      return this.tasks.filter(task => task.status === 'completed').length;
    },
    pendingTasks() {
      return this.tasks.filter(task => task.status === 'pending').length;
    },
    totalProjects() {
      return this.projects.length;
    },
    recentTasks() {
      return this.tasks
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 5);
    },
    filteredTasks() {
      let filtered = this.tasks;
      
      if (this.taskFilter !== 'all') {
        filtered = filtered.filter(task => task.status === this.taskFilter);
      }
      
      if (this.projectFilter) {
        filtered = filtered.filter(task => task.project_id == this.projectFilter);
      }
      
      return filtered;
    }
  },
  async mounted() {
    // Check if user is logged in
    if (!this.user.username) {
      this.$router.push('/login');
      return;
    }
    
    await this.loadDashboardData();
  },
  methods: {
    async loadDashboardData() {
      this.loading = true;
      try {
        await Promise.all([
          this.fetchTasks(),
          this.fetchProjects(),
          this.fetchComments()
        ]);
        this.updateTabCounts();
      } catch (error) {
        this.showMessage('Failed to load dashboard data', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchTasks() {
      try {
        const response = await fetch('/api/tasks', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.tasks = await response.json();
        }
      } catch (error) {
        console.error('Error fetching tasks:', error);
      }
    },
    
    async fetchProjects() {
      try {
        const response = await fetch('/api/projects', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.projects = await response.json();
        }
      } catch (error) {
        console.error('Error fetching projects:', error);
      }
    },
    
    async fetchComments() {
      try {
        const response = await fetch('/api/comments', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.comments = await response.json();
        }
      } catch (error) {
        console.error('Error fetching comments:', error);
      }
    },
    
    async createTask() {
      this.loading = true;
      try {
        const response = await fetch('/api/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.newTask)
        });
        
        if (response.ok) {
          const newTask = await response.json();
          this.tasks.push(newTask);
          this.showCreateTask = false;
          this.resetNewTask();
          this.showMessage('Task created successfully', 'success');
          this.updateTabCounts();
        } else {
          this.showMessage('Failed to create task', 'error');
        }
      } catch (error) {
        this.showMessage('Error creating task', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async createProject() {
      this.loading = true;
      try {
        const response = await fetch('/api/projects', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.newProject)
        });
        
        if (response.ok) {
          const newProject = await response.json();
          this.projects.push(newProject);
          this.showCreateProject = false;
          this.resetNewProject();
          this.showMessage('Project created successfully', 'success');
          this.updateTabCounts();
        } else {
          this.showMessage('Failed to create project', 'error');
        }
      } catch (error) {
        this.showMessage('Error creating project', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async updateTaskStatus(task) {
      try {
        const response = await fetch(`/api/tasks/${task.id}/status`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({ status: task.status })
        });
        
        if (response.ok) {
          this.showMessage('Task status updated', 'success');
        } else {
          this.showMessage('Failed to update task status', 'error');
        }
      } catch (error) {
        this.showMessage('Error updating task status', 'error');
      }
    },
    
    async deleteTask(taskId) {
      if (!confirm('Are you sure you want to delete this task?')) return;
      
      try {
        const response = await fetch(`/api/tasks/${taskId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.tasks = this.tasks.filter(task => task.id !== taskId);
          this.showMessage('Task deleted successfully', 'success');
          this.updateTabCounts();
        } else {
          this.showMessage('Failed to delete task', 'error');
        }
      } catch (error) {
        this.showMessage('Error deleting task', 'error');
      }
    },
    
    async deleteProject(projectId) {
      if (!confirm('Are you sure you want to delete this project?')) return;
      
      try {
        const response = await fetch(`/api/projects/${projectId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.ok) {
          this.projects = this.projects.filter(project => project.id !== projectId);
          this.showMessage('Project deleted successfully', 'success');
          this.updateTabCounts();
        } else {
          this.showMessage('Failed to delete project', 'error');
        }
      } catch (error) {
        this.showMessage('Error deleting project', 'error');
      }
    },
    
    filterTasks() {
      // Computed property handles this automatically
    },
    
    updateTabCounts() {
      this.tabs.find(tab => tab.id === 'tasks').count = this.tasks.length;
      this.tabs.find(tab => tab.id === 'projects').count = this.projects.length;
      this.tabs.find(tab => tab.id === 'comments').count = this.comments.length;
    },
    
    resetNewTask() {
      this.newTask = {
        title: '',
        description: '',
        priority: 'medium',
        project_id: '',
        due_date: ''
      };
    },
    
    resetNewProject() {
      this.newProject = {
        name: '',
        description: ''
      };
    },
    
    formatDate(dateString) {
      if (!dateString) return 'No date';
      return new Date(dateString).toLocaleDateString();
    },
    
    showMessage(text, type) {
      this.message = text;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
      }, 3000);
    },
    
    logout() {
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      this.$router.push('/login');
    },
    
    // Placeholder methods for future implementation
    editTask(task) {
      console.log('Edit task:', task);
    },
    
    editProject(project) {
      console.log('Edit project:', project);
    },
    
    viewTaskComments(task) {
      console.log('View comments for task:', task);
    },
    
    viewProjectTasks(project) {
      console.log('View tasks for project:', project);
    },
    
    editComment(comment) {
      console.log('Edit comment:', comment);
    },
    
    deleteComment(commentId) {
      console.log('Delete comment:', commentId);
    }
  }
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 1rem 2rem;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.dashboard-header h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 2rem;
  font-weight: 700;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logout-btn {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.logout-btn:hover {
  background: #c0392b;
}

.dashboard-nav {
  background: rgba(255, 255, 255, 0.1);
  padding: 1rem 2rem;
  display: flex;
  gap: 1rem;
  overflow-x: auto;
}

.nav-tab {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  color: white;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;
  position: relative;
}

.nav-tab.active {
  background: rgba(255, 255, 255, 0.9);
  color: #2c3e50;
  transform: translateY(-2px);
}

.count-badge {
  background: #e74c3c;
  color: white;
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
}

.dashboard-main {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.tab-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-info h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
}

.stat-info p {
  margin: 0.5rem 0 0 0;
  opacity: 0.9;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  margin: 0;
  color: #2c3e50;
}

.primary-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filters select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
}

.tasks-grid, .projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.task-card, .project-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #667eea;
  transition: all 0.3s;
}

.task-card:hover, .project-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.task-card.completed {
  opacity: 0.7;
  border-left-color: #2ecc71;
}

.task-header, .project-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.task-header h3, .project-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.2rem;
}

.task-menu, .project-menu {
  display: flex;
  gap: 0.5rem;
}

.edit-btn, .delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background 0.3s;
}

.edit-btn:hover {
  background: rgba(52, 152, 219, 0.1);
}

.delete-btn:hover {
  background: rgba(231, 76, 60, 0.1);
}

.task-description, .project-description {
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.task-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.priority {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.priority.low {
  background: #d4edda;
  color: #155724;
}

.priority.medium {
  background: #fff3cd;
  color: #856404;
}

.priority.high {
  background: #f8d7da;
  color: #721c24;
}

.due-date {
  color: #666;
  font-size: 0.9rem;
}

.task-footer, .project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.status-select {
  padding: 0.25rem 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  background: white;
}

.comments-btn, .view-tasks-btn {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.comments-btn:hover, .view-tasks-btn:hover {
  background: rgba(102, 126, 234, 0.2);
}

.project-stats {
  margin-bottom: 1rem;
}

.stat {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-weight: 600;
  color: #2c3e50;
}

.stat-value {
  color: #666;
}

.progress-bar {
  background: #e9ecef;
  border-radius: 10px;
  height: 8px;
  flex: 1;
  margin: 0 0.5rem;
  overflow: hidden;
}

.progress-fill {
  background: linear-gradient(135deg, #667eea, #764ba2);
  height: 100%;
  border-radius: 10px;
  transition: width 0.3s ease;
}

.created-date {
  color: #666;
  font-size: 0.9rem;
}

.recent-section {
  margin-top: 2rem;
}

.recent-section h2 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.task-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-item {
  background: white;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s;
}

.task-item:hover {
  transform: translateX(5px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.task-item.completed {
  opacity: 0.7;
  text-decoration: line-through;
}

.task-info h4 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.task-info p {
  margin: 0 0 0.25rem 0;
  color: #666;
  font-size: 0.9rem;
}

.task-info small {
  color: #999;
  font-size: 0.8rem;
}

.task-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.in_progress {
  background: #cce5ff;
  color: #004085;
}

.status-badge.completed {
  background: #d4edda;
  color: #155724;
}

.action-btn {
  background: #667eea;
  color: white;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.action-btn:hover {
  background: #5a6fd8;
  transform: scale(1.1);
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.comment-item {
  background: white;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-left: 3px solid #667eea;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.comment-header strong {
  color: #2c3e50;
}

.comment-header small {
  color: #999;
}

.comment-text {
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.comment-actions {
  display: flex;
  gap: 1rem;
}

.comment-actions button {
  background: none;
  border: none;
  color: #667eea;
  cursor: pointer;
  font-size: 0.9rem;
  transition: color 0.3s;
}

.comment-actions button:hover {
  color: #5a6fd8;
}

.comment-actions .delete-btn {
  color: #e74c3c;
}

.comment-actions .delete-btn:hover {
  color: #c0392b;
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
  border-radius: 15px;
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
  padding: 1.5rem;
  border-radius: 15px 15px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.3rem;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 1.5rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.task-form, .project-form {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2c3e50;
  font-weight: 600;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.secondary-btn {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.secondary-btn:hover {
  background: #5a6268;
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(5px);
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #e9ecef;
  border-top: 5px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Toast Messages */
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  color: white;
  font-weight: 600;
  z-index: 3000;
  animation: toastSlideIn 0.3s ease-out;
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
  .dashboard-header {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .dashboard-header h1 {
    font-size: 1.5rem;
  }
  
  .dashboard-nav {
    padding: 1rem;
    justify-content: flex-start;
  }
  
  .dashboard-main {
    padding: 1rem;
  }
  
  .tab-content {
    padding: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .tasks-grid, .projects-grid {
    grid-template-columns: 1fr;
  }
  
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .task-item {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .task-actions {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .nav-tab {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }
  
  .stat-card {
    padding: 1rem;
  }
  
  .stat-icon {
    font-size: 2rem;
  }
  
  .stat-info h3 {
    font-size: 1.5rem;
  }
  
  .task-card, .project-card {
    padding: 1rem;
  }
}</style>