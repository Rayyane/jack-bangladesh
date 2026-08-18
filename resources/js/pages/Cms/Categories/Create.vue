<script setup lang="ts">
import CategoryForm from '@/components/cms/CategoryForm.vue';

defineProps<{ parentOptions: { id: number; label: string }[] }>();
</script>

<template>
    <CategoryForm title="Create category" submit-url="/cms/categories" :parent-options="parentOptions" />
</template>
