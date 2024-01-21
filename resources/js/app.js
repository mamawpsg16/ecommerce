import './bootstrap';
import { createApp } from 'vue'
import App from '@/views/App.vue';
import './bootstrap.js'
import router from '@/router/index.js';
import { createPinia } from 'pinia'


const pinia = createPinia()
const app = createApp(App);

app.use(pinia)
app.use(router);
app.mount('#app');