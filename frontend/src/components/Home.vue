<template>
  <div class="home-container">
    <!-- Navigation -->
    <nav class="navbar">
      <div class="nav-content">
        <div class="logo">
          <h1>TaskMaster</h1>
        </div>
        <div class="nav-links">
          <router-link to="/login" class="nav-btn">Login</router-link>
          <router-link to="/register" class="nav-btn primary">Get Started</router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <div class="hero-text">
          <h1>Master Your Tasks, Achieve Your Goals</h1>
          <p>
            TaskMaster is the ultimate task management solution that helps you organize, 
            prioritize, and complete your work efficiently. Stay on top of your projects 
            and never miss a deadline again.
          </p>
          <div class="hero-buttons">
            <router-link to="/register" class="cta-btn primary">
              Start Free Today
              <i class="fas fa-arrow-right"></i>
            </router-link>
            <router-link to="/login" class="cta-btn secondary">
              Sign In
            </router-link>
          </div>
        </div>
        <div class="hero-image">
          <div class="task-card demo">
            <div class="task-header">
              <h3>Complete Project Proposal</h3>
              <span class="status high">High Priority</span>
            </div>
            <p>Finalize the Q1 project proposal and submit to management</p>
            <div class="task-meta">
              <span class="due-date">Due: Today</span>
              <span class="progress">75% Complete</span>
            </div>
          </div>
          <div class="task-card demo delay-1">
            <div class="task-header">
              <h3>Team Meeting</h3>
              <span class="status medium">Medium</span>
            </div>
            <p>Weekly standup with development team</p>
            <div class="task-meta">
              <span class="due-date">Due: Tomorrow</span>
              <span class="progress">Ready</span>
            </div>
          </div>
          <div class="task-card demo delay-2">
            <div class="task-header">
              <h3>Code Review</h3>
              <span class="status low">Low Priority</span>
            </div>
            <p>Review pull requests from team members</p>
            <div class="task-meta">
              <span class="due-date">Due: Friday</span>
              <span class="progress">Pending</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="container">
        <h2>Why Choose TaskMaster?</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>Task Organization</h3>
            <p>Organize your tasks with projects, priorities, and due dates. Keep everything structured and easy to find.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Progress Tracking</h3>
            <p>Monitor your progress with visual indicators and completion statistics. Stay motivated as you achieve your goals.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">💬</div>
            <h3>Team Collaboration</h3>
            <p>Add comments and notes to tasks. Collaborate effectively with your team members and stakeholders.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Priority Management</h3>
            <p>Set task priorities to focus on what matters most. Never lose sight of your most important work.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Responsive Design</h3>
            <p>Access your tasks anywhere, anytime. Our responsive design works perfectly on all devices.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Fast & Reliable</h3>
            <p>Lightning-fast performance with real-time updates. Your data is always secure and accessible.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">10,000+</div>
            <div class="stat-label">Tasks Completed</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Active Users</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Uptime</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Ready to Get Organized?</h2>
          <p>Join thousands of users who have transformed their productivity with TaskMaster.</p>
          <router-link to="/register" class="cta-btn large primary">
            Start Your Free Account
            <i class="fas fa-rocket"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-section">
            <h3>TaskMaster</h3>
            <p>The ultimate task management solution for individuals and teams.</p>
          </div>
          <div class="footer-section">
            <h4>Features</h4>
            <ul>
              <li>Task Management</li>
              <li>Project Organization</li>
              <li>Team Collaboration</li>
              <li>Progress Tracking</li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Support</h4>
            <ul>
              <li>Help Center</li>
              <li>Contact Us</li>
              <li>Documentation</li>
              <li>Community</li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Company</h4>
            <ul>
              <li>About Us</li>
              <li>Privacy Policy</li>
              <li>Terms of Service</li>
              <li>Blog</li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2024 TaskMaster. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'Home',
  mounted() {
    // Check if user is already logged in and redirect to dashboard
    const user = localStorage.getItem('user');
    const token = localStorage.getItem('token');
    if (user && token) {
      this.$router.push('/dashboard');
    }
  }
}
</script>

<style scoped>
.home-container {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: #2c3e50;
}

