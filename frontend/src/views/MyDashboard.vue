<template>
  <div class="shell">
    <!-- ============================================ Sidebar -->
    <aside class="sidebar">
      <div class="sidebar__brand">
        <span class="sidebar__mark">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <span class="sidebar__wordmark">TaskMaster</span>
      </div>

      <div class="sidebar__section">Workspace</div>
      <nav class="sidebar__nav">
        <a href="#" class="navitem is-active">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="2"/>
            <rect x="14" y="3" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="2"/>
            <rect x="14" y="12" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="2"/>
            <rect x="3" y="16" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="2"/>
          </svg>
          Overview
        </a>
        <a href="#" class="navitem">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 11L11 13L15 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
          </svg>
          Tasks
          <span class="navitem__count">{{ totalTasks }}</span>
        </a>
        <a href="#" class="navitem">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M3 10H21M8 3V7M16 3V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Calendar
        </a>
        <a href="#" class="navitem">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 20V14M12 20V6M18 20V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Reports
        </a>
      </nav>

      <!-- Focus meter — a compact read on how much of the board is done. -->
      <div class="focus">
        <div class="focus__head">
          <span>Focus</span>
          <span class="focus__dot"></span>
        </div>
        <div class="focus__value">{{ completionRate }}%</div>
        <div class="focus__track">
          <div class="focus__fill" :style="{ width: completionRate + '%' }"></div>
        </div>
        <p class="focus__note">{{ completedTasks }} of {{ totalTasks || 0 }} complete</p>
      </div>

      <div class="sidebar__foot">
        <div class="account">
          <div class="account__avatar">{{ userInitials }}</div>
          <div class="account__id">
            <p class="account__name">{{ currentUser?.username || 'User' }}</p>
            <p class="account__role">{{ currentUser?.role || 'Member' }}</p>
          </div>
        </div>
        <button @click="logout" class="navitem navitem--quiet">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M16 17L21 12L16 7M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Sign out
        </button>
      </div>
    </aside>

    <!-- ============================================ Main -->
    <div class="main">
      <header class="topbar">
        <div>
          <p class="topbar__eyebrow">{{ currentUser?.username || 'Your' }} workspace</p>
          <h1 class="topbar__title">Task dashboard</h1>
        </div>
        <div class="topbar__actions">
          <span class="pill">
            <span class="pill__dot"></span>
            Live
          </span>
          <button @click="showAddModal = true" class="btn btn--primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            New task
          </button>
        </div>
      </header>

      <div class="canvas">
        <!-- Stats -->
        <section class="stats">
          <article class="stat">
            <p class="stat__label">Open tasks</p>
            <p class="stat__value">{{ pendingTasks }}</p>
            <p class="stat__meta">Waiting to start</p>
          </article>
          <article class="stat">
            <p class="stat__label">In progress</p>
            <p class="stat__value">{{ inProgressTasks }}</p>
            <p class="stat__meta stat__meta--blue">Active right now</p>
          </article>
          <article class="stat">
            <p class="stat__label">Completed</p>
            <p class="stat__value">{{ completedTasks }}</p>
            <p class="stat__meta stat__meta--mint">Done and dusted</p>
          </article>
          <article class="stat stat--accent">
            <p class="stat__label">Total</p>
            <p class="stat__value">{{ totalTasks }}</p>
            <div class="stat__track">
              <div class="stat__fill" :style="{ width: completionRate + '%' }"></div>
            </div>
            <p class="stat__meta">{{ completionRate }}% complete</p>
          </article>
        </section>

        <!-- Tasks -->
        <section class="panel">
          <header class="panel__head">
            <div>
              <h2 class="panel__title">Your tasks</h2>
              <p class="panel__sub">{{ filteredTasks.length }} shown of {{ totalTasks }}</p>
            </div>
            <div class="panel__filters">
              <div class="select">
                <select v-model="statusFilter" @change="filterTasks">
                  <option value="">All status</option>
                  <option value="pending">Pending</option>
                  <option value="in_progress">In progress</option>
                  <option value="completed">Completed</option>
                </select>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="select">
                <select v-model="priorityFilter" @change="filterTasks">
                  <option value="">All priority</option>
                  <option value="high">High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </header>

          <div v-if="filteredTasks.length" class="table-scroll">
            <table class="table">
              <thead>
                <tr>
                  <th>Task</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Due</th>
                  <th class="table__right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="task in filteredTasks" :key="task.id">
                  <td>
                    <div class="task">
                      <span class="task__tick" :class="'is-' + task.status"></span>
                      <div>
                        <p class="task__title">{{ task.title }}</p>
                        <p v-if="task.description" class="task__desc">{{ task.description }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="select select--sm" :class="'tone-' + task.status">
                      <select v-model="task.status" @change="updateTask(task)">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In progress</option>
                        <option value="completed">Completed</option>
                      </select>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                  </td>
                  <td>
                    <span class="badge" :class="'badge--' + task.priority">{{ task.priority }}</span>
                  </td>
                  <td class="table__muted">{{ formatDate(task.due_date) }}</td>
                  <td>
                    <div class="rowactions">
                      <button @click="viewTask(task)" class="iconbtn" title="View">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                        </svg>
                      </button>
                      <button @click="editTask(task)" class="iconbtn" title="Edit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M11 4H4C2.89543 4 2 4.89543 2 6V20C2 21.1046 2.89543 22 4 22H18C19.1046 22 20 21.1046 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                          <path d="M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </button>
                      <button @click="confirmDelete(task)" class="iconbtn iconbtn--danger" title="Delete">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M3 6H21M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6M19 6V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="empty">
            <div class="empty__ring">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 11L11 13L15 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <h3>{{ tasks.length ? 'Nothing matches those filters' : 'No tasks yet' }}</h3>
            <p>{{ tasks.length ? 'Try clearing a filter to see more.' : 'Create your first task to get started.' }}</p>
            <button v-if="!tasks.length" @click="showAddModal = true" class="btn btn--primary">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
              New task
            </button>
          </div>
        </section>
      </div>
    </div>

    <!-- ============================================ Add modal -->
    <transition name="fade">
      <div v-if="showAddModal" class="scrim" @click="closeModal">
        <div class="modal" @click.stop>
          <header class="modal__head">
            <h3>New task</h3>
            <button @click="closeModal" class="iconbtn" aria-label="Close">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </header>

          <form @submit.prevent="addTask" class="modal__body">
            <div class="field">
              <label for="title">Task title</label>
              <div class="field__control">
                <input v-model="newTask.title" type="text" id="title" placeholder="What needs doing?" required />
              </div>
            </div>

            <div class="field">
              <label for="description">Description</label>
              <div class="field__control">
                <textarea v-model="newTask.description" id="description" placeholder="Add any detail worth remembering" rows="3"></textarea>
              </div>
            </div>

            <div class="modal__row">
              <div class="field">
                <label for="priority">Priority</label>
                <div class="field__control">
                  <select v-model="newTask.priority" id="priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                  <svg class="field__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </div>

              <div class="field">
                <label for="due_date">Due date</label>
                <div class="field__control">
                  <input v-model="newTask.due_date" type="date" id="due_date" />
                </div>
              </div>
            </div>

            <footer class="modal__foot">
              <button type="button" @click="closeModal" class="btn btn--ghost">Cancel</button>
              <button type="submit" class="btn btn--primary" :disabled="isLoading">
                {{ isLoading ? 'Creating…' : 'Create task' }}
              </button>
            </footer>
          </form>
        </div>
      </div>
    </transition>

    <!-- ============================================ Edit modal -->
    <transition name="fade">
      <div v-if="editMode" class="scrim" @click="cancelEdit">
        <div class="modal" @click.stop>
          <header class="modal__head">
            <h3>Edit task</h3>
            <button @click="cancelEdit" class="iconbtn" aria-label="Close">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </header>

          <form @submit.prevent="saveEdit" class="modal__body">
            <div class="field">
              <label for="edit-title">Task title</label>
              <div class="field__control">
                <input v-model="editTaskData.title" type="text" id="edit-title" placeholder="Enter task title" required />
              </div>
            </div>

            <div class="field">
              <label for="edit-description">Description</label>
              <div class="field__control">
                <textarea v-model="editTaskData.description" id="edit-description" placeholder="Enter task description" rows="3"></textarea>
              </div>
            </div>

            <div class="modal__row">
              <div class="field">
                <label for="edit-priority">Priority</label>
                <div class="field__control">
                  <select v-model="editTaskData.priority" id="edit-priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                  <svg class="field__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </div>

              <div class="field">
                <label for="edit-status">Status</label>
                <div class="field__control">
                  <select v-model="editTaskData.status" id="edit-status">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In progress</option>
                    <option value="completed">Completed</option>
                  </select>
                  <svg class="field__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </div>
            </div>

            <div class="field">
              <label for="edit-due-date">Due date</label>
              <div class="field__control">
                <input v-model="editTaskData.due_date" type="date" id="edit-due-date" />
              </div>
            </div>

            <footer class="modal__foot">
              <button type="button" @click="cancelEdit" class="btn btn--ghost">Cancel</button>
              <button type="submit" class="btn btn--primary" :disabled="isLoading">
                {{ isLoading ? 'Saving…' : 'Save changes' }}
              </button>
            </footer>
          </form>
        </div>
      </div>
    </transition>

    <!-- ============================================ View modal -->
    <transition name="fade">
      <div v-if="singleTask" class="scrim" @click="closeSingleTask">
        <div class="modal" @click.stop>
          <header class="modal__head">
            <h3>Task details</h3>
            <button @click="closeSingleTask" class="iconbtn" aria-label="Close">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </header>

          <div class="modal__body">
            <dl class="detail">
              <dt>Title</dt>
              <dd>{{ singleTask.title }}</dd>

              <dt>Description</dt>
              <dd>{{ singleTask.description || 'No description provided' }}</dd>

              <dt>Status</dt>
              <dd><span class="badge" :class="'badge--' + singleTask.status">{{ formatStatus(singleTask.status) }}</span></dd>

              <dt>Priority</dt>
              <dd><span class="badge" :class="'badge--' + singleTask.priority">{{ singleTask.priority }}</span></dd>

              <dt>Due date</dt>
              <dd>{{ formatDate(singleTask.due_date) }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </transition>

    <!-- ============================================ Delete modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="scrim" @click="cancelDelete">
        <div class="modal modal--narrow" @click.stop>
          <div class="modal__body confirm">
            <div class="confirm__ring">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6H21M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6M19 6V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3>Delete this task?</h3>
            <p>"{{ taskToDelete?.title }}" will be removed permanently. This can't be undone.</p>
          </div>
          <footer class="modal__foot">
            <button @click="cancelDelete" class="btn btn--ghost">Cancel</button>
            <button @click="deleteTask(taskToDelete.id)" class="btn btn--danger" :disabled="isLoading">
              {{ isLoading ? 'Deleting…' : 'Delete task' }}
            </button>
          </footer>
        </div>
      </div>
    </transition>

    <!-- ============================================ Toasts -->
    <transition name="drop">
      <div v-if="successMessage" class="toast toast--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ successMessage }}
      </div>
    </transition>

    <transition name="drop">
      <div v-if="errorMessage" class="toast toast--error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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
    },
    completionRate() {
      if (!this.tasks.length) return 0;
      return Math.round((this.completedTasks / this.tasks.length) * 100);
    },
    userInitials() {
      const name = this.currentUser?.full_name || this.currentUser?.username || 'U';
      return name
        .split(/\s+/)
        .slice(0, 2)
        .map(part => part.charAt(0).toUpperCase())
        .join('');
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
/* ==========================================================================
   Dashboard shell — fixed sidebar, scrolling canvas.
   ========================================================================== */

.shell {
  display: grid;
  grid-template-columns: 264px 1fr;
  min-height: 100vh;
  background: var(--bg);
}

/* -------------------------------------------------- sidebar */

.sidebar {
  position: sticky;
  top: 0;
  align-self: start;
  display: flex;
  flex-direction: column;
  height: 100vh;
  padding: 22px 16px;
  border-right: 1px solid var(--border-soft);
  background:
    radial-gradient(80% 40% at 0% 0%, rgba(34, 211, 238, 0.07), transparent 70%),
    var(--bg-elevated);
}

.sidebar__brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 8px 22px;
}

.sidebar__mark {
  display: grid;
  place-items: center;
  width: 30px;
  height: 30px;
  border-radius: var(--r-sm);
  background: var(--accent);
  color: var(--accent-ink);
}

.sidebar__wordmark {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 17px;
  letter-spacing: -0.04em;
  color: var(--text-bright);
}

.sidebar__section {
  padding: 0 8px 8px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.10em;
  text-transform: uppercase;
  color: var(--text-dim);
}

.sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.navitem {
  display: flex;
  align-items: center;
  gap: 11px;
  width: 100%;
  padding: 10px 12px;
  border: 1px solid transparent;
  border-radius: var(--r-md);
  background: transparent;
  font-size: 14px;
  text-align: left;
  text-decoration: none;
  color: var(--text-muted);
  transition: color 0.16s var(--ease), background 0.16s var(--ease), border-color 0.16s var(--ease);
}

.navitem:hover {
  color: var(--text);
  background: var(--surface);
}

.navitem.is-active {
  color: var(--text-bright);
  background: var(--surface-active);
  border-color: var(--border);
}

.navitem__count {
  margin-left: auto;
  padding: 2px 8px;
  border-radius: var(--r-pill);
  background: var(--surface-hover);
  font-size: 11px;
  font-variant-numeric: tabular-nums;
  color: var(--text-muted);
}

.navitem--quiet { font-size: 13px; }
.navitem--quiet:hover { color: var(--rose); }

/* Focus meter */

.focus {
  margin: 22px 0 auto;
  padding: 16px;
  border: 1px solid var(--border-soft);
  border-radius: var(--r-lg);
  background: var(--surface);
}

.focus__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.10em;
  text-transform: uppercase;
  color: var(--text-dim);
}

