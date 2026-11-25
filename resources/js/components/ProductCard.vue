<template>
  <v-card class="product-card h-100 d-flex flex-column" elevation="0">
    <div class="product-image">
      <v-img v-if="product.image" :src="product.image" height="190" cover :alt="product.name" />
      <div v-else class="image-placeholder">
        <v-icon size="58">mdi-image-outline</v-icon>
      </div>
      <v-chip v-if="precio.discount" class="discount-badge" color="primary" size="small" variant="flat">
        -{{ precio.discount }}%
      </v-chip>
    </div>

    <v-card-text class="product-copy flex-grow-1">
      <div class="d-flex align-center justify-space-between mb-2">
        <span v-if="info" class="company-label">{{ info.name }}</span>
        <v-chip v-if="soldOut" size="x-small" color="error" variant="tonal">Agotado</v-chip>
      </div>
      <div class="product-name">{{ product.name }}</div>
      <div v-if="product.brand" class="product-brand">{{ product.brand }}</div>

      <div class="product-price mt-3">
        <span class="current-price">{{ formatMoney(precio.final) }}</span>
        <span v-if="precio.discount" class="old-price">
          {{ formatMoney(precio.price) }}
        </span>
      </div>
    </v-card-text>

    <v-card-actions class="product-actions">
      <v-btn :to="`/producto/${product.id}`" variant="text" class="detail-button">Detalles</v-btn>
      <v-spacer />
      <v-btn color="primary" variant="flat" :disabled="soldOut" :loading="cargando" icon aria-label="Agregar al carrito" @click="agregar">
        <v-icon>mdi-cart-plus</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { errorMessage } from '../services/api';
import { formatMoney, productPrice } from '../services/format';
import { auth } from '../stores/auth';
import { addToCart } from '../stores/cart';
import { company } from '../stores/company';
import { notify } from '../stores/toast';

const props = defineProps({ product: { type: Object, required: true } });
const router = useRouter();
const cargando = ref(false);

const soldOut = computed(() => props.product.status === 'sold_out' || props.product.stock < 1);
const precio = computed(() => productPrice(props.product));
const info = computed(() =>
  company.list.find((c) => c.id === (props.product.company_id ?? props.product.company?.id))
);

async function agregar() {
  if (!auth.user) {
    router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } });
    return;
  }

  cargando.value = true;
  try {
    await addToCart(props.product.id, 1);
    notify('Producto agregado al carrito');
  } catch (e) {
    notify(errorMessage(e), 'error');
  } finally {
    cargando.value = false;
  }
}
</script>

<style scoped>
.product-card {
  overflow: hidden;
  border: 1px solid #eae7e0;
  border-radius: 13px;
  background: #fff;
  transition: box-shadow 180ms ease, transform 180ms ease;
}

.product-card:hover {
  box-shadow: 0 12px 28px rgb(34 32 33 / 11%) !important;
  transform: translateY(-3px);
}

.product-image {
  position: relative;
  overflow: hidden;
  background: #f3f2ef;
}

.image-placeholder {
  display: grid;
  height: 190px;
  place-items: center;
  color: #aaa59a;
}

.discount-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  color: #242222;
  font-weight: 850;
}

.product-copy {
  padding: 16px 16px 10px;
}

.company-label {
  overflow: hidden;
  color: #827b6e;
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 0.07em;
  text-overflow: ellipsis;
  text-transform: uppercase;
  white-space: nowrap;
}

.product-name {
  display: -webkit-box;
  overflow: hidden;
  min-height: 2.8em;
  color: #292725;
  font-size: 0.95rem;
  font-weight: 750;
  line-height: 1.4;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.product-brand {
  margin-top: 4px;
  color: #8a857a;
  font-size: 0.78rem;
}

.product-price {
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 8px;
}

.current-price {
  color: #242222;
  font-size: 1.15rem;
  font-weight: 900;
}

.old-price {
  color: #99938a;
  font-size: 0.78rem;
  text-decoration: line-through;
}

.product-actions {
  padding: 8px 16px 16px;
}

.detail-button {
  padding-inline: 0;
  color: #6f685c;
  font-size: 0.78rem;
  font-weight: 700;
}
</style>