/* Navigation */
.navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 1rem 0;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.nav-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
}

.logo h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.nav-links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.nav-btn {
  padding: 0.5rem 1.5rem;
  text-decoration: none;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.nav-btn:not(.primary) {
  color: #2c3e50;
}

.nav-btn:not(.primary):hover {
  color: #667eea;
  border-color: #667eea;
}

.nav-btn.primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.nav-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 120px 0 80px;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
}

.hero-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

.hero-text h1 {
  font-size: 3.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-text p {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 2.5rem;
  line-height: 1.8;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.cta-btn {
  padding: 1rem 2rem;
  text-decoration: none;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  border: 2px solid transparent;
}

.cta-btn.primary {
  background: white;
  color: #667eea;
}

.cta-btn.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.cta-btn.secondary {
  background: transparent;
  color: white;
  border-color: rgba(255, 255, 255, 0.5);
}

.cta-btn.secondary:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: white;
}

.cta-btn.large {
  padding: 1.25rem 2.5rem;
  font-size: 1.2rem;
}

.hero-image {
  position: relative;
  height: 500px;
}

.task-card.demo {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 1.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  position: absolute;
  width: 280px;
  animation: float 6s ease-in-out infinite;
}

.task-card.demo:nth-child(1) {
  top: 50px;
  left: 0;
}

.task-card.demo:nth-child(2) {
  top: 200px;
  right: 0;
  animation-delay: -2s;
}

.task-card.demo:nth-child(3) {
  bottom: 50px;
  left: 50px;
  animation-delay: -4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.task-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.1rem;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status.high {
  background: #fee2e2;
  color: #dc2626;
}

.status.medium {
  background: #fef3c7;
  color: #d97706;
}

.status.low {
  background: #dcfce7;
  color: #16a34a;
}

.task-card p {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.task-meta {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #888;
}

/* Features Section */
.features {
  padding: 100px 0;
  background: #f8fafc;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.features h2 {
  text-align: center;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 3rem;
  color: #2c3e50;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.feature-card h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #2c3e50;
}

.feature-card p {
  color: #666;
  line-height: 1.6;
}

/* Stats Section */
.stats {
  padding: 80px 0;
  background: linear-gradient(135deg, #667eea, #764ba2);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
}

.stat-item {
  text-align: center;
  color: white;
}

.stat-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1.1rem;
  opacity: 0.9;
}

/* CTA Section */
.cta-section {
  padding: 100px 0;
  background: #2c3e50;
}

.cta-content {
  text-align: center;
  color: white;
}

.cta-content h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.cta-content p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

/* Footer */
.footer {
  background: #1a252f;
  color: white;
  padding: 60px 0 20px;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-section h3 {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #667eea;
}

.footer-section h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.footer-section p {
  opacity: 0.8;
  line-height: 1.6;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section li {
  margin-bottom: 0.5rem;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.footer-section li:hover {
  opacity: 1;
  cursor: pointer;
}

.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  opacity: 0.6;
}

/* Responsive Design */
@media (max-width: 768px) {
  .nav-content {
    padding: 0 1rem;
  }
  
  .hero-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
    padding: 0 1rem;
  }
  
  .hero-text h1 {
    font-size: 2.5rem;
  }
  
  .hero-image {
    height: 300px;
    order: -1;
  }
  
  .task-card.demo {
    position: relative;
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .task-card.demo:nth-child(2),
  .task-card.demo:nth-child(3) {
    display: none;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .hero-buttons {
    justify-content: center;
  }
  
  .cta-btn {
    flex: 1;
    text-align: center;
    justify-content: center;
    min-width: 140px;
  }
}

@media (max-width: 480px) {
  .hero {
    padding: 100px 0 60px;
  }
  
  .hero-text h1 {
    font-size: 2rem;
  }
  
  .hero-text p {
    font-size: 1rem;
  }
  
  .cta-btn {
    padding: 0.875rem 1.5rem;
    font-size: 1rem;
  }
  
  .features h2 {
    font-size: 2rem;
  }
  
  .feature-card {
    padding: 1.5rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .cta-content h2 {
    font-size: 2rem;
  }
  
  .cta-content p {
    font-size: 1rem;
  }
}</style>