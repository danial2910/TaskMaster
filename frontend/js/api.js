const API_BASE_URL = 'http://TaskMaster.local/backend/public'; 

async function makeRequest(url, method = 'GET', data = null, requiresAuth = true) {
    const headers = {
        'Content-Type': 'application/json'
    };

    if (requiresAuth) {
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            throw new Error('No authentication token found');
        }
        headers['Authorization'] = `Bearer ${token}`;
    }

    const config = {
        method,
        headers
    };

    if (data) {
        config.body = JSON.stringify(data);
    }

    const response = await fetch(`${API_BASE_URL}${url}`, config);

    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Request failed');
    }

    return response.json();
}

export const api = {
    // Auth endpoints
    register: (data) => makeRequest('/api/auth/register', 'POST', data, false),
    login: (data) => makeRequest('/api/auth/login', 'POST', data, false),

    // Task endpoints
    getTasks: () => makeRequest('/api/tasks'),
    createTask: (data) => makeRequest('/api/tasks', 'POST', data),
    getTask: (id) => makeRequest(`/api/tasks/${id}`),
    updateTask: (id, data) => makeRequest(`/api/tasks/${id}`, 'PUT', data),
    deleteTask: (id) => makeRequest(`/api/tasks/${id}`, 'DELETE'),
    completeTask: (id) => makeRequest(`/api/tasks/${id}/complete`, 'PUT')
};