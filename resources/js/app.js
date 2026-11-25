import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';
import { vuetify } from './plugins/vuetify';
import { loadCompanies } from './stores/company';

loadCompanies();

createApp(App).use(router).use(vuetify).mount('#app');