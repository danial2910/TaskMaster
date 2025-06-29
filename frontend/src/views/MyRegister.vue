<template>
  <div class="page-container">
    <!-- Animated background -->
    <div class="background-animation">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
      <div class="floating-shape shape-4"></div>
      <div class="floating-shape shape-5"></div>
      <div class="floating-shape shape-6"></div>
    </div>

    <div class="register-container">
      <!-- Header Section -->
      <div class="brand-section">
        <div class="logo-circle">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M20 8V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M23 11H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h1 class="brand-title">Join Us Today</h1>
        <p class="brand-subtitle">Create your account to get started</p>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="register" autocomplete="off" class="register-form">
        <!-- Username Field -->
        <div class="form-group" :class="{ 'focused': focusedField === 'username' }">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input 
              v-model="form.username" 
              type="text" 
              id="username" 
              required 
              placeholder="Choose a username"
              @focus="focusedField = 'username'"
              @blur="focusedField = null"
            />
            <div class="validation-icon" v-if="form.username">
              <svg v-if="isValidUsername" class="valid-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Email Field -->
        <div class="form-group" :class="{ 'focused': focusedField === 'email' }">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input 
              v-model="form.email" 
              type="email" 
              id="email" 
              required 
              placeholder="Enter your email"
              @focus="focusedField = 'email'"
              @blur="focusedField = null"
            />
            <div class="validation-icon" v-if="form.email">
              <svg v-if="isValidEmail" class="valid-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Full Name Field -->
        <div class="form-group" :class="{ 'focused': focusedField === 'full_name' }">
          <label for="full_name">Full Name</label>
          <div class="input-wrapper">
            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M23 21V19C23 18.1645 22.7155 17.3541 22.2094 16.6977C21.7033 16.0414 20.9979 15.5776 20.2 15.3742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45768C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input 
              v-model="form.full_name" 
              type="text" 
              id="full_name" 
              required 
              placeholder="Enter your full name"
              @focus="focusedField = 'full_name'"
              @blur="focusedField = null"
            />
            <div class="validation-icon" v-if="form.full_name">
              <svg v-if="isValidFullName" class="valid-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Password Field -->
        <div class="form-group" :class="{ 'focused': focusedField === 'password' }">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              required 
              placeholder="Create a password"
              @focus="focusedField = 'password'"
              @blur="focusedField = null"
            />
            <button 
              type="button" 
              class="password-toggle"
              @click="showPassword = !showPassword"
            >
              <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 4.028 7.66497 6.17 6.17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.9 4.24C10.5883 4.0789 11.2931 3.99836 12 4C19 4 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
          
          <!-- Password Strength Indicator -->
          <div v-if="form.password" class="password-strength">
            <div class="strength-bar">
              <div class="strength-fill" :class="passwordStrengthClass" :style="{ width: passwordStrengthWidth }"></div>
            </div>
            <p class="strength-text" :class="passwordStrengthClass">{{ passwordStrengthText }}</p>
          </div>
        </div>

        <!-- Role Field -->
        <div class="form-group" :class="{ 'focused': focusedField === 'role' }">
          <label for="role">Account Type</label>
          <div class="select-wrapper">
            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M23 21V19C23 18.1645 22.7155 17.3541 22.2094 16.6977C21.7033 16.0414 20.9979 15.5776 20.2 15.3742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45768C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <select 
              v-model="form.role" 
              id="role" 
              required
              @focus="focusedField = 'role'"
              @blur="focusedField = null"
            >
              <option value="staff">Staff Member</option>
              <option value="admin">Administrator</option>
            </select>
            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" v-model="agreeToTerms" required>
            <span class="checkmark"></span>
            <span class="checkbox-text">
              I agree to the 
              <a href="#" class="terms-link">Terms of Service</a> 
              and 
              <a href="#" class="terms-link">Privacy Policy</a>
            </span>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="register-btn" :disabled="isLoading || !isFormValid">
          <div class="btn-content">
            <svg v-if="isLoading" class="loading-spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 3C16.9706 3 21 7.02944 21 12" stroke="url(#spinner-gradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="spinner-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" style="stop-color:currentColor;stop-opacity:1" />
                  <stop offset="100%" style="stop-color:currentColor;stop-opacity:0" />
                </linearGradient>
              </defs>
            </svg>
            <span v-if="!isLoading">Create Account</span>
            <span v-else>Creating Account...</span>
          </div>
        </button>

        <!-- Social Registration -->
        <div class="divider">
          <span>or sign up with</span>
        </div>

        <div class="social-buttons">
          <button type="button" class="social-btn google-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.79 15.71 17.57V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"/>
              <path d="M12 23C15.24 23 17.95 21.92 19.28 20.34L15.71 17.57C14.74 18.22 13.48 18.62 12 18.62C8.91 18.62 6.26 16.68 5.4 13.94H1.72V16.84C3.04 19.46 7.26 23 12 23Z" fill="#34A853"/>
              <path d="M5.4 13.94C5.18 13.29 5.06 12.59 5.06 11.87C5.06 11.15 5.18 10.45 5.4 9.8V6.9H1.72C0.99 8.36 0.58 9.98 0.58 11.87C0.58 13.76 0.99 15.38 1.72 16.84L5.4 13.94Z" fill="#FBBC05"/>
              <path d="M12 5.12C13.62 5.12 15.08 5.68 16.24 6.78L19.43 3.59C17.95 2.22 15.24 1.25 12 1.25C7.26 1.25 3.04 4.79 1.72 7.41L5.4 10.31C6.26 7.57 8.91 5.12 12 5.12Z" fill="#EA4335"/>
            </svg>
            Google
          </button>
          
          <button type="button" class="social-btn github-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 19C4 20.5 4 16.5 2 16M22 16V19.5C22 20.6046 21.1046 21.5 20 21.5H19C18.4477 21.5 18 21.0523 18 20.5V18.5C18 17.5 18 17 18 16C18 15 17 14 16 14C17 14 18 13 18 12V9C18 7.89543 17.1046 7 16 7H15C13.8954 7 13 7.89543 13 9V12C13 13 14 14 15 14C14 14 13 15 13 16C13 17 13 17.5 13 18.5V20.5C13 21.0523 12.5523 21.5 12 21.5H11C9.89543 21.5 9 20.6046 9 19.5V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            GitHub
          </button>
        </div>

        <!-- Login Link -->
        <div class="login-link">
          Already have an account?
          <router-link to="/">Sign in here</router-link>
        </div>
      </form>
    </div>

    <!-- Toast Messages -->
    <transition name="slide-down">
      <div v-if="successMessage" class="message success-msg">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4905 2.02168 11.3363C2.16356 9.18219 2.99721 7.13008 4.39828 5.49618C5.79935 3.86229 7.69279 2.72845 9.79619 2.24664C11.8996 1.76482 14.1003 1.95624 16.07 2.79L17.59 3.47" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ successMessage }}
      </div>
    </transition>

    <transition name="slide-down">
      <div v-if="errorMessage" class="message error-msg">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'MyRegister',
  data() {
    return {
      form: {
        username: '',
        password: '',
        email: '',
        role: 'staff',
        full_name: ''
      },
      showPassword: false,
      agreeToTerms: false,
      focusedField: null,
      errorMessage: '',
      successMessage: '',
      isLoading: false
    };
  },
  computed: {
    isValidEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(this.form.email);
    },
    isValidUsername() {
      return this.form.username.length >= 3;
    },
    isValidFullName() {
      return this.form.full_name.trim().length >= 2;
    },
    passwordStrength() {
      const password = this.form.password;
      let score = 0;
      
      if (password.length >= 8) score++;
      if (/[a-z]/.test(password)) score++;
      if (/[A-Z]/.test(password)) score++;
      if (/[0-9]/.test(password)) score++;
      if (/[^A-Za-z0-9]/.test(password)) score++;
      
      return score;
    },
    passwordStrengthClass() {
      switch (this.passwordStrength) {
        case 0:
        case 1:
          return 'weak';
        case 2:
        case 3:
          return 'medium';
        case 4:
        case 5:
          return 'strong';
        default:
          return 'weak';
      }
    },
    passwordStrengthWidth() {
      return `${(this.passwordStrength / 5) * 100}%`;
    },
    passwordStrengthText() {
      switch (this.passwordStrength) {
        case 0:
        case 1:
          return 'Weak password';
        case 2:
        case 3:
          return 'Medium strength';
        case 4:
        case 5:
          return 'Strong password';
        default:
          return '';
      }
    },
    isFormValid() {
      return (
        this.isValidUsername &&
        this.isValidEmail &&
        this.isValidFullName &&
        this.form.password.length >= 6 &&
        this.agreeToTerms
      );
    }
  },
  methods: {
    async register() {
      if (!this.isFormValid) return;
      
      this.errorMessage = '';
      this.successMessage = '';
      this.isLoading = true;
      
      try {
        const res = await fetch('http://localhost:8000/api/auth/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        });
        const data = await res.json();
        
        if (res.ok && data.message) {
          this.successMessage = data.message + ' You can now sign in to your account.';
          this.form = { username: '', password: '', email: '', role: 'staff', full_name: '' };
          this.agreeToTerms = false;
          
          // Redirect to login after 2 seconds
          setTimeout(() => {
            this.$router.push('/');
          }, 2000);
        } else {
          this.errorMessage = data.error || 'Registration failed. Please try again.';
        }
      } catch {
        this.errorMessage = 'Network error. Please check your connection and try again.';
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
  padding: 20px;
}

.background-animation {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.floating-shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 8s ease-in-out infinite;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 120px;
  height: 120px;
  top: 20%;
  right: 20%;
  animation-delay: 2s;
}

.shape-3 {
  width: 60px;
  height: 60px;
  top: 50%;
  left: 5%;
  animation-delay: 4s;
}

.shape-4 {
  width: 100px;
  height: 100px;
  top: 70%;
  right: 10%;
  animation-delay: 1s;
}

.shape-5 {
  width: 140px;
  height: 140px;
  top: 60%;
  left: 20%;
  animation-delay: 3s;
}

.shape-6 {
  width: 90px;
  height: 90px;
  top: 30%;
  right: 5%;
  animation-delay: 5s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-30px) rotate(180deg);
  }
}

.register-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 48px 40px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  z-index: 1;
  max-height: 95vh;
  overflow-y: auto;
}

