<template>
  <v-container class="catalog-page py-8 py-md-12">
    <div class="catalog-heading">
      <div>
        <span class="eyebrow">GRUPO KABOOM EXPRESS</span>
        <h1>Catálogo de productos</h1>
        <p>Encuentra herramientas y equipos para tu próximo proyecto.</p>
      </div>
      <v-chip v-if="company.selected !== 'all'" color="primary" variant="tonal">
        {{ companyName }}
      </v-chip>
    </div>

    <div class="catalog-filters">
      <v-text-field
        v-model="search"
        label="Buscar productos"
        placeholder="Nombre, marca o descripción"
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        density="comfortable"
        hide-details
        @keyup.enter="aplicarFiltros"
      />
      <v-select
        v-model="categoryId"
        :items="categories"
        item-title="name"
        item-value="id"
        label="Todas las categorías"
        variant="outlined"
        density="comfortable"
        clearable
        hide-details
        @update:model-value="aplicarFiltros"
      />
      <v-btn color="primary" size="large" prepend-icon="mdi-magnify" @click="aplicarFiltros">
        Buscar
      </v-btn>
    </div>

    <v-alert v-if="error" class="mb-6" type="error" variant="tonal">{{ error }}</v-alert>
    <div class="results-heading">
      <h2>{{ search ? `Resultados para “${search}”` : 'Todos los productos' }}</h2>
      <span v-if="!cargando">{{ products.length }} productos</span>
    </div>

    <v-progress-linear v-if="cargando" color="primary" indeterminate rounded />
    <div v-else-if="products.length" class="product-grid">
      <ProductCard v-for="product in products" :key="product.id" :product="product" />
    </div>
    <div v-else class="catalog-empty">
      <v-icon size="44">mdi-package-variant</v-icon>
      <h2>No encontramos productos</h2>
      <p>Prueba con otra búsqueda o selecciona una categoría diferente.</p>
      <v-btn color="primary" variant="flat" @click="limpiarFiltros">Limpiar filtros</v-btn>
    </div>
  </v-container>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import api, { errorMessage } from '../services/api';
import { company, selectedCompanyId } from '../stores/company';

const route = useRoute();
const router = useRouter();
const products = ref([]);
const categories = ref([]);
const search = ref(String(route.query.search || ''));
const categoryId = ref(route.query.category_id ? Number(route.query.category_id) : null);
const cargando = ref(false);
const error = ref('');
const companyName = computed(() => company.list.find((item) => item.slug === company.selected)?.name || '');

watch(
  () => [route.query.search, route.query.category_id, company.selected, selectedCompanyId()],
  () => {
    search.value = String(route.query.search || '');
    categoryId.value = route.query.category_id ? Number(route.query.category_id) : null;
    cargarProductos();
  },
  { immediate: true }
);

onMounted(async () => {
  try {
    const { data } = await api.get('/categories');
    categories.value = data;
  } catch (requestError) {
    error.value = errorMessage(requestError);
  }
});

async function cargarProductos() {
  cargando.value = true;
  error.value = '';

  try {
    const params = {
      search: route.query.search || undefined,
      category_id: route.query.category_id || undefined,
      company_id: selectedCompanyId() || undefined,
    };
    const { data } = await api.get('/products', { params });
    products.value = data;
  } catch (requestError) {
    error.value = errorMessage(requestError);
    products.value = [];
  } finally {
    cargando.value = false;
  }
}

function aplicarFiltros() {
  router.push({
    name: 'catalog',
    query: {
      search: search.value.trim() || undefined,
      category_id: categoryId.value || undefined,
    },
  });
}

function limpiarFiltros() {
  search.value = '';
  categoryId.value = null;
  router.push({ name: 'catalog' });
}
</script>

<style scoped>
.catalog-page {
  max-width: 1280px;
  min-height: 65vh;
}

.eyebrow {
  color: #886b00;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.13em;
}

.catalog-heading {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 28px;
}

.catalog-heading h1 {
  margin: 8px 0 4px;
  color: #242222;
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 900;
  letter-spacing: -0.045em;
}

.catalog-heading p {
  margin: 0;
  color: #777168;
}

.catalog-filters {
  display: grid;
  grid-template-columns: minmax(220px, 1fr) minmax(200px, 300px) auto;
  gap: 12px;
  margin-bottom: 34px;
  padding: 18px;
  border: 1px solid #e9e6df;
  border-radius: 14px;
  background: #fff;
}

.results-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.results-heading h2 {
  color: #292725;
  font-size: 1.15rem;
  font-weight: 800;
}

.results-heading span {
  color: #817a6e;
  font-size: 0.84rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.catalog-empty {
  display: flex;
  min-height: 300px;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 1px dashed #d7d2c8;
  border-radius: 14px;
  color: #777168;
  background: #fff;
  text-align: center;
}

.catalog-empty h2 {
  margin: 12px 0 4px;
  color: #292725;
  font-size: 1.25rem;
}

.catalog-empty p {
  margin-bottom: 20px;
}

@media (max-width: 960px) {
  .product-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .catalog-heading {
    align-items: flex-start;
    flex-direction: column;
  }

  .catalog-filters {
    grid-template-columns: 1fr;
  }

  .product-grid {
    gap: 12px;
  }
}
</style>
