import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: { Accept: 'application/json' },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('kaboom_token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

// Saca un mensaje legible de cualquier error de la API
export function errorMessage(error) {
    const data = error.response?.data;

    if (data?.errors) {
        return Object.values(data.errors)[0][0];
    }

    return data?.message || 'Ocurrió un error. Inténtalo de nuevo.';
}

export default api;