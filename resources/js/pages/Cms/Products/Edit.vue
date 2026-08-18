<script setup lang="ts">
import ProductForm from '@/components/cms/ProductForm.vue';
defineProps<{
    product: any;
    revision: any;
    categories: { id: number; name: string }[];
}>();
</script>
<template>
    <ProductForm
        :product="product"
        :revision="revision"
        :categories="categories"
    />
</template>
