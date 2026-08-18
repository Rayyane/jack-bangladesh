<script setup lang="ts">
import ProductForm from '@/components/cms/ProductForm.vue';
defineProps<{ categories: { id: number; name: string }[] }>();
</script>
<template><ProductForm :categories="categories" /></template>
