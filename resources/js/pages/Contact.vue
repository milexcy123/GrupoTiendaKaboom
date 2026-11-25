<template>
  <div class="contact-page">
    <v-container class="contact-shell py-7 py-md-11">
      <section class="contact-hero">
        <span class="eyebrow">ESTAMOS PARA AYUDARTE</span>
        <h1>Hablemos de tu próximo proyecto.</h1>
        <p>Déjanos tu consulta y nuestro equipo te responderá.</p>
      </section>

      <div class="contact-layout">
        <section class="contact-information">
          <div class="section-title">
            <span class="eyebrow">CONTACTO DIRECTO</span>
            <h2>Encuentra a tu equipo</h2>
          </div>

          <article v-for="item in displayedCompanies" :key="item.id || item.slug" class="contact-company">
            <span class="company-mark" :style="{ backgroundColor: item.primary_color }">
              <v-icon>{{ item.slug === 'agrinsa' ? 'mdi-sprout' : 'mdi-water-pump' }}</v-icon>
            </span>
            <div class="company-details">
              <strong>{{ item.name }}</strong>
              <a v-if="item.phone" :href="`tel:${item.phone}`">
                <v-icon size="17">mdi-phone-outline</v-icon>{{ item.phone }}
              </a>
              <a v-if="item.email" :href="`mailto:${item.email}`">
                <v-icon size="17">mdi-email-outline</v-icon>{{ item.email }}
              </a>
              <span v-if="!item.phone && !item.email" class="contact-pending">Datos de contacto próximamente</span>
            </div>
          </article>

          <div class="response-note">
            <v-icon color="primary">mdi-clock-fast</v-icon>
            <span>Completa el formulario y nos pondremos en contacto contigo.</span>
          </div>
        </section>

        <v-card class="contact-form-card" elevation="0">
          <div class="form-heading">
            <span class="eyebrow">ENVÍANOS UN MENSAJE</span>
            <h2>¿En qué podemos ayudarte?</h2>
          </div>
          <v-form ref="formRef" @submit.prevent="enviar">
            <v-text-field
              v-model="form.name"
              label="Nombre completo"
              placeholder="Tu nombre"
              prepend-inner-icon="mdi-account-outline"
              :rules="[required]"
              variant="outlined"
              autocomplete="name"
            />
            <v-text-field
              v-model="form.email"
              label="Correo electrónico"
              placeholder="nombre@correo.com"
              prepend-inner-icon="mdi-email-outline"
              :rules="[required, validEmail]"
              variant="outlined"
              type="email"
              autocomplete="email"
            />
            <v-textarea
              v-model="form.message"
              label="Tu mensaje"
              placeholder="Cuéntanos qué necesitas..."
              prepend-inner-icon="mdi-message-text-outline"
              :rules="[required]"
              variant="outlined"
              rows="5"
              auto-grow
            />
            <v-btn type="submit" color="primary" size="large" block :loading="sending" append-icon="mdi-send">
              Enviar consulta
            </v-btn>
          </v-form>
        </v-card>
      </div>
    </v-container>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import api, { errorMessage } from '../services/api';
import { company } from '../stores/company';
import { notify } from '../stores/toast';

const formRef = ref(null);
const sending = ref(false);
const form = reactive({ name: '', email: '', message: '' });
const displayedCompanies = computed(() => {
  if (company.selected === 'all') return company.list;
  return company.list.filter((item) => item.slug === company.selected);
});

const required = (value) => Boolean(String(value || '').trim()) || 'Este campo es obligatorio.';
const validEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || 'Escribe un correo válido.';

async function enviar() {
  const result = await formRef.value.validate();
  if (!result.valid) return;

  sending.value = true;
  try {
    const { data } = await api.post('/contact', form);
    notify(data.message || 'Tu mensaje fue enviado.');
    form.name = '';
    form.email = '';
    form.message = '';
    formRef.value.resetValidation();
  } catch (requestError) {
    notify(errorMessage(requestError), 'error');
  } finally {
    sending.value = false;
  }
}
</script>

<style scoped>
.contact-page {
  min-height: 68vh;
  background: #f6f7f9;
}

.contact-shell {
  max-width: 1160px;
}

.contact-hero {
  margin-bottom: 30px;
  padding: clamp(24px, 5vw, 48px);
  border-radius: 16px;
  color: #fff;
  background:
    radial-gradient(circle at 92% 20%, rgb(var(--v-theme-primary) / 26%), transparent 21rem),
    linear-gradient(110deg, #252321, #393633);
}

.eyebrow {
  color: rgb(var(--v-theme-primary));
  font-size: 0.7rem;
  font-weight: 850;
  letter-spacing: 0.13em;
}

.contact-hero h1 {
  max-width: 710px;
  margin: 10px 0;
  font-size: clamp(2rem, 5vw, 3.3rem);
  font-weight: 950;
  letter-spacing: -0.05em;
  line-height: 1.07;
}

.contact-hero p {
  margin: 0;
  color: rgb(255 255 255 / 78%);
}

.contact-layout {
  display: grid;
  grid-template-columns: minmax(260px, 0.85fr) minmax(0, 1.15fr);
  gap: 22px;
}

.contact-information {
  padding: 22px 4px;
}

.section-title h2,
.form-heading h2 {
  margin-top: 6px;
  color: #292d33;
  font-size: 1.45rem;
  font-weight: 900;
  letter-spacing: -0.035em;
}

.contact-company {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 20px;
  padding: 18px;
  border: 1px solid #e6e8eb;
  border-radius: 12px;
  background: #fff;
}

.company-mark {
  display: grid;
  width: 42px;
  height: 42px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 12px;
  color: #fff;
}

.company-details {
  display: flex;
  min-width: 0;
  flex-direction: column;
  gap: 8px;
}

.company-details strong {
  color: #292d33;
  font-weight: 850;
}

.company-details a,
.contact-pending {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #69717a;
  font-size: 0.82rem;
  text-decoration: none;
}

.company-details a:hover {
  color: rgb(var(--v-theme-primary));
}

.response-note {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 22px;
  color: #6d737a;
  font-size: 0.83rem;
  line-height: 1.5;
}

.contact-form-card {
  padding: clamp(20px, 4vw, 32px);
  border: 1px solid #e6e8eb;
  border-radius: 14px;
  background: #fff;
}

.form-heading {
  margin-bottom: 20px;
}

.contact-form-card :deep(.v-field) {
  border-radius: 9px;
}

@media (max-width: 760px) {
  .contact-layout {
    grid-template-columns: 1fr;
  }

  .contact-information {
    padding-inline: 0;
  }
}
</style>
