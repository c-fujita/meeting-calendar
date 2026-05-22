import './bootstrap';
import { createApp } from 'vue';
import Calendar from './components/Calendar.vue';

const appElement = document.getElementById('app')

createApp(Calendar, {
    apiUrl: appElement.dataset.apiUrl,
}).mount('#app')