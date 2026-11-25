import { reactive } from 'vue';

export const toast = reactive({ show: false, text: '', color: 'success' });

export function notify(text, color = 'success') {
    toast.text = text;
    toast.color = color;
    toast.show = true;
}