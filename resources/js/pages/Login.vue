<template>
  <v-container class="py-10" style="max-width: 480px">
    <v-card class="pa-6" elevation="3">
      <v-card-title class="text-h5 mb-2">Iniciar sesión</v-card-title>

      <v-alert v-if="error" type="error" variant="tonal" class="mb-4">{{ error }}</v-alert>

      <v-form @submit.prevent="enviar">
        <v-text-field v-model="email" label="Correo" type="email" prepend-inner-icon="mdi-email" />
        <v-text-field
          v-model="password"
          label="Contraseña"
          :type="ver ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock"
          :append-inner-icon="ver ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="ver = !ver"
        />
        <v-btn type="submit" color="primary" block size="large" :loading="cargando">Entrar</v-btn>
      </v-form>

      <div class="text-center mt-4">
        ¿No tienes cuenta? <router-link to="/registro">Regístrate</router-link>
      </div>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { errorMessage } from '../services/api';
import { login } from '../stores/auth';

const route = useRoute();
const router = useRouter();

const email = ref('');
const password = ref('');
const ver = ref(false);
const cargando = ref(false);
const error = ref('');

async function enviar() {
  error.value = '';
  cargando.value = true;

  try {
    const user = await login(email.value, password.value);
    const destino = route.query.redirect || (user.role === 'admin' ? '/admin' : '/');
    router.push(destino);
  } catch (e) {
    error.value = errorMessage(e);
  } finally {
    cargando.value = false;
  }
}
</script>