.focus__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent-vivid);
  box-shadow: 0 0 10px var(--accent-vivid);
}

.focus__value {
  margin: 10px 0 12px;
  font-family: var(--font-display);
  font-size: 34px;
  font-weight: 500;
  letter-spacing: -0.05em;
  line-height: 1;
  color: var(--text-bright);
}

.focus__track {
  height: 4px;
  border-radius: var(--r-pill);
  background: rgba(255, 255, 255, 0.08);
  overflow: hidden;
}

.focus__fill {
  height: 100%;
  border-radius: var(--r-pill);
  background: linear-gradient(90deg, var(--accent-vivid), var(--mint));
  transition: width 0.5s var(--ease);
}

.focus__note {
  margin: 10px 0 0;
  font-size: 12px;
  color: var(--text-dim);
}

/* Account */

.sidebar__foot {
  padding-top: 14px;
  border-top: 1px solid var(--border-soft);
}

.account {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  margin-bottom: 4px;
}

.account__avatar {
  display: grid;
  place-items: center;
  flex: none;
  width: 34px;
  height: 34px;
  border-radius: var(--r-pill);
  background: linear-gradient(140deg, var(--accent-vivid), var(--mint));
  color: var(--accent-ink);
  font-size: 12px;
  font-weight: 600;
}

