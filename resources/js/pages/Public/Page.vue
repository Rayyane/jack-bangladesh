<script setup lang="ts">
import Home from '@/pages/Home.vue';
import AboutUs from '@/pages/AboutUs.vue';
import Contact from '@/pages/Contact.vue';

defineProps<{
    template_key: string;
    content: Record<string, unknown> | null;
    gallery?: { id: number; url: string; alt_text: string | null }[];
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
        category: { name: string; slug: string } | null;
        card_image: string | null;
        leaflet: string | null;
    }[];
}>();
</script>

<template>
    <Home
        v-if="template_key === 'home'"
        :content="content"
        :gallery="gallery"
        :featured_categories="featured_categories"
        :featured_products="featured_products"
    />
    <AboutUs
        v-else-if="template_key === 'about'"
        :content="content"
        :gallery="gallery"
    />
    <Contact v-else-if="template_key === 'contact'" :content="content" />
    <main v-else class="mx-auto min-h-screen max-w-5xl px-4 py-16">
        <h1 class="text-3xl font-semibold">
            {{ content?.title ?? template_key }}
        </h1>
        <p v-if="content?.description" class="mt-4 text-muted-foreground">
            {{ content.description }}
        </p>
    </main>
</template>
