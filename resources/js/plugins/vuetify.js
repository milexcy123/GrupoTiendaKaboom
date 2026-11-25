import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

export const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'kaboom',
        themes: {
            // Tema base del grupo: amarillo y carbón
            kaboom: {
                dark: false,
                colors: {
                    primary: '#FDC101',
                    'on-primary': '#222021',
                    secondary: '#2B2829',
                    'on-secondary': '#FFFFFF',
                    background: '#F5F5F5',
                    surface: '#FFFFFF',
                    error: '#E53935',
                },
            },
            // Bomohsa: azul y blanco
            bomohsa: {
                dark: false,
                colors: {
                    primary: '#1565C0',
                    'on-primary': '#FFFFFF',
                    secondary: '#0D2B52',
                    'on-secondary': '#FFFFFF',
                    background: '#FFFFFF',
                    surface: '#FFFFFF',
                },
            },
            // Agrinsa: rojo y blanco
            agrinsa: {
                dark: false,
                colors: {
                    primary: '#C62828',
                    'on-primary': '#FFFFFF',
                    secondary: '#4A1010',
                    'on-secondary': '#FFFFFF',
                    background: '#FFFFFF',
                    surface: '#FFFFFF',
                },
            },
        },
    },
});

// Cambia el tema activo (si el nombre no existe, vuelve al tema base)
export function applyTheme(name) {
    const theme = vuetify.theme;

    const cambiar = (nombre) => {
        if (typeof theme.change === 'function') {
            theme.change(nombre);
        } else {
            theme.global.name.value = nombre;
        }
    };

    try {
        cambiar(name);
    } catch (e) {
        cambiar('kaboom');
    }
}