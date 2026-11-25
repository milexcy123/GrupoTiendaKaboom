import { reactive } from 'vue';
import api from '../services/api';
import { applyTheme } from '../plugins/vuetify';

export const company = reactive({
    selected: localStorage.getItem('kaboom_company') || 'all',
    list: [],
});

const themeName = () => (company.selected === 'all' ? 'kaboom' : company.selected);

export function selectCompany(slug) {
    company.selected = slug;
    localStorage.setItem('kaboom_company', slug);
    applyTheme(themeName());
}

// id de la empresa elegida (o null si son "todas")
export function selectedCompanyId() {
    const found = company.list.find((c) => c.slug === company.selected);

    return found ? found.id : null;
}

export async function loadCompanies() {
    try {
        const { data } = await api.get('/companies');
        company.list = data;

        if (company.selected !== 'all' && !data.some((c) => c.slug === company.selected)) {
            company.selected = 'all';
        }
    } catch (e) {
        // sin conexión con la API: se queda con el tema base
    }

    applyTheme(themeName());
}