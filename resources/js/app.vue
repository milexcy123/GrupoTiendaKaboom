<template>
  <v-app>
    <v-app-bar color="primary" title="Grupo Kaboom Express" />
    <v-main>
      <v-container>
        <v-card class="pa-5" elevation="3">
          <v-card-title class="text-h5">Prueba de conexión con la API</v-card-title>
          <v-card-text>
            <v-alert v-if="mensaje" type="success" variant="tonal">
              {{ mensaje }} (base de datos: {{ baseDatos }})
            </v-alert>
            <v-alert v-if="error" type="error" variant="tonal">{{ error }}</v-alert>
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" :loading="cargando" @click="probarApi">
              Probar API
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const mensaje = ref('');
const baseDatos = ref('');
const error = ref('');
const cargando = ref(false);

async function probarApi() {
  cargando.value = true;
  mensaje.value = '';
  error.value = '';
  try {
    const { data } = await axios.get('/api/ping');
    mensaje.value = data.message;
    baseDatos.value = data.database;
  } catch (e) {
    error.value = 'No se pudo conectar con la API. Revisa la terminal de php artisan serve.';
  } finally {
    cargando.value = false;
  }
}
</script>