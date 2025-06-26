// router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Import components
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import Dashboard from '@/components/Dashboard.vue'
import TaskList from '@/components/TaskList.vue'
import TaskDetail from '@/components/TaskDetail.vue'
import Profile from '@/components/Profile.vue'
import NotFound from '@/components/NotFound.vue'

// Auth guard function
function requireAuth(to, from, next) {
  const token = localStorage.getItem('token')
  
  if (!token) {
    // No token found, redirect to login
    next('/login')
  } else {
    // Token exists, check if it's valid (optional)
    try {
      const payload = JSON.parse(atob(token.split('.')[1]))
      const currentTime = Date.now() / 1000
      
      if (payload.exp < currentTime) {
        // Token expired
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        next('/login')
      } else {
        // Token is valid
        next()
      }
    } catch (error) {
      // Invalid token format
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      next('/login')
    }
  }
}

// Guest guard function (for login/register pages)
function requireGuest(to, from, next) {
  const token = localStorage.getItem('token')
  
  if (token) {
    // User is already logged in, redirect to dashboard
    next('/dashboard')
  } else {
    next()
  }
}

const routes = [
  // Default route - redirect to dashboard or login
  {
    path: '/',
    name: 'Home',
    redirect: () => {
      const token = localStorage.getItem('token')
      return token ? '/dashboard' : '/login'
    }
  },
  
  // Authentication routes
  {
    path: '/login',
    name: 'Login',
    component: Login,
    beforeEnter: requireGuest,
    meta: {
      title: 'Login - Task Management',
      hideNavbar: true
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    beforeEnter: requireGuest,
    meta: {
      title: 'Register - Task Management',
      hideNavbar: true
    }
  },
  
  // Protected routes (require authentication)
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    beforeEnter: requireAuth,
    meta: {
      title: 'Dashboard - Task Management',
      requiresAuth: true
    }
  },
  {
    path: '/tasks',
    name: 'TaskList',
    component: TaskList,
    beforeEnter: requireAuth,
    meta: {
      title: 'My Tasks - Task Management',
      requiresAuth: true
    }
  },
  {
    path: '/tasks/:id',
    name: 'TaskDetail',
    component: TaskDetail,
    beforeEnter: requireAuth,
    props: true, // Pass route params as props
    meta: {
      title: 'Task Details - Task Management',
      requiresAuth: true
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    beforeEnter: requireAuth,
    meta: {
      title: 'Profile - Task Management',
      requiresAuth: true
    }
  },
  
  // Logout route (programmatic)
  {
    path: '/logout',
    name: 'Logout',
    beforeEnter: (to, from, next) => {
      // Clear authentication data
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      // Redirect to login
      next('/login')
    }
  },
  
  // 404 Not Found
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: {
      title: '404 - Page Not Found'
    }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Scroll to top when navigating to a new route
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Global navigation guards
router.beforeEach((to, from, next) => {
  // Update document title
  if (to.meta.title) {
    document.title = to.meta.title
  }
  
  // Continue with navigation
  next()
})

// Handle navigation errors
router.onError((error) => {
  console.error('Router error:', error)
})

export default router