<template>
  <div v-if="profile" class="company-home" :style="{ '--company-color': profile.primary_color, '--company-ink': profile.ink }">
    <v-container class="company-shell py-6 py-md-10">
      <router-link to="/" class="back-link">
        <v-icon size="18">mdi-arrow-left</v-icon>
        Todas las empresas
      </router-link>

      <section class="company-hero" :style="{ backgroundImage: `linear-gradient(90deg, rgba(13, 23, 40, .88), rgba(13, 23, 40, .36)), url('${profile.image}')` }">
        <div class="hero-brand">
          <span class="hero-logo">{{ profile.mark }}</span>
          <span class="hero-label">GRUPO KABOOM EXPRESS</span>
        </div>
        <div class="hero-copy">
          <span class="hero-kicker">{{ profile.kicker }}</span>
          <h1>{{ profile.name }}</h1>
          <p>{{ profile.description }}</p>
          <v-btn color="primary" size="large" prepend-icon="mdi-view-grid-outline" @click="irAlCatalogo">
            Explorar catálogo
          </v-btn>
        </div>
        <v-icon class="hero-watermark" size="220">{{ profile.icon }}</v-icon>
      </section>

      <section class="quick-links">
        <div class="section-heading">
          <div>
            <span class="eyebrow">¿CÓMO PODEMOS AYUDARTE?</span>
            <h2>Explora {{ profile.name }}</h2>
          </div>
          <span class="company-count">Catálogo exclusivo de {{ profile.name }}</span>
        </div>

        <div class="option-grid">
          <button
            v-for="option in options"
            :key="option.title"
            type="button"
            class="option-card"
            @click="option.action()"
          >
            <span class="option-icon"><v-icon size="25">{{ option.icon }}</v-icon></span>
            <strong>{{ option.title }}</strong>
            <span>{{ option.description }}</span>
            <v-icon class="option-arrow" size="20">mdi-arrow-right</v-icon>
          </button>
        </div>
      </section>

      <section class="company-promise">
        <div class="promise-icon"><v-icon size="30">mdi-handshake-outline</v-icon></div>
        <div>
          <span class="eyebrow">ATENCIÓN CERCANA</span>
          <h2>La solución correcta para cada proyecto.</h2>
          <p>Explora nuestros equipos, compara opciones y encuentra justo lo que necesitas.</p>
        </div>
        <v-btn color="primary" variant="tonal" append-icon="mdi-arrow-right" @click="irAlCatalogo">
          Ver productos
        </v-btn>
      </section>
    </v-container>
  </div>

  <v-container v-else class="py-16 text-center">
    <v-progress-circular color="primary" indeterminate />
    <p class="mt-4 text-medium-emphasis">Cargando empresa...</p>
  </v-container>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { company, selectCompany } from '../stores/company';

const route = useRoute();
const router = useRouter();

const profiles = {
  bomohsa: {
    name: 'Bomohsa',
    primary_color: '#1565C0',
    ink: '#123d72',
    mark: 'B',
    icon: 'mdi-water-pump',
    kicker: 'AGUA · ENERGÍA · INDUSTRIA',
    description: 'Tecnología y equipos confiables para impulsar tus proyectos.',
    image: 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1800&q=85',
  },
  agrinsa: {
    name: 'Agrinsa',
    primary_color: '#C62828',
    ink: '#6f1b1b',
    mark: 'A',
    icon: 'mdi-sprout',
    kicker: 'CAMPO · GANADERÍA · PRODUCTIVIDAD',
    description: 'Equipos y soluciones para hacer crecer el trabajo en el campo.',
    image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=85',
  },
};

const profile = computed(() => {
  const preset = profiles[route.params.slug];
  const data = company.list.find((item) => item.slug === route.params.slug);

  if (!preset) return null;

  return {
    ...preset,
    ...data,
    primary_color: data?.primary_color || preset.primary_color,
  };
});

const options = [
  {
    title: 'Catálogo',
    description: 'Encuentra todos nuestros productos y equipos.',
    icon: 'mdi-storefront-outline',
    action: irAlCatalogo,
  },
  {
    title: 'Ofertas',
    description: 'Descubre precios especiales por tiempo limitado.',
    icon: 'mdi-tag-heart-outline',
    action: () => router.push('/ofertas'),
  },
  {
    title: 'Atención al cliente',
    description: 'Conoce nuestros canales de contacto y soporte.',
    icon: 'mdi-headset',
    action: () => router.push('/contacto'),
  },
];

function irAlCatalogo() {
  router.push({ name: 'catalog' });
}

function actualizarEmpresa(slug) {
  if (!profiles[slug]) {
    router.replace({ name: 'home' });
    return;
  }

  selectCompany(slug);
}

onMounted(() => actualizarEmpresa(route.params.slug));
watch(() => route.params.slug, actualizarEmpresa);
</script>

