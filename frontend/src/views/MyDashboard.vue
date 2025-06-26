<template>
  <div class="dashboard-container">
    <h2>Task Management Dashboard</h2>
    <button @click="logout" class="logout-btn">Logout</button>

    <div class="task-section">
      <h3>Your Tasks</h3>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Due Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in tasks" :key="task.id">
            <td>{{ task.title }}</td>
            <td>{{ task.description }}</td>
            <td>
              <select v-model="task.status" @change="updateTask(task)">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </td>
            <td>{{ task.priority }}</td>
            <td>{{ task.due_date }}</td>
            <td>
              <button @click="fetchSingleTask(task.id)">View</button>
              <button @click="editTask(task)">Edit</button>
              <button @click="deleteTask(task.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal or Section for Viewing a Single Task -->
    <div v-if="singleTask" class="view-task-section">
      <h3>Task Details</h3>
      <p><strong>Title:</strong> {{ singleTask.title }}</p>
      <p><strong>Description:</strong> {{ singleTask.description }}</p>
      <p><strong>Status:</strong> {{ singleTask.status }}</p>
      <p><strong>Priority:</strong> {{ singleTask.priority }}</p>
      <p><strong>Due Date:</strong> {{ singleTask.due_date }}</p>
      <button @click="singleTask = null">Close</button>
    </div>

    <div class="add-task-section">
      <h3>Add Task</h3>
      <form @submit.prevent="addTask">
        <input v-model="newTask.title" placeholder="Title" required />
        <input v-model="newTask.description" placeholder="Description" />
        <select v-model="newTask.priority">
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>
        <input v-model="newTask.due_date" type="date" />
        <button type="submit">Add</button>
      </form>
    </div>

    <div v-if="editMode" class="edit-task-section">
      <h3>Edit Task</h3>
      <form @submit.prevent="saveEdit">
        <input v-model="editTaskData.title" placeholder="Title" required />
        <input v-model="editTaskData.description" placeholder="Description" />
        <select v-model="editTaskData.priority">
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>
        <input v-model="editTaskData.due_date" type="date" />
        <select v-model="editTaskData.status">
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
        <button type="submit">Save</button>
        <button type="button" @click="editMode = false">Cancel</button>
      </form>
    </div>

    <div v-if="errorMessage" class="error-msg">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-msg">{{ successMessage }}</div>
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
      singleTask: null // For displaying a single task
    };
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
          this.errorMessage = data.error || 'Failed to fetch tasks';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    async fetchSingleTask(id) {
      this.errorMessage = '';
      this.successMessage = '';
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (res.ok) {
          this.singleTask = data;
          alert(`Task: ${data.title}\nDescription: ${data.description}\nStatus: ${data.status}`);
        } else {
          this.errorMessage = data.error || 'Task not found';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    async updateTaskStatus(task, newStatus) {
      this.errorMessage = '';
      this.successMessage = '';
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${task.id}/status`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ status: newStatus })
        });
        const data = await res.json();
        if (res.ok) {
          this.successMessage = data.message;
          task.status = newStatus;
        } else {
          this.errorMessage = data.error || 'Failed to update status';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    async addTask() {
      this.errorMessage = '';
      this.successMessage = '';
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
          this.successMessage = data.message;
          this.newTask = { title: '', description: '', priority: 'medium', due_date: '' };
          this.fetchTasks();
        } else {
          this.errorMessage = data.error || 'Failed to add task';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    async updateTask(task) {
      this.errorMessage = '';
      this.successMessage = '';
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
          this.successMessage = data.message;
        } else {
          this.errorMessage = data.error || 'Failed to update task';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    editTask(task) {
      this.editMode = true;
      this.editTaskData = { ...task };
    },
    async saveEdit() {
      this.errorMessage = '';
      this.successMessage = '';
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
          this.successMessage = data.message;
          this.editMode = false;
          this.fetchTasks();
        } else {
          this.errorMessage = data.error || 'Failed to update task';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    async deleteTask(id) {
      this.errorMessage = '';
      this.successMessage = '';
      try {
        const token = localStorage.getItem('jwt_token');
        const res = await fetch(`http://localhost:8000/api/tasks/${id}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (res.ok) {
          this.successMessage = data.message;
          this.fetchTasks();
        } else {
          this.errorMessage = data.error || 'Failed to delete task';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    },
    logout() {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('user_info');
      this.$router.push('/');
    }
  },
  mounted() {
    this.fetchTasks();
  }
};
</script>

<style scoped>
.dashboard-container {
  max-width: 900px;
  margin: 40px auto;
  padding: 32px;
  background: #fff;
  box-shadow: 0 6px 24px rgba(0,0,0,0.10);
  border-radius: 12px;
  border: 1px solid #e3e3e3;
  position: relative;
}
.logout-btn {
  float: right;
  padding: 8px 16px;
  background: #dc3545;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.task-section, .add-task-section, .edit-task-section {
  margin-top: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
th {
  background-color: #f8f9fa;
}
input, select {
  margin-right: 8px;
  padding: 6px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.error-msg, .success-msg {
  margin-top: 15px;
  padding: 10px;
  text-align: center;
  border-radius: 4px;
}
.error-msg {
  color: #dc3545;
  background: #fff0f2;
  border: 1px solid #f5c2c7;
}
.success-msg {
  color: #28a745;
  background: #eafaf1;
  border: 1px solid #b7e4c7;
}
</style>