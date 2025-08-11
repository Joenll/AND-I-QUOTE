import { createApp } from 'vue'
import App from './App.vue'
import router from './router'  // no /src here
import './style.css'           // Tailwind

createApp(App).use(router).mount('#app')
