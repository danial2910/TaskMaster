// API Service - Centralized API calls for TaskMaster
class ApiService {
  constructor() {
    this.baseURL = 'http://localhost/TaskMaster/backend/api.php'
    this.headers = {
      'Content-Type': 'application/json'
    }
  }

  // Get authorization headers
  getAuthHeaders() {
    const token = localStorage.getItem('token')
    return {
      ...this.headers,
      'Authorization': `Bearer ${token}`
    }
  }

  // Generic request method
  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`
    const config = {
      headers: options.requiresAuth ? this.getAuthHeaders() : this.headers,
      ...options
    }

    try {
      const response = await fetch(url, config)
      
      // Handle different response types
      const contentType = response.headers.get('content-type')
      let data
      
      if (contentType && contentType.includes('application/json')) {
        data = await response.json()
      } else {
        data = await response.text()
        // Try to parse as JSON if it looks like JSON
        try {
          data = JSON.parse(data)
        } catch (e) {
          // Keep as text if not valid JSON
        }
      }

      if (!response.ok) {
        throw new Error(data.error || data.message || `HTTP ${response.status}`)
      }

      return data
    } catch (error) {
      console.error('API Request failed:', error)
      throw error
    }
  }

  // Authentication Methods
  async register(userData) {
    return this.request('/api/auth/register', {
      method: 'POST',
      body: JSON.stringify(userData)
    })
  }

  async login(credentials) {
    return this.request('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify(credentials)
    })
  }

  // Workspace Methods
  async getWorkspaces() {
    return this.request('/api/workspaces', {
      method: 'GET',
      requiresAuth: true
    })
  }

  async getWorkspace(id) {
    return this.request(`/api/workspaces/${id}`, {
      method: 'GET',
      requiresAuth: true
    })
  }

  async createWorkspace(workspaceData) {
    return this.request('/api/workspaces', {
      method: 'POST',
      body: JSON.stringify(workspaceData),
      requiresAuth: true
    })
  }

  async updateWorkspace(id, workspaceData) {
    return this.request(`/api/workspaces/${id}`, {
      method: 'PUT',
      body: JSON.stringify(workspaceData),
      requiresAuth: true
    })
  }

  async deleteWorkspace(id) {
    return this.request(`/api/workspaces/${id}`, {
      method: 'DELETE',
      requiresAuth: true
    })
  }

  // Project Methods
  async getProjects() {
    return this.request('/api/projects', {
      method: 'GET',
      requiresAuth: true
    })
  }

  async getProject(id) {
    return this.request(`/api/projects/${id}`, {
      method: 'GET',
      requiresAuth: true
    })
  }

  async createProject(projectData) {
    return this.request('/api/projects', {
      method: 'POST',
      body: JSON.stringify(projectData),
      requiresAuth: true
    })
  }

  async updateProject(id, projectData) {
    return this.request(`/api/projects/${id}`, {
      method: 'PUT',
      body: JSON.stringify(projectData),
      requiresAuth: true
    })
  }

  async deleteProject(id) {
    return this.request(`/api/projects/${id}`, {
      method: 'DELETE',
      requiresAuth: true
    })
  }

  // Task Methods
  async getTasks() {
    return this.request('/api/tasks', {
      method: 'GET',
      requiresAuth: true
    })
  }

  async getTask(id) {
    return this.request(`/api/tasks/${id}`, {
      method: 'GET',
      requiresAuth: true
    })
  }

  async createTask(taskData) {
    return this.request('/api/tasks', {
      method: 'POST',
      body: JSON.stringify(taskData),
      requiresAuth: true
    })
  }

  async updateTask(id, taskData) {
    return this.request(`/api/tasks/${id}`, {
      method: 'PUT',
      body: JSON.stringify(taskData),
      requiresAuth: true
    })
  }

  async updateTaskStatus(id, status) {
    return this.request(`/api/tasks/${id}/status`, {
      method: 'PUT',
      body: JSON.stringify({ status }),
      requiresAuth: true
    })
  }

  async deleteTask(id) {
    return this.request(`/api/tasks/${id}`, {
      method: 'DELETE',
      requiresAuth: true
    })
  }

  // Comment Methods
  async getComments() {
    return this.request('/api/comments', {
      method: 'GET',
      requiresAuth: true
    })
  }

  async getTaskComments(taskId) {
    return this.request(`/api/comments/task/${taskId}`, {
      method: 'GET',
      requiresAuth: true
    })
  }

  async createComment(commentData) {
    return this.request('/api/comments', {
      method: 'POST',
      body: JSON.stringify(commentData),
      requiresAuth: true
    })
  }

  async updateComment(id, commentData) {
    return this.request(`/api/comments/${id}`, {
      method: 'PUT',
      body: JSON.stringify(commentData),
      requiresAuth: true
    })
  }

  async deleteComment(id) {
    return this.request(`/api/comments/${id}`, {
      method: 'DELETE',
      requiresAuth: true
    })
  }

  // Utility Methods
  async testConnection() {
    return this.request('', {
      method: 'GET'
    })
  }

  // Batch operations
  async batchUpdateTasks(updates) {
    const promises = updates.map(update => 
      this.updateTask(update.id, update.data)
    )
    return Promise.all(promises)
  }

  async batchDeleteTasks(taskIds) {
    const promises = taskIds.map(id => this.deleteTask(id))
    return Promise.all(promises)
  }

  // Search and filter methods
  async searchTasks(query, filters = {}) {
    const tasks = await this.getTasks()
    
    let filtered = tasks

    // Text search
    if (query) {
      const searchLower = query.toLowerCase()
      filtered = filtered.filter(task => 
        task.title.toLowerCase().includes(searchLower) ||
        (task.content && task.content.toLowerCase().includes(searchLower))
      )
    }

    // Apply filters
    if (filters.status) {
      filtered = filtered.filter(task => task.status === filters.status)
    }

    if (filters.priority) {
      filtered = filtered.filter(task => task.priority === filters.priority)
    }

    if (filters.projectId) {
      filtered = filtered.filter(task => task.project_id == filters.projectId)
    }

    if (filters.dueDate) {
      const dueDate = new Date(filters.dueDate)
      filtered = filtered.filter(task => {
        if (!task.due_date) return false
        const taskDue = new Date(task.due_date)
        return taskDue.toDateString() === dueDate.toDateString()
      })
    }

    // Sort options
    if (filters.sortBy) {
      filtered.sort((a, b) => {
        switch (filters.sortBy) {
          case 'title':
            return a.title.localeCompare(b.title)
          case 'priority':
            const priorityOrder = { high: 3, medium: 2, low: 1 }
            return priorityOrder[b.priority] - priorityOrder[a.priority]
          case 'dueDate':
            if (!a.due_date && !b.due_date) return 0
            if (!a.due_date) return 1
            if (!b.due_date) return -1
            return new Date(a.due_date) - new Date(b.due_date)
          case 'created':
            return new Date(b.created_at) - new Date(a.created_at)
          default:
            return 0
        }
      })
    }

    return filtered
  }

  // Analytics methods
  async getTaskStats() {
    const tasks = await this.getTasks()
    
    return {
      total: tasks.length,
      todo: tasks.filter(t => t.status === 'todo').length,
      inProgress: tasks.filter(t => t.status === 'in_progress').length,
      done: tasks.filter(t => t.status === 'done').length,
      high: tasks.filter(t => t.priority === 'high').length,
      medium: tasks.filter(t => t.priority === 'medium').length,
      low: tasks.filter(t => t.priority === 'low').length,
      overdue: tasks.filter(t => t.due_date && new Date(t.due_date) < new Date()).length
    }
  }

  async getProjectStats() {
    const projects = await this.getProjects()
    
    return {
      total: projects.length,
      totalTasks: projects.reduce((sum, p) => sum + (p.tasks_count || 0), 0),
      avgProgress: projects.length > 0 
        ? projects.reduce((sum, p) => sum + (p.progress || 0), 0) / projects.length 
        : 0
    }
  }

  // Export/Import methods
  async exportTasks(format = 'json') {
    const tasks = await this.getTasks()
    
    if (format === 'csv') {
      return this.convertToCSV(tasks)
    }
    
    return JSON.stringify(tasks, null, 2)
  }

  convertToCSV(data) {
    if (!data.length) return ''
    
    const headers = Object.keys(data[0])
    const csvContent = [
      headers.join(','),
      ...data.map(row => 
        headers.map(header => {
          const value = row[header]
          // Escape commas and quotes in CSV
          if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
            return `"${value.replace(/"/g, '""')}"`
          }
          return value
        }).join(',')
      )
    ].join('\n')
    
    return csvContent
  }

  // Error handling helper
  handleApiError(error) {
    console.error('API Error:', error)
    
    // Common error messages
    const errorMessages = {
      401: 'Please log in to continue',
      403: 'You don\'t have permission to perform this action',
      404: 'The requested resource was not found',
      429: 'Too many requests. Please try again later',
      500: 'Server error. Please try again later'
    }

    if (error.status && errorMessages[error.status]) {
      return errorMessages[error.status]
    }

    return error.message || 'An unexpected error occurred'
  }
}

// Create and export a singleton instance
const apiService = new ApiService()

export default apiService

// Also export the class for testing or multiple instances
export { ApiService }