import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import Dashboard from '../components/Dashboard.vue'
import Workspaces from '../components/Workspaces.vue'
import Projects from '../components/Projects.vue'
import Tasks from '../components/Tasks.vue'
import Home from '../components/Home.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { 
      title: 'TaskMaster - Ultimate Task Management'
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { 
      title: 'Login - TaskMaster',
      guest: true 
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { 
      title: 'Register - TaskMaster',
      guest: true 
    }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { 
      requiresAuth: true,
      title: 'Dashboard - TaskMaster'
    }
  },
  {
    path: '/workspaces',
    name: 'Workspaces',
    component: Workspaces,
    meta: { 
      requiresAuth: true,
      title: 'Workspaces - TaskMaster'
    }
  },
  {
    path: '/workspace/:workspaceId',
    name: 'WorkspaceDetail',
    component: Projects,
    meta: { 
      requiresAuth: true,
      title: 'Workspace Projects - TaskMaster'
    }
  },
  {
    path: '/projects',
    name: 'Projects',
    component: Projects,
    meta: { 
      requiresAuth: true,
      title: 'Projects - TaskMaster'
    }
  },
  {
    path: '/project/:projectId',
    name: 'ProjectDetail',
    component: Projects,
    meta: { 
      requiresAuth: true,
      title: 'Project Details - TaskMaster'
    }
  },
  {
    path: '/project/:projectId/tasks',
    name: 'ProjectTasks',
    component: Tasks,
    meta: { 
      requiresAuth: true,
      title: 'Project Tasks - TaskMaster'
    }
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: Tasks,
    meta: { 
      requiresAuth: true,
      title: 'All Tasks - TaskMaster'
    }
  },
  {
    path: '/tasks/:status',
    name: 'TasksByStatus',
    component: Tasks,
    meta: { 
      requiresAuth: true,
      title: 'Tasks - TaskMaster'
    }
  },
  // Redirect old routes for backward compatibility
  {
    path: '/item-list',
    redirect: '/dashboard'
  },
  {
    path: '/seller-home',
    redirect: '/dashboard'
  },
  // 404 Catch all route
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('../components/NotFound.vue'),
    meta: {
      title: 'Page Not Found - TaskMaster'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Always scroll to top when navigating to a new page
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Helper function to check if user is authenticated
function isAuthenticated() {
  const user = localStorage.getItem('user')
  const token = localStorage.getItem('token')
  return user && token
}

// Navigation guard to check authentication and handle redirects
router.beforeEach((to, from, next) => {
  const authenticated = isAuthenticated()
  
  // Set page title
  if (to.meta.title) {
    document.title = to.meta.title
  }
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !authenticated) {
    // Store intended destination for redirect after login
    localStorage.setItem('intendedRoute', to.fullPath)
    next('/login')
    return
  }
  
  // Check if route is for guests only (login/register pages)
  if (to.meta.guest && authenticated) {
    // If user is already logged in, redirect to dashboard
    next('/dashboard')
    return
  }
  
  // If user just logged in and we have an intended route, redirect there
  if (authenticated && to.path === '/login') {
    const intendedRoute = localStorage.getItem('intendedRoute')
    if (intendedRoute) {
      localStorage.removeItem('intendedRoute')
      next(intendedRoute)
      return
    }
    next('/dashboard')
    return
  }
  
  // Allow navigation
  next()
})

// After each route change
router.afterEach((to) => {
  // You can add analytics tracking here if needed
  console.log(`Navigated to: ${to.path}`)
})

export default router