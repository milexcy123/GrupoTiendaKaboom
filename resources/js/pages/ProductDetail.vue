<template>
  <v-container class="detail-page py-8 py-md-12">
    <v-btn variant="text" prepend-icon="mdi-arrow-left" class="back-button" @click="router.back()">
      Volver
    </v-btn>

    <v-progress-linear v-if="cargando" color="primary" indeterminate rounded />
    <v-alert v-else-if="error" type="error" variant="tonal">{{ error }}</v-alert>
    <v-row v-else-if="product" class="detail-content" align="stretch">
      <v-col cols="12" md="6">
        <div class="detail-image">
          <v-img v-if="product.image" :src="product.image" :alt="product.name" cover />
          <div v-else class="image-placeholder">
            <v-icon size="96">mdi-image-outline</v-icon>
          </div>
          <v-chip v-if="precio.discount" color="primary" class="discount-chip" variant="flat">
            {{ precio.discount }}% de descuento
          </v-chip>
        </div>
      </v-col>
      <v-col cols="12" md="6">
        <div class="detail-copy">
          <div class="product-meta">
            <span v-if="product.company">{{ product.company.name }}</span>
            <span v-if="product.category">{{ product.category.name }}</span>
          </div>
          <p v-if="product.brand" class="brand-name">{{ product.brand }}</p>
          <h1>{{ product.name }}</h1>
          <div class="detail-price">
            <strong>{{ formatMoney(precio.final) }}</strong>
            <span v-if="precio.discount">{{ formatMoney(precio.price) }}</span>
          </div>
          <p class="stock-status" :class="{ 'out-of-stock': soldOut }">
            <v-icon size="18">{{ soldOut ? 'mdi-close-circle-outline' : 'mdi-check-circle-outline' }}</v-icon>
            {{ soldOut ? 'Agotado' : `${product.stock} disponibles` }}
          </p>
          <p v-if="product.description" class="description">{{ product.description }}</p>
          <p v-else class="description">Consulta los detalles con nuestro equipo y encuentra lo que necesitas para tu proyecto.</p>

          <div class="purchase-row">
            <div class="quantity-control">
              <v-btn
                icon="mdi-minus"
                size="small"
                variant="text"
                aria-label="Disminuir cantidad"
                :disabled="quantity <= 1"
                @click="quantity--"
              />
              <span>{{ quantity }}</span>
              <v-btn
                icon="mdi-plus"
                size="small"
                variant="text"
                aria-label="Aumentar cantidad"
                :disabled="quantity >= product.stock"
                @click="quantity++"
              />
            </div>
            <v-btn
              color="primary"
              size="large"
              prepend-icon="mdi-cart-plus"
              :disabled="soldOut"
              :loading="agregando"
              @click="agregarAlCarrito"
            >
              Agregar al carrito
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api, { errorMessage } from '../services/api';
import { formatMoney, productPrice } from '../services/format';
import { addToCart } from '../stores/cart';
import { auth } from '../stores/auth';
import { notify } from '../stores/toast';

const route = useRoute();
const router = useRouter();
const product = ref(null);
const cargando = ref(true);
const agregando = ref(false);
const error = ref('');
const quantity = ref(1);
const precio = computed(() => (product.value ? productPrice(product.value) : { price: 0, final: 0, discount: 0 }));
const soldOut = computed(
  () => !product.value || product.value.status === 'sold_out' || Number(product.value.stock) < 1
);

watch(
  () => route.params.id,
  cargarProducto,
  { immediate: true }
);

async function cargarProducto(id) {
  cargando.value = true;
  error.value = '';
  product.value = null;
  quantity.value = 1;

  try {
    const { data } = await api.get(`/products/${id}`);
    product.value = data;
  } catch (requestError) {
    error.value = errorMessage(requestError);
  } finally {
    cargando.value = false;
  }
}

async function agregarAlCarrito() {
  if (!auth.user) {
    router.push({ name: 'login', query: { redirect: route.fullPath } });
    return;
  }

  agregando.value = true;
  try {
    await addToCart(product.value.id, quantity.value);
    notify('Producto agregado al carrito');
  } catch (requestError) {
    notify(errorMessage(requestError), 'error');
  } finally {
    agregando.value = false;
  }
}
</script>

<style scoped>
.detail-page {
  max-width: 1180px;
  min-height: 65vh;
}

.back-button {
  margin-bottom: 20px;
}

.detail-image {
  position: relative;
  display: grid;
  min-height: 400px;
  overflow: hidden;
  place-items: center;
  border: 1px solid #e9e6df;
  border-radius: 16px;
  background: #f3f2ef;
}

.detail-image :deep(.v-img) {
  width: 100%;
  height: 100%;
  min-height: 400px;
}

.image-placeholder {
  color: #aaa59a;
}

.discount-chip {
  position: absolute;
  top: 16px;
  left: 16px;
  color: #242222;
  font-weight: 850;
}

.detail-copy {
  padding: clamp(8px, 2vw, 28px);
}

.product-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.product-meta span {
  padding: 6px 10px;
  border-radius: 99px;
  color: #5e5749;
  background: #f0eee8;
  font-size: 0.72rem;
  font-weight: 750;
}

.brand-name {
  margin: 20px 0 4px;
  color: #817a6e;
  font-size: 0.85rem;
  font-weight: 700;
}

.detail-copy h1 {
  margin: 8px 0 18px;
  color: #242222;
  font-size: clamp(1.8rem, 4vw, 2.8rem);
  font-weight: 900;
  letter-spacing: -0.045em;
  line-height: 1.08;
}

.detail-price {
  display: flex;
  align-items: baseline;
  gap: 12px;
}

.detail-price strong {
  color: #242222;
  font-size: 1.9rem;
  font-weight: 900;
}

.detail-price span {
  color: #99938a;
  text-decoration: line-through;
}

.stock-status {
  display: flex;
  align-items: center;
  gap: 7px;
  margin: 12px 0;
  color: #35834b;
  font-size: 0.85rem;
  font-weight: 700;
}

.stock-status.out-of-stock {
  color: #c62828;
}

.description {
  margin: 22px 0;
  color: #69645c;
  line-height: 1.7;
  white-space: pre-line;
}

.purchase-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 14px;
  margin-top: 24px;
}

.quantity-control {
  display: inline-flex;
  align-items: center;
  min-height: 44px;
  border: 1px solid #e1ddd5;
  border-radius: 8px;
  background: #fff;
}

.quantity-control span {
  min-width: 28px;
  text-align: center;
  font-weight: 750;
}

@media (max-width: 600px) {
  .detail-image,
  .detail-image :deep(.v-img) {
    min-height: 290px;
  }

  .purchase-row {
    align-items: stretch;
    flex-direction: column;
  }
}
</style>
