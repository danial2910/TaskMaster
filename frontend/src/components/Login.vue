<template>
  <div class="login-container">
    <div class="login-form">
      <div class="logo-section">
        <h1>TaskMaster</h1>
        <p>Manage your tasks efficiently</p>
      </div>
      
      <h2>Welcome Back</h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="username">Username:</label>
          <input
            type="text"
            id="username"
            v-model="username"
            required
            class="form-control"
            placeholder="Enter your username"
          />
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            class="form-control"
            placeholder="Enter your password"
          />
        </div>
        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
          <span v-if="loading" class="spinner"></span>
        </button>
      </form>
      
      <div v-if="message" class="message" :class="messageType">
        {{ message }}
      </div>
      
      <div class="divider">
        <span>or</span>
      </div>
      
      <p class="register-link">
        Don't have an account? 
        <router-link to="/register" class="link">Create one here</router-link>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      username: '',
      password: '',
      message: '',
      messageType: '',
      loading: false
    }
  },
  mounted() {
    // Check if user is already logged in
    const user = localStorage.getItem('user');
    const token = localStorage.getItem('token');
    if (user && token) {
      this.$router.push('/dashboard');
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.message = '';
      
      try {
        const response = await fetch('http://localhost/TaskMaster/backend/api.php?action=login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            username: this.username,
            password: this.password
          })
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (parseError) {
          console.error('Failed to parse JSON:', parseError);
          console.error('Response was:', responseText);
          this.message = 'Invalid response from server';
          this.messageType = 'error';
          return;
        }
        
        if (data.status === 'success') {
          this.message = 'Login successful! Redirecting...';
          this.messageType = 'success';
          
          // Store user data and token
          localStorage.setItem('user', JSON.stringify(data.user));
          localStorage.setItem('token', data.token);
          
          // Redirect to dashboard
          setTimeout(() => {
            this.$router.push('/dashboard');
          }, 1000);
        } else {
          this.message = data.message || 'Invalid username or password';
          this.messageType = 'error';
        }
      } catch (error) {
        this.message = 'Connection error. Please try again.';
        this.messageType = 'error';
        console.error('Login error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-form {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 450px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.logo-section {
  text-align: center;
  margin-bottom: 2rem;
}

.logo-section h1 {
  margin: 0;
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.logo-section p {
  margin: 0.5rem 0 0 0;
  color: #666;
  font-size: 1.1rem;
}

.login-form h2 {
  text-align: center;
  margin-bottom: 2rem;
  color: #2c3e50;
  font-size: 1.8rem;
  font-weight: 600;
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
  transform: translateY(-2px);
}

.form-control::placeholder {
  color: #adb5bd;
}

.btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

.btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn:active {
  transform: translateY(0);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.message {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 12px;
  text-align: center;
  font-weight: 500;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message.success {
  background: rgba(46, 204, 113, 0.1);
  color: #27ae60;
  border: 2px solid rgba(46, 204, 113, 0.2);
}

.message.error {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c;
  border: 2px solid rgba(231, 76, 60, 0.2);
}

.divider {
  text-align: center;
  margin: 2rem 0;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e9ecef;
}

.divider span {
  background: rgba(255, 255, 255, 0.95);
  padding: 0 1rem;
  color: #666;
  font-size: 0.9rem;
}

.register-link {
  text-align: center;
  margin: 0;
  color: #666;
  font-size: 0.95rem;
}

.link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.link:hover {
  color: #5a6fd8;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 480px) {
  .login-container {
    padding: 1rem;
  }
  
  .login-form {
    padding: 2rem;
    border-radius: 15px;
  }
  
  .logo-section h1 {
    font-size: 2rem;
  }
  
  .login-form h2 {
    font-size: 1.5rem;
  }
  
  .form-control {
    padding: 0.875rem;
  }
  
  .btn {
    padding: 0.875rem;
    font-size: 1rem;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .login-form {
    background: rgba(44, 62, 80, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .login-form h2 {
    color: #ecf0f1;
  }
  
  .form-group label {
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
  
  .form-control::placeholder {
    color: #95a5a6;
  }
  
  .register-link {
    color: #bdc3c7;
  }
  
  .divider span {
    background: rgba(44, 62, 80, 0.95);
    color: #bdc3c7;
  }
}
</style>