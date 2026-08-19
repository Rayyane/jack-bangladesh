<script setup lang="ts">
import Home from '@/pages/Home.vue';

defineProps<{
    template_key: string;
    content: Record<string, unknown> | null;
    featured_categories?: {
        id: number;
        name: string;
        slug: string;
        image: string | null;
    }[];
    featured_products?: {
        id: number;
        slug: string;
        name: string;
        description: string;
        image: string | null;
        leaflet: string | null;
    }[];
}>();
</script>

<template>
    <Home
        v-if="template_key === 'home'"
        :content="content"
        :featured-categories="featured_categories"
        :featured-products="featured_products"
    />
    <main v-else class="mx-auto min-h-screen max-w-5xl px-4 py-16">
        <h1 class="text-3xl font-semibold">
            {{ content?.title ?? template_key }}
        </h1>
        <p v-if="content?.description" class="mt-4 text-muted-foreground">
            {{ content.description }}
        </p>
    </main>
</template>
