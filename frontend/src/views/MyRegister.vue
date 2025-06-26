<template>
  <div class="register-container">
    <h2>Create Your Account</h2>
    <form @submit.prevent="register" autocomplete="off">
      <div class="form-row">
        <label for="username">Username</label>
        <input v-model="form.username" id="username" required placeholder="Enter username" />
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input v-model="form.password" id="password" type="password" required placeholder="Enter password" />
      </div>
      <div class="form-row">
        <label for="email">Email</label>
        <input v-model="form.email" id="email" type="email" required placeholder="Enter email" />
      </div>
      <div class="form-row">
        <label for="full_name">Full Name</label>
        <input v-model="form.full_name" id="full_name" required placeholder="Enter your full name" />
      </div>
      <div class="form-row">
        <label for="role">Role</label>
        <select v-model="form.role" id="role" required>
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="register-btn">Register</button>
    </form>
    <div v-if="errorMessage" class="error-msg">{{ errorMessage }}</div>
    <div v-if="successMessage" class="success-msg">{{ successMessage }}</div>
    <div class="login-link">
      Already have an account?
      <router-link to="/">Login here</router-link>
    </div>
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
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async register() {
      this.errorMessage = '';
      this.successMessage = '';
      try {
        const res = await fetch('http://localhost:8000/api/auth/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        });
        const data = await res.json();
        if (res.ok && data.message) {
          this.successMessage = data.message + ' You can now log in.';
          this.form = { username: '', password: '', email: '', role: 'staff', full_name: '' };
        } else {
          this.errorMessage = data.error || 'Registration failed.';
        }
      } catch {
        this.errorMessage = 'Network error. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
.register-container {
  max-width: 420px;
  margin: 40px auto;
  padding: 32px 28px 24px 28px;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 6px 24px rgba(0,0,0,0.10);
  border: 1px solid #e3e3e3;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

h2 {
  text-align: center;
  color: #007bff;
  margin-bottom: 28px;
  font-weight: 700;
  letter-spacing: 1px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.form-row {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  font-size: 1rem;
  color: #333;
  font-weight: 500;
}

input, select {
  padding: 9px 12px;
  border: 1px solid #bfc9d1;
  border-radius: 5px;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s;
}

input:focus, select:focus {
  border: 1.5px solid #007bff;
  outline: none;
  background: #fff;
}

.register-btn {
  margin-top: 10px;
  padding: 11px 0;
  background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
  color: #fff;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  font-size: 1.08rem;
  cursor: pointer;
  transition: background 0.2s;
  box-shadow: 0 2px 8px rgba(0,123,255,0.08);
}

.register-btn:hover {
  background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
}

.error-msg, .success-msg {
  margin-top: 18px;
  text-align: center;
  font-size: 1rem;
  font-weight: 500;
  border-radius: 4px;
  padding: 8px 0;
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

.login-link {
  margin-top: 22px;
  text-align: center;
  font-size: 1rem;
}

.login-link a {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>