<template>
  <div class="auth">
    <!-- Brand panel -->
    <aside class="auth__aside">
      <div class="auth__beams">
        <span class="auth__beam"></span>
        <span class="auth__beam"></span>
        <span class="auth__beam"></span>
        <span class="auth__beam"></span>
        <span class="auth__beam"></span>
        <span class="auth__beam"></span>
      </div>
      <div class="auth__grid"></div>

      <div class="auth__brand">
        <span class="auth__mark">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        TaskMaster
      </div>

      <div class="auth__pitch">
        <span class="auth__eyebrow">Task management, made calm</span>
        <h1 class="auth__headline">Your work, without the overwhelm.</h1>
        <p class="auth__sub">
          Organize tasks, track deadlines, and keep everything in one calm
          workspace no complex setup, no learning curve.
        </p>
      </div>

      <div class="auth__stats">
        <div>
          <div class="auth__stat-value">24</div>
          <div class="auth__stat-label">Open tasks</div>
        </div>
        <div>
          <div class="auth__stat-value">38</div>
          <div class="auth__stat-label">Completed</div>
        </div>
        <div>
          <div class="auth__stat-value">92%</div>
          <div class="auth__stat-label">On track</div>
        </div>
      </div>
    </aside>

    <!-- Form -->
    <main class="auth__main">
      <div class="auth__card">
        <h2 class="auth__title">Welcome back</h2>
        <p class="auth__caption">Sign in to your account to continue.</p>

        <form @submit.prevent="login" autocomplete="off" class="auth__form">
          <div class="field" :class="{ focused: focusedField === 'username' }">
            <label for="username">Username</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
              </svg>
              <input
                v-model="username"
                type="text"
                id="username"
                required
                placeholder="Enter your username"
                @focus="focusedField = 'username'"
                @blur="focusedField = null"
              />
            </div>
          </div>

          <div class="field" :class="{ focused: focusedField === 'password' }">
            <label for="password">Password</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                id="password"
                required
                placeholder="Enter your password"
                @focus="focusedField = 'password'"
                @blur="focusedField = null"
              />
              <button
                type="button"
                class="field__affix"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                @click="showPassword = !showPassword"
              >
                <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 4.028 7.66497 6.17 6.17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9.9 4.24C10.5883 4.0789 11.2931 3.99836 12 4C19 4 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="auth__row">
            <label class="check">
              <input type="checkbox" v-model="rememberMe" />
              <span class="check__box"></span>
              Remember me
            </label>
            <a href="#" class="auth__link">Forgot password?</a>
          </div>

          <button type="submit" class="btn btn--primary btn--block" :disabled="isLoading">
            <svg v-if="isLoading" class="spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.5" stroke-opacity="0.25"/>
              <path d="M12 3C16.9706 3 21 7.02944 21 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span>{{ isLoading ? 'Signing in…' : 'Sign in' }}</span>
            <svg v-if="!isLoading" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <div class="divider"><span>or</span></div>

          <button type="button" class="btn btn--ghost btn--block">
            <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.79 15.71 17.57V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"/>
              <path d="M12 23C15.24 23 17.95 21.92 19.28 20.34L15.71 17.57C14.74 18.22 13.48 18.62 12 18.62C8.91 18.62 6.26 16.68 5.4 13.94H1.72V16.84C3.04 19.46 7.26 23 12 23Z" fill="#34A853"/>
              <path d="M5.4 13.94C5.18 13.29 5.06 12.59 5.06 11.87C5.06 11.15 5.18 10.45 5.4 9.8V6.9H1.72C0.99 8.36 0.58 9.98 0.58 11.87C0.58 13.76 0.99 15.38 1.72 16.84L5.4 13.94Z" fill="#FBBC05"/>
              <path d="M12 5.12C13.62 5.12 15.08 5.68 16.24 6.78L19.43 3.59C17.95 2.22 15.24 1.25 12 1.25C7.26 1.25 3.04 4.79 1.72 7.41L5.4 10.31C6.26 7.57 8.91 5.12 12 5.12Z" fill="#EA4335"/>
            </svg>
            Continue with Google
          </button>

          <div class="auth__switch">
            Don't have an account?
            <router-link to="/register">Create one</router-link>
          </div>
        </form>
      </div>
    </main>

    <!-- Messages -->
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
  name: 'MyLogin',
  data() {
    return {
      username: '',
      password: '',
      showPassword: false,
      rememberMe: false,
      focusedField: null,
      errorMessage: '',
      successMessage: '',
      isLoading: false
    };
  },
  methods: {
    async login() {
      this.errorMessage = '';
      this.successMessage = '';
      this.isLoading = true;

      try {
        const res = await fetch('http://localhost:8000/api/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            username: this.username,
            password: this.password
          })
        });
        const data = await res.json();

        if (res.ok && data.token) {
          this.successMessage = `Welcome back, ${data.user.username}! Redirecting...`;
          localStorage.setItem('jwt_token', data.token);
          localStorage.setItem('user_info', JSON.stringify(data.user));

          // Simulate a brief delay for better UX
          setTimeout(() => {
            this.$router.push('/dashboard');
          }, 1500);
        } else {
          this.errorMessage = data.error || 'Invalid credentials. Please try again.';
        }
      } catch (err) {
        this.errorMessage = 'Connection failed. Please check your network and try again.';
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>
