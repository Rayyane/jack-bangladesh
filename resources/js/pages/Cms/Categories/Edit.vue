<script setup lang="ts">
import CategoryForm from '@/components/cms/CategoryForm.vue';

defineProps<{
    category: { id: number; name: string; parent_id: number | null; sort_order: number; is_featured: boolean; show_in_nav: boolean; image_url: string | null };
    parentOptions: { id: number; label: string }[];
}>();
</script>

<template>
    <CategoryForm title="Edit category" :submit-url="`/cms/categories/${category.id}`" :parent-options="parentOptions" :category="category" />
</template>
