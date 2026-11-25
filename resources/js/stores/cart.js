import { reactive } from 'vue';
import api from '../services/api';
import { auth } from './auth';

export const cart = reactive({ items: [], total: 0, count: 0 });

function apply(data) {
    cart.items = data.items;
    cart.total = data.total;
    cart.count = data.items.reduce((sum, item) => sum + item.quantity, 0);
}

export function clearCartState() {
    apply({ items: [], total: 0 });
}

export async function loadCart() {
    if (!auth.user) {
        clearCartState();
        return;
    }

    const { data } = await api.get('/cart');
    apply(data);
}

export async function addToCart(productId, quantity = 1) {
    const { data } = await api.post('/cart/items', { product_id: productId, quantity });
    apply(data);
}

export async function updateCartItem(itemId, quantity) {
    const { data } = await api.put(`/cart/items/${itemId}`, { quantity });
    apply(data);
}

export async function removeCartItem(itemId) {
    const { data } = await api.delete(`/cart/items/${itemId}`);
    apply(data);
}