<template>
  <div class="login-container">
    <h2>Login</h2>
    <form @submit.prevent="login" autocomplete="off">
      <div class="form-row">
        <label for="username">Username</label>
        <input v-model="username" type="text" id="username" required placeholder="Enter your username" />
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input v-model="password" type="password" id="password" required placeholder="Enter your password" />
      </div>
      <button type="submit" class="login-btn">Login</button>
      <div class="register-link">
        Don't have an account?
        <router-link to="/register">Register here</router-link>
      </div>
    </form>
    <div v-if="successMessage" class="success-msg">{{ successMessage }}</div>
    <div v-if="errorMessage" class="error-msg">{{ errorMessage }}</div>
  </div>
</template>

<script>
export default {
  name: 'MyLogin',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async login() {
      this.errorMessage = '';
      this.successMessage = '';
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
          this.successMessage = `Login successful! Welcome, ${data.user.username}. Redirecting...`;
          localStorage.setItem('jwt_token', data.token);
          localStorage.setItem('user_info', JSON.stringify(data.user));
          this.$router.push('/dashboard');
        } else {
          this.errorMessage = data.error || 'Login failed. Please check your credentials.';
        }
      } catch (err) {
        this.errorMessage = 'Network error. Could not connect to the server. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
.login-container {
  max-width: 420px;
  margin: 40px auto;
  padding: 32px 28px 24px 28px;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
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
input {
  padding: 9px 12px;
  border: 1px solid #bfc9d1;
  border-radius: 5px;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s;
}
input:focus {
  border: 1.5px solid #007bff;
  outline: none;
  background: #fff;
}
.login-btn {
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
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.08);
}
.login-btn:hover {
  background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
}
.error-msg,
.success-msg {
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
.register-link {
  margin-top: 22px;
  text-align: center;
  font-size: 1rem;
}
.register-link a {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
}
.register-link a:hover {
  text-decoration: underline;
}
</style>