.account__id { min-width: 0; }

.account__name {
  margin: 0;
  font-size: 13px;
  font-weight: 500;
  color: var(--text);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.account__role {
  margin: 0;
  font-size: 11px;
  text-transform: capitalize;
  color: var(--text-dim);
}

/* -------------------------------------------------- main */

.main { min-width: 0; }

.topbar {
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 20px 32px;
  border-bottom: 1px solid var(--border-soft);
  background: rgba(8, 12, 20, 0.8);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}

.topbar__eyebrow {
  margin: 0 0 3px;
  font-size: 12px;
  color: var(--text-dim);
}

.topbar__title { font-size: 24px; }

.topbar__actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 6px 13px;
  border: 1px solid var(--border);
  border-radius: var(--r-pill);
  background: var(--surface);
  font-size: 12px;
  color: var(--text-muted);
}

.pill__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--mint);
  box-shadow: 0 0 10px var(--mint);
}

.canvas {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 24px 32px 48px;
}

/* -------------------------------------------------- stats */

.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat {
  padding: 18px;
  border: 1px solid var(--border-soft);
  border-radius: var(--r-lg);
  background: var(--surface);
  transition: border-color 0.2s var(--ease), background 0.2s var(--ease);
}

.stat:hover {
  border-color: var(--border);
  background: var(--surface-hover);
}

