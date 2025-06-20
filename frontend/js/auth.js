import { api } from './api.js';

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const logoutBtn = document.getElementById('logout-btn');
    const authButtons = document.getElementById('auth-buttons');
    const userMenu = document.getElementById('user-menu');
    const usernameDisplay = document.getElementById('username-display');

    // Check if user is already logged in
    if (localStorage.getItem('jwt_token')) {
        const userData = JSON.parse(localStorage.getItem('user_data'));
        updateUIForLoggedInUser(userData);
        loadTasks();
    }

    // Login form submission
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const response = await api.login({ email, password });
                
                // Store token and user data
                localStorage.setItem('jwt_token', response.token);
                
                // Decode token to get user info (simplified)
                const tokenPayload = JSON.parse(atob(response.token.split('.')[1]));
                const userData = {
                    name: tokenPayload.name,
                    email: tokenPayload.email
                };
                localStorage.setItem('user_data', JSON.stringify(userData));
                
                updateUIForLoggedInUser(userData);
                loadTasks();
                
                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
                loginForm.reset();
            } catch (error) {
                document.getElementById('login-error').textContent = error.message;
                document.getElementById('login-error').classList.remove('d-none');
            }
        });
    }

    // Register form submission
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;

            try {
                await api.register({ name, email, password });
                
                // Auto-login after registration
                const loginResponse = await api.login({ email, password });
                localStorage.setItem('jwt_token', loginResponse.token);
                
                const tokenPayload = JSON.parse(atob(loginResponse.token.split('.')[1]));
                const userData = {
                    name: tokenPayload.name,
                    email: tokenPayload.email
                };
                localStorage.setItem('user_data', JSON.stringify(userData));
                
                updateUIForLoggedInUser(userData);
                loadTasks();
                
                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
                registerForm.reset();
            } catch (error) {
                document.getElementById('register-error').textContent = error.message;
                document.getElementById('register-error').classList.remove('d-none');
            }
        });
    }

    // Logout button
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_data');
            updateUIForLoggedOutUser();
            document.getElementById('task-list').innerHTML = `
                <div class="col-12 text-center py-5" id="no-tasks-message">
                    <h4>Please login to view your tasks</h4>
                </div>
            `;
            document.getElementById('add-task-btn').style.display = 'none';
        });
    }

    function updateUIForLoggedInUser(userData) {
        authButtons.classList.add('d-none');
        userMenu.classList.remove('d-none');
        usernameDisplay.textContent = userData.name;
        document.getElementById('add-task-btn').style.display = 'block';
    }

    function updateUIForLoggedOutUser() {
        authButtons.classList.remove('d-none');
        userMenu.classList.add('d-none');
        document.getElementById('add-task-btn').style.display = 'none';
    }
});

export function loadTasks() {
    // This will be implemented in tasks.js
}