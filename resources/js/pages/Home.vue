<script setup lang="ts">
import FeaturedCategories from '@/components/custom/FeaturedCategories.vue';
import FeaturedProducts from '@/components/custom/FeaturedProducts.vue';
import Footer from '@/components/custom/FooterSection.vue';
import HeroSection from '@/components/custom/HeroSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Content = Record<string, unknown>;
type GalleryImage = { id: number; url: string; alt_text: string | null };
defineProps<{
    content?: Content | null;
    gallery?: GalleryImage[];
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
    <Navbar />
    <HeroSection :content="content" :gallery="gallery" />
    <FeaturedCategories :categories="featured_categories" />
    <FeaturedProducts :products="featured_products" />
    <Footer />
</template>
