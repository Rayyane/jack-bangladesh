<script setup lang="ts">
import Home from '@/pages/Home.vue';
import AboutUs from '@/pages/AboutUs.vue';
import Contact from '@/pages/Contact.vue';
import SeoMeta from '@/components/SeoMeta.vue';

defineProps<{
    template_key: string;
    content: Record<string, unknown> | null;
    meta_title?: string | null;
    meta_description?: string | null;
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
    <SeoMeta
        :title="meta_title ?? (template_key === 'home' ? 'Jack Bangladesh' : template_key === 'about' ? 'About Jack Bangladesh' : 'Contact Jack Bangladesh')"
        :description="meta_description"
        :image="gallery?.find((image) => image.alt_text === `${template_key}-hero`)?.url ?? null"
    />
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