.stat--accent {
  background:
    linear-gradient(150deg, rgba(34, 211, 238, 0.10), transparent 60%),
    var(--surface);
  border-color: rgba(34, 211, 238, 0.20);
}

.stat__label {
  margin: 0;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.10em;
  text-transform: uppercase;
  color: var(--text-dim);
}

.stat__value {
  margin: 10px 0 6px;
  font-family: var(--font-display);
  font-size: 38px;
  font-weight: 500;
  letter-spacing: -0.05em;
  line-height: 1;
  color: var(--text-bright);
  font-variant-numeric: tabular-nums;
}

.stat__meta {
  margin: 0;
  font-size: 12px;
  color: var(--text-dim);
}

.stat__meta--blue { color: var(--blue); }
.stat__meta--mint { color: var(--mint); }

.stat__track {
  height: 4px;
  margin: 4px 0 8px;
  border-radius: var(--r-pill);
  background: rgba(255, 255, 255, 0.08);
  overflow: hidden;
}

.stat__fill {
  height: 100%;
  border-radius: var(--r-pill);
  background: linear-gradient(90deg, var(--accent-vivid), var(--mint));
  transition: width 0.5s var(--ease);
}

/* -------------------------------------------------- panel */

.panel {
  border: 1px solid var(--border-soft);
  border-radius: var(--r-xl);
  background: var(--panel);
  overflow: hidden;
}

