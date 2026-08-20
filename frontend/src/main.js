// main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/theme.css'
import './assets/auth.css'

createApp(App)
    .use(router)
    .mount('#app')