<style scoped>
.company-home {
  min-height: 70vh;
  background:
    radial-gradient(ellipse at 85% 12%, color-mix(in srgb, var(--company-color) 7%, transparent), transparent 28rem),
    #f6f7f9;
}

.company-shell {
  max-width: 1240px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  margin-bottom: 18px;
  color: #625f59;
  font-size: 0.84rem;
  font-weight: 700;
  text-decoration: none;
}

.back-link:hover {
  color: var(--company-ink);
}

.company-hero {
  position: relative;
  display: flex;
  min-height: 360px;
  overflow: hidden;
  flex-direction: column;
  justify-content: space-between;
  padding: clamp(24px, 5vw, 52px);
  border-radius: 16px;
  color: #fff;
  background-position: center;
  background-size: cover;
  box-shadow: 0 18px 45px rgb(24 37 53 / 14%);
}

.hero-brand {
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 12px;
}

.hero-logo {
  display: grid;
  width: 44px;
  height: 44px;
  place-items: center;
  border-radius: 50%;
  color: var(--company-ink);
  background: #fff;
  font-size: 1.3rem;
  font-weight: 950;
}

.hero-label,
.hero-kicker,
.eyebrow {
  font-size: 0.7rem;
  font-weight: 850;
  letter-spacing: 0.13em;
}

.hero-label {
  color: rgb(255 255 255 / 78%);
}

.hero-copy {
  z-index: 1;
  max-width: 580px;
}

.hero-kicker {
  color: #dceaff;
}

.hero-copy h1 {
  margin: 8px 0;
  font-size: clamp(2.5rem, 6vw, 4.4rem);
  font-weight: 950;
  letter-spacing: -0.06em;
  line-height: 1;
}

.hero-copy p {
  max-width: 500px;
  margin: 0 0 22px;
  color: rgb(255 255 255 / 85%);
  font-size: 1rem;
  line-height: 1.6;
}

.hero-watermark {
  position: absolute;
  right: 7%;
  bottom: 10%;
  color: rgb(255 255 255 / 20%);
  transform: rotate(-12deg);
}

.quick-links {
  padding: 42px 0 34px;
}

.section-heading {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 18px;
}

.eyebrow {
  color: var(--company-color);
}

.section-heading h2,
.company-promise h2 {
  margin-top: 5px;
  color: #242a33;
  font-size: clamp(1.4rem, 3vw, 1.9rem);
  font-weight: 900;
  letter-spacing: -0.04em;
}

.company-count {
  padding: 8px 12px;
  border: 1px solid color-mix(in srgb, var(--company-color) 20%, #fff);
  border-radius: 999px;
  color: var(--company-ink);
  background: #fff;
  font-size: 0.76rem;
  font-weight: 750;
}

.option-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}

.option-card {
  position: relative;
  display: flex;
  min-height: 178px;
  flex-direction: column;
  align-items: flex-start;
  padding: 20px;
  border: 1px solid #e6e8eb;
  border-radius: 12px;
  color: #292d33;
  background: #fff;
  cursor: pointer;
  text-align: left;
  transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
}

.option-card:hover {
  border-color: color-mix(in srgb, var(--company-color) 55%, #fff);
  box-shadow: 0 12px 26px rgb(24 37 53 / 8%);
  transform: translateY(-2px);
}

.option-icon {
  display: grid;
  width: 44px;
  height: 44px;
  margin-bottom: 14px;
  place-items: center;
  border-radius: 12px;
  color: var(--company-color);
  background: color-mix(in srgb, var(--company-color) 10%, #fff);
}

.option-card strong {
  margin-bottom: 5px;
  font-size: 0.95rem;
  font-weight: 850;
}

.option-card > span:not(.option-icon) {
  max-width: 250px;
  color: #777d84;
  font-size: 0.8rem;
  line-height: 1.5;
}

.option-arrow {
  position: absolute;
  right: 18px;
  bottom: 18px;
  color: var(--company-color);
}

.company-promise {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 24px;
  border: 1px solid #e7e9ed;
  border-radius: 14px;
  background: #fff;
}

.promise-icon {
  display: grid;
  width: 58px;
  height: 58px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 16px;
  color: #fff;
  background: var(--company-color);
}

.company-promise > div:nth-child(2) {
  flex: 1;
}

.company-promise h2 {
  font-size: 1.2rem;
}

.company-promise p {
  margin: 4px 0 0;
  color: #777d84;
  font-size: 0.84rem;
}

@media (max-width: 760px) {
  .company-hero {
    min-height: 330px;
  }

  .hero-watermark {
    right: -24px;
    bottom: 12%;
  }

  .option-grid {
    grid-template-columns: 1fr;
  }

  .option-card {
    min-height: 150px;
  }

  .company-promise {
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .company-promise > div:nth-child(2) {
    flex-basis: calc(100% - 78px);
  }

  .company-count {
    display: none;
  }
}
</style>