.panel__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  padding: 20px 22px;
  border-bottom: 1px solid var(--border-soft);
}

.panel__title { font-size: 18px; }

.panel__sub {
  margin: 3px 0 0;
  font-size: 13px;
  color: var(--text-dim);
}

.panel__filters { display: flex; gap: 10px; }

/* Select — the arrow is drawn by us since `appearance: none` drops the native one. */
.select {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.select select {
  appearance: none;
  -webkit-appearance: none;
  padding: 9px 34px 9px 14px;
  border: 1px solid var(--border);
  border-radius: var(--r-pill);
  background: var(--surface);
  font-size: 13px;
  color: var(--text);
  cursor: pointer;
  outline: none;
  transition: border-color 0.16s var(--ease), background 0.16s var(--ease);
}

.select select:hover {
  border-color: var(--border-strong);
  background: var(--surface-hover);
}

.select select:focus-visible {
  outline: 2px solid var(--accent-vivid);
  outline-offset: 1px;
}

.select > svg {
  position: absolute;
  right: 13px;
  color: var(--text-dim);
  pointer-events: none;
}

.select--sm select {
  padding: 6px 28px 6px 11px;
  font-size: 12px;
  font-weight: 500;
}

.select--sm > svg { right: 10px; }

.tone-pending select     { color: var(--amber); border-color: rgba(251, 191, 36, 0.28); background: rgba(251, 191, 36, 0.08); }
.tone-in_progress select { color: var(--blue);  border-color: rgba(147, 197, 253, 0.28); background: rgba(147, 197, 253, 0.08); }
.tone-completed select   { color: var(--mint);  border-color: rgba(110, 231, 183, 0.28); background: rgba(110, 231, 183, 0.08); }

.tone-pending > svg     { color: var(--amber); }
.tone-in_progress > svg { color: var(--blue); }
.tone-completed > svg   { color: var(--mint); }

/* -------------------------------------------------- table */

.table-scroll { overflow-x: auto; }

.table {
  width: 100%;
  border-collapse: collapse;
  min-width: 780px;
}

.table th {
  padding: 12px 22px;
  border-bottom: 1px solid var(--border-soft);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.10em;
  text-transform: uppercase;
  text-align: left;
  color: var(--text-dim);
}

.table td {
  padding: 14px 22px;
  border-bottom: 1px solid var(--border-soft);
  vertical-align: middle;
}

.table tbody tr { transition: background 0.15s var(--ease); }
.table tbody tr:hover { background: rgba(255, 255, 255, 0.025); }
.table tbody tr:last-child td { border-bottom: 0; }

.table__right { text-align: right; }
.table__muted { font-size: 13px; color: var(--text-muted); white-space: nowrap; }

.task {
  display: flex;
  align-items: flex-start;
  gap: 11px;
}

.task__tick {
  flex: none;
  width: 8px;
  height: 8px;
  margin-top: 6px;
  border-radius: 50%;
}

.task__tick.is-pending     { background: var(--amber); box-shadow: 0 0 8px rgba(251, 191, 36, 0.5); }
.task__tick.is-in_progress { background: var(--blue);  box-shadow: 0 0 8px rgba(147, 197, 253, 0.5); }
.task__tick.is-completed   { background: var(--mint);  box-shadow: 0 0 8px rgba(110, 231, 183, 0.5); }

.task__title {
  margin: 0;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-bright);
}

