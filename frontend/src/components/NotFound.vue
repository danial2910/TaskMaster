<template>
  <div class="not-found-container">
    <div class="not-found-content">
      <div class="error-illustration">
        <div class="error-code">404</div>
        <div class="error-icon">🔍</div>
      </div>
      
      <div class="error-text">
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist or has been moved.</p>
      </div>
      
      <div class="error-actions">
        <router-link to="/" class="primary-btn">
          <i class="fas fa-home"></i>
          Go Home
        </router-link>
        <router-link to="/dashboard" class="secondary-btn" v-if="isAuthenticated">
          <i class="fas fa-tachometer-alt"></i>
          Dashboard
        </router-link>
        <button @click="goBack" class="secondary-btn">
          <i class="fas fa-arrow-left"></i>
          Go Back
        </button>
      </div>
      
      <div class="helpful-links">
        <h3>Popular Pages</h3>
        <div class="links-grid">
          <router-link to="/workspaces" class="link-card" v-if="isAuthenticated">
            <i class="fas fa-building"></i>
            <span>Workspaces</span>
          </router-link>
          <router-link to="/projects" class="link-card" v-if="isAuthenticated">
            <i class="fas fa-folder"></i>
            <span>Projects</span>
          </router-link>
          <router-link to="/tasks" class="link-card" v-if="isAuthenticated">
            <i class="fas fa-tasks"></i>
            <span>Tasks</span>
          </router-link>
          <router-link to="/login" class="link-card" v-if="!isAuthenticated">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
  computed: {
    isAuthenticated() {
      return localStorage.getItem('user') && localStorage.getItem('token')
    }
  },
  methods: {
    goBack() {
      if (window.history.length > 1) {
        this.$router.go(-1)
      } else {
        this.$router.push('/')
      }
    }
  }
}
</script>

<style scoped>
.not-found-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.not-found-content {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  max-width: 600px;
  width: 100%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.error-illustration {
  margin-bottom: 2rem;
  position: relative;
}

.error-code {
  font-size: 8rem;
  font-weight: 900;
  color: #667eea;
  line-height: 1;
  margin-bottom: 1rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-text h1 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 2.5rem;
  font-weight: 700;
}

.error-text p {
  margin: 0 0 2rem 0;
  color: #666;
  font-size: 1.2rem;
  line-height: 1.6;
}

.error-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.primary-btn, .secondary-btn {
  padding: 1rem 2rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border: none;
  cursor: pointer;
}

.primary-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.secondary-btn {
  background: #6c757d;
  color: white;
}

.secondary-btn:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.helpful-links {
  border-top: 1px solid #e9ecef;
  padding-top: 2rem;
}

.helpful-links h3 {
  margin: 0 0 1.5rem 0;
  color: #2c3e50;
  font-size: 1.3rem;
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1rem;
}

.link-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 1.5rem 1rem;
  text-decoration: none;
  color: #2c3e50;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.link-card:hover {
  background: #667eea;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.link-card i {
  font-size: 1.5rem;
}

.link-card span {
  font-size: 0.9rem;
  font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
  .not-found-content {
    padding: 2rem;
  }
  
  .error-code {
    font-size: 6rem;
  }
  
  .error-text h1 {
    font-size: 2rem;
  }
  
  .error-text p {
    font-size: 1rem;
  }
  
  .error-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .primary-btn, .secondary-btn {
    width: 100%;
    justify-content: center;
    max-width: 200px;
  }
  
  .links-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .not-found-container {
    padding: 1rem;
  }
  
  .not-found-content {
    padding: 1.5rem;
  }
  
  .error-code {
    font-size: 4rem;
  }
  
  .error-text h1 {
    font-size: 1.7rem;
  }
  
  .links-grid {
    grid-template-columns: 1fr;
  }
}
</style>