import { reactive, computed } from 'vue';
import api from '../services/api';

export const auth = reactive({
    user: null,
    token: localStorage.getItem('kaboom_token'),
    ready: false,
});

export const isAdmin = computed(() => auth.user?.role === 'admin');

function saveSession(data) {
    auth.token = data.token;
    auth.user = data.user;
    localStorage.setItem('kaboom_token', data.token);
}

function clearSession() {
    auth.token = null;
    auth.user = null;
    localStorage.removeItem('kaboom_token');
}

// Se llama una vez al abrir la página: recupera al usuario si hay token guardado
export async function loadUser() {
    if (!auth.token) {
        auth.user = null;
        auth.ready = true;
        return;
    }

    try {
        const { data } = await api.get('/user');
        auth.user = data;
    } catch (e) {
        clearSession();
    } finally {
        auth.ready = true;
    }
}

export async function login(email, password) {
    const { data } = await api.post('/login', { email, password });
    saveSession(data);

    return data.user;
}

export async function register(payload) {
    const { data } = await api.post('/register', payload);
    saveSession(data);

    return data.user;
}

export async function logout() {
    try {
        await api.post('/logout');
    } catch (e) {
        // si el token ya no servía, igual se cierra la sesión local
    }

    clearSession();
}