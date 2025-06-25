<template>
  <div class="register-container">
    <div class="register-form">
      <div class="logo-section">
        <h1>TaskMaster</h1>
        <p>Join thousands of productive users</p>
      </div>
      
      <h2>Create Your Account</h2>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="username">Username:</label>
          <input
            type="text"
            id="username"
            v-model="username"
            required
            class="form-control"
            placeholder="Choose a unique username"
            minlength="3"
          />
          <small class="form-hint">At least 3 characters</small>
        </div>
        
        <div class="form-group">
          <label for="email">Email (Optional):</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            placeholder="your.email@example.com"
          />
          <small class="form-hint">For account recovery and notifications</small>
        </div>
        
        <div class="form-group">
          <label for="password">Password:</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            class="form-control"
            placeholder="Create a strong password"
            minlength="6"
          />
          <small class="form-hint">At least 6 characters</small>
        </div>
        
        <div class="form-group">
          <label for="confirmPassword">Confirm Password:</label>
          <input
            type="password"
            id="confirmPassword"
            v-model="confirmPassword"
            required
            class="form-control"
            placeholder="Repeat your password"
            :class="{ 'error': confirmPassword && password !== confirmPassword }"
          />
          <small 
            v-if="confirmPassword && password !== confirmPassword" 
            class="form-hint error"
          >
            Passwords do not match
          </small>
        </div>
        
        <div class="terms-section">
          <label class="checkbox-container">
            <input type="checkbox" v-model="agreeToTerms" required>
            <span class="checkmark"></span>
            I agree to the 
            <a href="#" class="link">Terms of Service</a> and 
            <a href="#" class="link">Privacy Policy</a>
          </label>
        </div>
        
        <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
          {{ loading ? 'Creating Account...' : 'Create Account' }}
          <span v-if="loading" class="spinner"></span>
        </button>
      </form>
      
      <div v-if="message" class="message" :class="messageType">
        {{ message }}
      </div>
      
      <div class="divider">
        <span>or</span>
      </div>
      
      <p class="login-link">
        Already have an account? 
        <router-link to="/login" class="link">Sign in here</router-link>
      </p>
      
      <div class="features-preview">
        <div class="feature">
          <span class="feature-icon">📋</span>
          <span>Organize Tasks</span>
        </div>
        <div class="feature">
          <span class="feature-icon">📊</span>
          <span>Track Progress</span>
        </div>
        <div class="feature">
          <span class="feature-icon">🎯</span>
          <span>Meet Deadlines</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      username: '',
      email: '',
      password: '',
      confirmPassword: '',
      agreeToTerms: false,
      message: '',
      messageType: '',
      loading: false
    }
  },
  computed: {
    isFormValid() {
      return this.username.length >= 3 && 
             this.password.length >= 6 && 
             this.password === this.confirmPassword && 
             this.agreeToTerms;
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
    async handleRegister() {
      // Validate passwords match
      if (this.password !== this.confirmPassword) {
        this.message = 'Passwords do not match';
        this.messageType = 'error';
        return;
      }

      if (!this.agreeToTerms) {
        this.message = 'Please agree to the Terms of Service and Privacy Policy';
        this.messageType = 'error';
        return;
      }

      this.loading = true;
      this.message = '';
      
      try {
        const requestBody = {
          username: this.username.trim(),
          password: this.password
        };
        
        // Add email if provided
        if (this.email.trim()) {
          requestBody.email = this.email.trim();
        }

        const response = await fetch('http://localhost/TaskMaster/backend/api.php?action=register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(requestBody)
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
          this.message = 'Account created successfully! Redirecting to login...';
          this.messageType = 'success';
          
          // Clear form
          this.username = '';
          this.email = '';
          this.password = '';
          this.confirmPassword = '';
          this.agreeToTerms = false;
          
          // Redirect to login page
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);
        } else {
          this.message = data.message || 'Registration failed. Please try again.';
          this.messageType = 'error';
        }
      } catch (error) {
        this.message = 'Connection error. Please check your internet connection and try again.';
        this.messageType = 'error';
        console.error('Registration error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.register-form {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 500px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  max-height: 90vh;
  overflow-y: auto;
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

.register-form h2 {
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

.form-control.error {
  border-color: #e74c3c;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.form-control::placeholder {
  color: #adb5bd;
}

.form-hint {
  display: block;
  font-size: 0.8rem;
  color: #666;
  margin-top: 0.25rem;
}

.form-hint.error {
  color: #e74c3c;
  font-weight: 600;
}

.terms-section {
  margin-bottom: 1.5rem;
}

.checkbox-container {
  display: flex;
  align-items: flex-start;
  cursor: pointer;
  font-size: 0.9rem;
  color: #2c3e50;
  line-height: 1.4;
  gap: 0.75rem;
}

.checkbox-container input {
  display: none;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #ddd;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-container input:checked + .checkmark {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-color: #667eea;
}

.checkbox-container input:checked + .checkmark::after {
  content: '✓';
  color: white;
  font-weight: bold;
  font-size: 0.8rem;
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
  background: #6c757d;
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

.login-link {
  text-align: center;
  margin: 0 0 1.5rem 0;
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

.features-preview {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

.feature {
  text-align: center;
  font-size: 0.8rem;
  color: #666;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.feature-icon {
  font-size: 1.2rem;
  margin-bottom: 0.25rem;
}

/* Responsive Design */
@media (max-width: 480px) {
  .register-container {
    padding: 1rem;
  }
  
  .register-form {
    padding: 2rem;
    border-radius: 15px;
    max-width: 100%;
  }
  
  .logo-section h1 {
    font-size: 2rem;
  }
  
  .register-form h2 {
    font-size: 1.5rem;
  }
  
  .form-control {
    padding: 0.875rem;
  }
  
  .btn {
    padding: 0.875rem;
    font-size: 1rem;
  }
  
  .features-preview {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .feature {
    flex-direction: row;
    justify-content: center;
    gap: 0.5rem;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .register-form {
    background: rgba(44, 62, 80, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .register-form h2 {
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
  
  .form-hint {
    color: #bdc3c7;
  }
  
  .checkbox-container {
    color: #ecf0f1;
  }
  
  .checkmark {
    border-color: rgba(255, 255, 255, 0.3);
  }
  
  .login-link {
    color: #bdc3c7;
  }
  
  .feature {
    color: #bdc3c7;
  }
  
  .divider span {
    background: rgba(44, 62, 80, 0.95);
    color: #bdc3c7;
  }
}

/* Loading state */
.form-control:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Success animation */
.message.success {
  animation: successPulse 0.6s ease-out;
}

@keyframes successPulse {
  0% { transform: scale(0.95); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}

/* Form validation styles */
.form-group.valid .form-control {
  border-color: #28a745;
}

.form-group.valid .form-control:focus {
  box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}
</style>