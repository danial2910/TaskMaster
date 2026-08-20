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
        <span class="auth__eyebrow">Free to start · No card required</span>
        <h1 class="auth__headline">Start with a clear workspace.</h1>
        <p class="auth__sub">
          Create your account and get every project, task, and deadline in one
          place from day one.
        </p>
      </div>

      <div class="auth__stats">
        <div>
          <div class="auth__stat-value">2 min</div>
          <div class="auth__stat-label">To set up</div>
        </div>
        <div>
          <div class="auth__stat-value">Unlimited</div>
          <div class="auth__stat-label">Tasks</div>
        </div>
      </div>
    </aside>

    <!-- Form -->
    <main class="auth__main">
      <div class="auth__card">
        <h2 class="auth__title">Create your account</h2>
        <p class="auth__caption">A few details and you're in.</p>

        <form @submit.prevent="register" autocomplete="off" class="auth__form">
          <!-- Username -->
          <div class="field" :class="{ focused: focusedField === 'username' }">
            <label for="username">Username</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
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
              <span v-if="form.username && isValidUsername" class="field__affix field__affix--valid">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </div>
          </div>

          <!-- Email -->
          <div class="field" :class="{ focused: focusedField === 'email' }">
            <label for="email">Email address</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input
                v-model="form.email"
                type="email"
                id="email"
                required
                placeholder="you@company.com"
                @focus="focusedField = 'email'"
                @blur="focusedField = null"
              />
              <span v-if="form.email && isValidEmail" class="field__affix field__affix--valid">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </div>
          </div>

          <!-- Full name -->
          <div class="field" :class="{ focused: focusedField === 'full_name' }">
            <label for="full_name">Full name</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                <path d="M23 21V19C22.9986 17.1771 21.7648 15.5857 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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
              <span v-if="form.full_name && isValidFullName" class="field__affix field__affix--valid">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </div>
          </div>

          <!-- Password -->
          <div class="field" :class="{ focused: focusedField === 'password' }">
            <label for="password">Password</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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

            <div v-if="form.password" class="strength">
              <div class="strength__track">
                <div class="strength__fill" :class="passwordStrengthClass" :style="{ width: passwordStrengthWidth }"></div>
              </div>
              <p class="strength__text" :class="passwordStrengthClass">{{ passwordStrengthText }}</p>
            </div>
          </div>

          <!-- Role -->
          <div class="field" :class="{ focused: focusedField === 'role' }">
            <label for="role">Account type</label>
            <div class="field__control">
              <svg class="field__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 7V12C3 16.97 6.84 21.44 12 22C17.16 21.44 21 16.97 21 12V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
              <select
                v-model="form.role"
                id="role"
                required
                @focus="focusedField = 'role'"
                @blur="focusedField = null"
              >
                <option value="staff">Staff member</option>
                <option value="admin">Administrator</option>
              </select>
              <svg class="field__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>

          <!-- Terms -->
          <label class="check">
            <input type="checkbox" v-model="agreeToTerms" required />
            <span class="check__box"></span>
            <span>
              I agree to the <a href="#">Terms of Service</a> and
              <a href="#">Privacy Policy</a>
            </span>
          </label>

          <button type="submit" class="btn btn--primary btn--block" :disabled="isLoading || !isFormValid">
            <svg v-if="isLoading" class="spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.5" stroke-opacity="0.25"/>
              <path d="M12 3C16.9706 3 21 7.02944 21 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span>{{ isLoading ? 'Creating account…' : 'Create account' }}</span>
            <svg v-if="!isLoading" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <div class="divider"><span>or sign up with</span></div>

          <div class="social-row">
            <button type="button" class="btn btn--ghost">
              <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.79 15.71 17.57V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"/>
                <path d="M12 23C15.24 23 17.95 21.92 19.28 20.34L15.71 17.57C14.74 18.22 13.48 18.62 12 18.62C8.91 18.62 6.26 16.68 5.4 13.94H1.72V16.84C3.04 19.46 7.26 23 12 23Z" fill="#34A853"/>
                <path d="M5.4 13.94C5.18 13.29 5.06 12.59 5.06 11.87C5.06 11.15 5.18 10.45 5.4 9.8V6.9H1.72C0.99 8.36 0.58 9.98 0.58 11.87C0.58 13.76 0.99 15.38 1.72 16.84L5.4 13.94Z" fill="#FBBC05"/>
                <path d="M12 5.12C13.62 5.12 15.08 5.68 16.24 6.78L19.43 3.59C17.95 2.22 15.24 1.25 12 1.25C7.26 1.25 3.04 4.79 1.72 7.41L5.4 10.31C6.26 7.57 8.91 5.12 12 5.12Z" fill="#EA4335"/>
              </svg>
              Google
            </button>

            <button type="button" class="btn btn--ghost">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
              </svg>
              GitHub
            </button>
          </div>

          <div class="auth__switch">
            Already have an account?
            <router-link to="/">Sign in</router-link>
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
