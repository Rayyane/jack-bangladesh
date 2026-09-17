<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    title: string;
    description?: string | null;
    image?: string | null;
    type?: 'website' | 'product';
}>(), {
    description: null,
    image: null,
    type: 'website',
});

const canonical = computed(() =>
    typeof window === 'undefined' ? '' : window.location.href.split('#')[0],
);
</script>

<template>
    <Head :title="title">
        <meta v-if="description" head-key="description" name="description" :content="description" />
        <link v-if="canonical" head-key="canonical" rel="canonical" :href="canonical" />
        <meta head-key="og:type" property="og:type" :content="type" />
        <meta head-key="og:title" property="og:title" :content="title" />
        <meta v-if="description" head-key="og:description" property="og:description" :content="description" />
        <meta v-if="canonical" head-key="og:url" property="og:url" :content="canonical" />
        <meta v-if="image" head-key="og:image" property="og:image" :content="image" />
        <meta head-key="twitter:card" name="twitter:card" :content="image ? 'summary_large_image' : 'summary'" />
        <meta head-key="twitter:title" name="twitter:title" :content="title" />
        <meta v-if="description" head-key="twitter:description" name="twitter:description" :content="description" />
        <meta v-if="image" head-key="twitter:image" name="twitter:image" :content="image" />
    </Head>
</template>