.task__desc {
  margin: 3px 0 0;
  max-width: 42ch;
  font-size: 12.5px;
  color: var(--text-dim);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.badge {
  display: inline-block;
  padding: 4px 11px;
  border: 1px solid transparent;
  border-radius: var(--r-pill);
  font-size: 11px;
  font-weight: 500;
  text-transform: capitalize;
}

.badge--high,
.badge--pending     { color: var(--amber); background: rgba(251, 191, 36, 0.10); border-color: rgba(251, 191, 36, 0.25); }
.badge--medium,
.badge--in_progress { color: var(--blue);  background: rgba(147, 197, 253, 0.10); border-color: rgba(147, 197, 253, 0.25); }
.badge--low,
.badge--completed   { color: var(--mint);  background: rgba(110, 231, 183, 0.10); border-color: rgba(110, 231, 183, 0.25); }

.badge--high { color: var(--rose); background: rgba(251, 113, 133, 0.10); border-color: rgba(251, 113, 133, 0.25); }

.rowactions {
  display: flex;
  justify-content: flex-end;
  gap: 4px;
}

.iconbtn {
  display: grid;
  place-items: center;
  width: 30px;
  height: 30px;
  border: 1px solid transparent;
  border-radius: var(--r-sm);
  background: transparent;
  color: var(--text-dim);
  transition: color 0.15s var(--ease), background 0.15s var(--ease), border-color 0.15s var(--ease);
}

.iconbtn:hover {
  color: var(--text-bright);
  background: var(--surface-hover);
  border-color: var(--border);
}

.iconbtn--danger:hover {
  color: var(--rose);
  background: rgba(251, 113, 133, 0.10);
  border-color: rgba(251, 113, 133, 0.25);
}

/* -------------------------------------------------- empty */

.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 72px 24px;
  text-align: center;
}

.empty__ring {
  display: grid;
  place-items: center;
  width: 56px;
  height: 56px;
  margin-bottom: 10px;
  border: 1px solid var(--border);
  border-radius: var(--r-pill);
  background: var(--surface);
  color: var(--accent-vivid);
}

