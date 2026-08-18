<script setup lang="ts">
import { computed } from 'vue';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Section = {
    id: number;
    title: string;
    description: string | null;
    image_url: string | null;
    image_alt: string | null;
};
const props = defineProps<{
    product: { slug: string; category: { name: string } | null };
    revision: {
        name: string;
        description: string;
        price: string | null;
        video_url: string | null;
        primary_image_url: string | null;
        sections: Section[];
        specifications: { url: string } | null;
    };
}>();
const videoEmbedUrl = computed(() => {
    if (!props.revision.video_url) {
        return null;
    }

    const match = props.revision.video_url.match(
        /(?:youtu\.be\/|v=|embed\/)([^?&/]+)/,
    );

    return match
        ? `https://www.youtube.com/embed/${match[1]}`
        : props.revision.video_url;
});
</script>

<template>
    <Navbar />
    <main class="min-h-screen bg-background font-sans">
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-16"
            >
                <div
                    class="relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-2xl border border-border/40 bg-muted/30 p-8 lg:col-span-7 lg:p-12"
                >
                    <img
                        :src="revision.primary_image_url ?? ''"
                        :alt="`${revision.name} product display`"
                        class="max-h-full max-w-full object-contain drop-shadow-2xl"
                    />
                </div>
                <div class="space-y-6 lg:col-span-5">
                    <div class="space-y-2">
                        <span
                            v-if="product.category"
                            class="text-xs font-bold tracking-widest text-jack-blue uppercase"
                            >{{ product.category.name }}</span
                        >
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-foreground lg:text-5xl"
                        >
                            {{ revision.name }}
                        </h1>
                    </div>
                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ revision.description }}
                    </p>
                    <p v-if="revision.price" class="text-md leading-relaxed">
                        {{ revision.price }}
                    </p>
                    <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                        <a
                            href="#"
                            class="flex-1 rounded-lg bg-jack-blue py-3.5 text-center text-xs font-bold text-white"
                            >Locate Nearest Dealer</a
                        ><a
                            v-if="revision.specifications"
                            :href="revision.specifications.url"
                            target="_blank"
                            class="flex-1 rounded-lg border border-border bg-card py-3.5 text-center text-xs font-bold text-foreground"
                            >Download Official Leaflet</a
                        >
                    </div>
                </div>
            </div>
        </section>
        <section
            v-if="revision.sections.length"
            class="border-t border-border/40 bg-muted/10 py-20"
        >
            <div class="mx-auto max-w-7xl space-y-24 px-4 sm:px-6 lg:px-8">
                <div
                    v-for="(feature, index) in revision.sections"
                    :key="feature.id"
                    class="grid grid-cols-1 items-center gap-8 md:grid-cols-2 lg:gap-16"
                >
                    <div :class="['space-y-4', index % 2 ? 'md:order-2' : '']">
                        <h2
                            class="text-2xl font-extrabold tracking-normal text-foreground sm:text-3xl"
                        >
                            {{ feature.title }}
                        </h2>
                        <p
                            class="text-lg leading-relaxed text-muted-foreground"
                        >
                            {{ feature.description }}
                        </p>
                    </div>
                    <div
                        v-if="feature.image_url"
                        :class="[
                            'aspect-[16/10] overflow-hidden rounded-xl border border-border shadow-md',
                            index % 2 ? 'md:order-1' : '',
                        ]"
                    >
                        <img
                            :src="feature.image_url"
                            :alt="feature.image_alt ?? feature.title"
                            class="size-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>
        <section v-if="videoEmbedUrl" class="bg-neutral-950 py-20 text-white">
            <div
                class="mx-auto max-w-5xl space-y-8 px-4 text-center sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-2xl space-y-2">
                    <h2
                        class="text-2xl font-extrabold tracking-tight sm:text-3xl"
                    >
                        See the Machine in Action
                    </h2>
                </div>
                <div
                    class="aspect-video w-full overflow-hidden rounded-xl border border-neutral-800 bg-neutral-900 shadow-2xl"
                >
                    <iframe
                        :src="videoEmbedUrl"
                        class="size-full"
                        :title="`${revision.name} video`"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                        "
                        allowfullscreen
                    />
                </div>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-8 border-b border-border pb-4">
                <h2
                    class="text-2xl font-extrabold tracking-normal text-foreground"
                >
                    Technical Specifications
                </h2>
                <p class="mt-0.5 text-lg leading-relaxed text-muted-foreground">
                    Comprehensive engineering reference blueprints and
                    operational limits.
                </p>
            </div>
            <div
                class="w-full overflow-x-auto rounded-xl border border-border bg-card p-2 shadow-sm"
            >
                <img
                    :src="revision.specifications?.url ?? ''"
                    :alt="`${revision.name} technical specifications`"
                    class="h-auto w-full min-w-[768px] rounded-lg"
                />
            </div>
        </section>
    </main>
    <Footer />
</template>
