<template>
  <div class="offers-page">
    <v-container class="offers-shell py-7 py-md-11">
      <section class="offers-hero">
        <div>
          <span class="eyebrow">OPORTUNIDADES PARA TU PROYECTO</span>
          <h1>Ofertas especiales</h1>
          <p>Precios especiales en productos seleccionados de nuestras empresas.</p>
        </div>
        <div class="hero-discount">
          <v-icon size="44">mdi-tag-heart-outline</v-icon>
          <span>Descuentos<br />por tiempo limitado</span>
        </div>
      </section>

      <div class="offers-toolbar">
        <div>
          <h2>{{ companyName ? `Ofertas de ${companyName}` : 'Aprovecha estas ofertas' }}</h2>
          <p>{{ visibleOffers.length }} promociones disponibles</p>
        </div>
        <v-btn to="/catalogo" variant="text" append-icon="mdi-arrow-right">Ver catálogo completo</v-btn>
      </div>

      <v-alert v-if="error" class="mb-5" type="error" variant="tonal">{{ error }}</v-alert>
      <v-progress-linear v-if="loading" color="primary" indeterminate rounded />

      <div v-else-if="visibleOffers.length" class="offer-grid">
        <ProductCard
          v-for="offer in visibleOffers"
          :key="offer.id"
          :product="offerProduct(offer)"
        />
      </div>
      <div v-else class="empty-offers">
        <span class="empty-icon"><v-icon size="34">mdi-tag-off-outline</v-icon></span>
        <h2>Aún no hay ofertas disponibles</h2>
        <p>Pronto tendremos nuevas promociones para {{ companyName || 'ti' }}.</p>
        <v-btn color="primary" to="/catalogo" append-icon="mdi-arrow-right">Explorar productos</v-btn>
      </div>
    </v-container>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import ProductCard from '../components/ProductCard.vue';
import api, { errorMessage } from '../services/api';
import { company, selectedCompanyId } from '../stores/company';

const offers = ref([]);
const loading = ref(true);
const error = ref('');

const companyName = computed(
  () => company.list.find((item) => item.slug === company.selected)?.name || ''
);
const visibleOffers = computed(() => {
  const companyId = selectedCompanyId();
  return offers.value.filter((offer) => !companyId || offer.product.company_id === companyId);
});

function offerProduct(offer) {
  return {
    ...offer.product,
    offers: [{ discount: offer.discount }],
  };
}

onMounted(async () => {
  try {
    const { data } = await api.get('/offers');
    offers.value = data;
  } catch (requestError) {
    error.value = errorMessage(requestError);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.offers-page {
  min-height: 65vh;
  background: #f6f7f9;
}

.offers-shell {
  max-width: 1240px;
}

.offers-hero {
  display: flex;
  min-height: 250px;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: clamp(24px, 5vw, 52px);
  border-radius: 16px;
  color: #fff;
  background:
    radial-gradient(circle at 84% 45%, rgb(var(--v-theme-primary) / 28%), transparent 20rem),
    linear-gradient(110deg, #242222, #403a35);
}

.eyebrow {
  color: rgb(var(--v-theme-primary));
  font-size: 0.7rem;
  font-weight: 850;
  letter-spacing: 0.13em;
}

.offers-hero h1 {
  margin: 8px 0;
  font-size: clamp(2.2rem, 5vw, 3.8rem);
  font-weight: 950;
  letter-spacing: -0.055em;
}

.offers-hero p {
  max-width: 470px;
  margin: 0;
  color: rgb(255 255 255 / 78%);
  line-height: 1.6;
}

.hero-discount {
  display: grid;
  width: 150px;
  height: 150px;
  flex: 0 0 auto;
  place-content: center;
  gap: 8px;
  border: 1px solid rgb(var(--v-theme-primary) / 45%);
  border-radius: 50%;
  color: rgb(var(--v-theme-primary));
  text-align: center;
}

.hero-discount span {
  color: #fff;
  font-size: 0.78rem;
  font-weight: 750;
  line-height: 1.35;
}

.offers-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin: 34px 0 20px;
}

.offers-toolbar h2 {
  color: #282d35;
  font-size: 1.35rem;
  font-weight: 900;
}

.offers-toolbar p {
  margin: 3px 0 0;
  color: #7a8088;
  font-size: 0.82rem;
}

.offer-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.empty-offers {
  display: flex;
  min-height: 330px;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 28px;
  border: 1px dashed #d9dde2;
  border-radius: 14px;
  background: #fff;
  text-align: center;
}

.empty-icon {
  display: grid;
  width: 64px;
  height: 64px;
  margin-bottom: 12px;
  place-items: center;
  border-radius: 20px;
  color: rgb(var(--v-theme-primary));
  background: rgb(var(--v-theme-primary) / 12%);
}

.empty-offers h2 {
  color: #292d33;
  font-size: 1.2rem;
  font-weight: 850;
}

.empty-offers p {
  margin: 5px 0 18px;
  color: #777d84;
}

@media (max-width: 960px) {
  .offer-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 650px) {
  .offers-hero {
    min-height: 220px;
  }

  .hero-discount {
    width: 92px;
    height: 92px;
  }

  .hero-discount :deep(.v-icon) {
    font-size: 30px !important;
  }

  .hero-discount span {
    font-size: 0.64rem;
  }

  .offer-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .offers-toolbar {
    align-items: flex-start;
  }

  .offers-toolbar .v-btn {
    padding-inline: 4px;
    font-size: 0.7rem;
  }
}
</style>