.empty h3 { font-size: 17px; }

.empty p {
  margin: 0 0 14px;
  font-size: 14px;
  color: var(--text-dim);
}

/* -------------------------------------------------- modals */

.scrim {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(2, 6, 23, 0.72);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  overflow-y: auto;
}

.modal {
  width: 100%;
  max-width: 520px;
  border: 1px solid var(--border);
  border-radius: var(--r-xl);
  background: var(--bg-elevated);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
}

.modal--narrow { max-width: 400px; }

.modal__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 22px;
  border-bottom: 1px solid var(--border-soft);
}

.modal__head h3 { font-size: 18px; }

.modal__body {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 22px;
}

.modal__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.modal__foot {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 22px;
  border-top: 1px solid var(--border-soft);
  background: rgba(255, 255, 255, 0.015);
}

/* In the add/edit modals the footer lives inside the form, which is also the
   padded body — pull it back out so its rule spans the full modal width. */
.modal__body > .modal__foot {
  margin: 6px -22px -22px;
}

/* `.field`, `.btn`, `.toast` come from the global auth stylesheet; only the
   pieces unique to this view are declared here. */

.modal__body textarea {
  flex: 1;
  min-width: 0;
  padding: 13px 14px;
  border: 0;
  background: transparent;
  font-size: 15px;
  line-height: 1.5;
  color: var(--text);
  outline: none;
  resize: vertical;
}

.modal__body textarea::placeholder { color: var(--text-dim); }

.btn--danger {
  background: var(--danger);
  color: #fff;
}

.btn--danger:hover:not(:disabled) {
  background: #e11d48;
  box-shadow: 0 0 26px -8px rgba(244, 63, 94, 0.7);
}

.detail {
  display: grid;
  grid-template-columns: 108px 1fr;
  gap: 14px 16px;
  margin: 0;
}

.detail dt {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--text-dim);
  padding-top: 2px;
}

.detail dd {
  margin: 0;
  font-size: 14px;
  color: var(--text);
  overflow-wrap: anywhere;
}

.confirm {
  align-items: center;
  text-align: center;
  padding-top: 30px;
}

.confirm__ring {
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  border-radius: var(--r-pill);
  background: rgba(244, 63, 94, 0.12);
  border: 1px solid rgba(244, 63, 94, 0.3);
  color: var(--danger);
}

.confirm h3 { font-size: 18px; }

.confirm p {
  margin: 0;
  font-size: 14px;
  line-height: 1.55;
  color: var(--text-muted);
}

.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s var(--ease); }

.fade-enter-from,
.fade-leave-to { opacity: 0; }

.fade-enter-active .modal,
.fade-leave-active .modal { transition: transform 0.22s var(--ease); }

.fade-enter-from .modal,
.fade-leave-to .modal { transform: translateY(10px) scale(0.98); }

/* -------------------------------------------------- responsive */

@media (max-width: 1100px) {
  .stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 860px) {
  .shell { grid-template-columns: 1fr; }

  .sidebar {
    position: static;
    height: auto;
    flex-direction: row;
    align-items: center;
    gap: 16px;
    overflow-x: auto;
    padding: 12px 16px;
    border-right: 0;
    border-bottom: 1px solid var(--border-soft);
  }

  .sidebar__brand { padding: 0; }
  .sidebar__section,
  .focus { display: none; }
  .sidebar__nav { flex-direction: row; }
  .navitem { white-space: nowrap; }

  .sidebar__foot {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    padding-top: 0;
    border-top: 0;
  }

  .account { margin-bottom: 0; }
  .account__id { display: none; }

  .topbar,
  .canvas { padding-left: 20px; padding-right: 20px; }
}

@media (max-width: 560px) {
  .stats { grid-template-columns: 1fr; }
  .modal__row { grid-template-columns: 1fr; }
  .topbar { flex-direction: column; align-items: flex-start; }
}
</style>
