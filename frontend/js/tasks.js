import { api } from './api.js';

document.addEventListener('DOMContentLoaded', () => {
    const taskList = document.getElementById('task-list');
    const addTaskBtn = document.getElementById('add-task-btn');
    const taskForm = document.getElementById('task-form');
    const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));

    // Load tasks if user is logged in
    if (localStorage.getItem('jwt_token')) {
        loadTasks();
    }

    // Add task button click
    if (addTaskBtn) {
        addTaskBtn.addEventListener('click', () => {
            document.getElementById('task-modal-title').textContent = 'Add New Task';
            document.getElementById('task-id').value = '';
            document.getElementById('task-form').reset();
            taskModal.show();
        });
    }

    // Task form submission
    if (taskForm) {
        taskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const taskId = document.getElementById('task-id').value;
            const taskData = {
                title: document.getElementById('task-title').value,
                description: document.getElementById('task-description').value,
                status: document.getElementById('task-status').value,
                due_date: document.getElementById('task-due-date').value || null
            };

            try {
                if (taskId) {
                    // Update existing task
                    await api.updateTask(taskId, taskData);
                } else {
                    // Create new task
                    await api.createTask(taskData);
                }
                
                loadTasks();
                taskModal.hide();
            } catch (error) {
                document.getElementById('task-error').textContent = error.message;
                document.getElementById('task-error').classList.remove('d-none');
            }
        });
    }
});

export async function loadTasks() {
    try {
        const tasks = await api.getTasks();
        const taskList = document.getElementById('task-list');
        
        if (tasks.length === 0) {
            taskList.innerHTML = `
                <div class="col-12 text-center py-5" id="no-tasks-message">
                    <h4>No tasks found. Add your first task!</h4>
                </div>
            `;
            return;
        }

        taskList.innerHTML = tasks.map(task => `
            <div class="col-md-4">
                <div class="card task-card ${task.status === 'completed' ? 'task-completed' : ''}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">${task.title}</h5>
                            <span class="status-badge status-${task.status}">
                                ${task.status.replace('_', ' ')}
                            </span>
                        </div>
                        <p class="card-text">${task.description || 'No description'}</p>
                        ${task.due_date ? `<p class="text-muted"><small>Due: ${new Date(task.due_date).toLocaleString()}</small></p>` : ''}
                        
                        <div class="task-actions d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-outline-primary edit-task" data-id="${task.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                            ${task.status !== 'completed' ? `
                                <button class="btn btn-sm btn-outline-success complete-task" data-id="${task.id}">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Add event listeners to action buttons
        document.querySelectorAll('.edit-task').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const taskId = e.currentTarget.getAttribute('data-id');
                await loadTaskForEdit(taskId);
            });
        });

        document.querySelectorAll('.delete-task').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const taskId = e.currentTarget.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this task?')) {
                    try {
                        await api.deleteTask(taskId);
                        loadTasks();
                    } catch (error) {
                        alert(error.message);
                    }
                }
            });
        });

        document.querySelectorAll('.complete-task').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const taskId = e.currentTarget.getAttribute('data-id');
                try {
                    await api.completeTask(taskId);
                    loadTasks();
                } catch (error) {
                    alert(error.message);
                }
            });
        });

    } catch (error) {
        console.error('Error loading tasks:', error);
        taskList.innerHTML = `
            <div class="col-12 text-center py-5" id="no-tasks-message">
                <h4>Error loading tasks. Please try again.</h4>
                <p class="text-danger">${error.message}</p>
            </div>
        `;
    }
}

async function loadTaskForEdit(taskId) {
    try {
        const task = await api.getTask(taskId);
        
        document.getElementById('task-modal-title').textContent = 'Edit Task';
        document.getElementById('task-id').value = task.id;
        document.getElementById('task-title').value = task.title;
        document.getElementById('task-description').value = task.description || '';
        document.getElementById('task-status').value = task.status;
        
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            document.getElementById('task-due-date').value = dueDate.toISOString().slice(0, 16);
        } else {
            document.getElementById('task-due-date').value = '';
        }
        
        const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
        taskModal.show();
    } catch (error) {
        alert(error.message);
    }
}