.brand-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo-circle {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  color: white;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.brand-title {
  font-size: 2rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 8px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-subtitle {
  color: #718096;
  font-size: 1rem;
  margin: 0;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: all 0.3s ease;
}

.form-group.focused {
  transform: translateY(-2px);
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 4px;
}

.input-wrapper,
.select-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  color: #a0aec0;
  z-index: 2;
  transition: color 0.3s ease;
}

.form-group.focused .input-icon {
  color: #667eea;
}

.input-wrapper input,
.select-wrapper select {
  width: 100%;
  padding: 16px 16px 16px 48px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  background: #f8fafc;
  transition: all 0.3s ease;
  color: #2d3748;
}

.select-wrapper select {
  appearance: none;
  cursor: pointer;
}

.select-arrow {
  position: absolute;
  right: 16px;
  color: #a0aec0;
  pointer-events: none;
}

.input-wrapper input:focus,
.select-wrapper select:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-toggle {
  position: absolute;
  right: 16px;
  background: none;
  border: none;
  color: #a0aec0;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.3s ease;
}

.password-toggle:hover {
  color: #667eea;
}

.validation-icon {
  position: absolute;
  right: 16px;
}

.valid-icon {
  color: #48bb78;
}

.password-strength {
  margin-top: 8px;
}

.strength-bar {
  width: 100%;
  height: 4px;
  background: #e2e8f0;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 4px;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.strength-fill.weak {
  background: #f56565;
}

.strength-fill.medium {
  background: #ed8936;
}

.strength-fill.strong {
  background: #48bb78;
}

.strength-text {
  font-size: 0.75rem;
  font-weight: 500;
}

.strength-text.weak {
  color: #f56565;
}

.strength-text.medium {
  color: #ed8936;
}

.strength-text.strong {
  color: #48bb78;
}

.checkbox-group {
  margin: 8px 0;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #4a5568;
  line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #e2e8f0;
  border-radius: 4px;
  position: relative;
  transition: all 0.3s ease;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-label input:checked + .checkmark {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: #667eea;
}

.checkbox-label input:checked + .checkmark::after {
  content: '';
  position: absolute;
  left: 6px;
  top: 2px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-text {
  flex: 1;
}

.terms-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.terms-link:hover {
  color: #764ba2;
  text-decoration: underline;
}

.register-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 16px 32px;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  position: relative;
  overflow: hidden;
}

.register-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

.register-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.loading-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.divider {
  text-align: center;
  position: relative;
  margin: 24px 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e2e8f0;
}

.divider span {
  background: rgba(255, 255, 255, 0.95);
  padding: 0 16px;
  color: #a0aec0;
  font-size: 0.9rem;
  position: relative;
}

.social-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  background: white;
  color: #4a5568;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.social-btn:hover {
  border-color: #cbd5e0;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.login-link {
  text-align: center;
  margin-top: 32px;
  color: #718096;
  font-size: 0.95rem;
}

.login-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
  transition: color 0.3s ease;
}

.login-link a:hover {
  color: #764ba2;
  text-decoration: underline;
}

.message {
  position: fixed;
  top: 24px;
  right: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  border-radius: 12px;
  font-size: 0.95rem;
  font-weight: 500;
  z-index: 1100;
  max-width: 400px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
}

.success-msg {
  background: #f0fff4;
  color: #22543d;
  border: 1px solid #9ae6b4;
}

.error-msg {
  background: #fed7d7;
  color: #742a2a;
  border: 1px solid #feb2b2;
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsive design */
@media (max-width: 480px) {
  .page-container {
    padding: 16px;
  }
  
  .register-container {
    padding: 32px 24px;
    max-height: 98vh;
  }
  
  .brand-title {
    font-size: 1.75rem;
  }
  
  .logo-circle {
    width: 64px;
    height: 64px;
  }
  
  .social-buttons {
    grid-template-columns: 1fr;
  }
  
  .message {
    position: fixed;
    top: 16px;
    left: 16px;
    right: 16px;
    max-width: none;
  }
}

@media (max-height: 700px) {
  .register-container {
    padding: 24px;
    margin: 10px 0;
  }
  
  .brand-section {
    margin-bottom: 24px;
  }
  
  .form-group {
    gap: 16px;
  }
}</style>