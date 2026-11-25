<template>
  <v-navigation-drawer v-model="drawer" :permanent="mdAndUp">
    <v-list-item title="Panel Kaboom" :subtitle="auth.user?.name" class="py-4" />
    <v-divider />
    <v-list nav density="comfortable">
      <v-list-item
        v-for="item in items"
        :key="item.to"
        :to="item.to"
        :prepend-icon="item.icon"
        :title="item.title"
        :exact="item.to === '/admin'"
      />
      <v-divider class="my-2" />
      <v-list-item to="/" prepend-icon="mdi-storefront" title="Ver la tienda" />
      <v-list-item prepend-icon="mdi-logout" title="Cerrar sesión" @click="salir" />
    </v-list>
  </v-navigation-drawer>

  <v-app-bar color="primary" elevation="2">
    <v-app-bar-nav-icon v-if="!mdAndUp" @click="drawer = !drawer" />
    <v-app-bar-title>Administración</v-app-bar-title>
  </v-app-bar>

  <v-main>
    <v-container fluid class="pa-4 pa-md-6">
      <router-view />
    </v-container>
  </v-main>

  <v-snackbar v-model="toast.show" :color="toast.color" timeout="3000">{{ toast.text }}</v-snackbar>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useDisplay } from 'vuetify';
import { auth, logout } from '../stores/auth';
import { toast } from '../stores/toast';

const router = useRouter();
const { mdAndUp } = useDisplay();
const drawer = ref(false);

const items = [
  { title: 'Resumen', to: '/admin', icon: 'mdi-view-dashboard' },
  { title: 'Pedidos', to: '/admin/pedidos', icon: 'mdi-clipboard-list' },
  { title: 'Productos', to: '/admin/productos', icon: 'mdi-package-variant' },
  { title: 'Categorías', to: '/admin/categorias', icon: 'mdi-shape' },
  { title: 'Ofertas', to: '/admin/ofertas', icon: 'mdi-tag-multiple' },
  { title: 'Banners', to: '/admin/banners', icon: 'mdi-image-multiple' },
  { title: 'Clientes', to: '/admin/clientes', icon: 'mdi-account-group' },
  { title: 'Reportes', to: '/admin/reportes', icon: 'mdi-chart-bar' },
];

async function salir() {
  await logout();
  router.push('/login');
}
</script>