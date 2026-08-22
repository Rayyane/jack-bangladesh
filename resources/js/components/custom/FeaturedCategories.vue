<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    categories?: {
        id: number;
        name: string;
        slug: string;
        image: string | null;
    }[];
}>();
const categories = computed(() =>
    props.categories?.length
        ? props.categories
        : [
              { id: 1, name: 'Lockstitch Machines' },
              { id: 2, name: 'Overlock Series' },
              { id: 3, name: 'Heavy Duty' },
              { id: 4, name: 'Automatic Templates' },
              { id: 5, name: 'Genuine Motors' },
              { id: 6, name: 'Spare Parts & Tools' },
              { id: 7, name: 'Ironing' },
              { id: 8, name: 'Automatic' },
              { id: 9, name: 'Motors' },
              { id: 10, name: 'Zig Zag' },
              { id: 11, name: 'Post Bed' },
              { id: 12, name: 'Interlock Series' },
          ].map((category) => ({ ...category, slug: '', image: null })),
);
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-12 font-sans sm:px-6 lg:px-8">
        <div class="my-6 mb-16">
            <h2
                class="text-4xl font-extrabold tracking-normal text-foreground sm:text-3xl"
            >
                Featured Categories
            </h2>
            <p class="mt-1 font-roboto text-lg text-muted-foreground">
                Curated Just For You
            </p>
        </div>

        <div
            class="grid grid-cols-2 justify-center gap-x-4 gap-y-16 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
        >
            <Link
                v-for="category in categories"
                :key="category.id"
                :href="`/products?category=${encodeURIComponent(category.slug)}`"
                class="group relative my-5 rounded-xl border border-border bg-card px-4 pt-20 pb-5 text-center shadow-sm transition-all duration-300 hover:border-jack-blue/30 hover:shadow-md"
            >
                <div
                    class="pointer-events-none absolute -top-12 left-1/2 flex aspect-[600/550] w-5/6 max-w-[140px] -translate-x-1/2 items-center justify-center"
                >
                    <img
                        :src="category.image ?? '/1790.png'"
                        :alt="category.name"
                        class="mx-auto h-full w-full object-contain transition-transform duration-300 group-hover:-translate-y-1"
                    />

                    <!-- <div class="absolute inset-0 border border-dashed border-muted-foreground/30 rounded flex items-center justify-center bg-muted/30 backdrop-blur-[1px]">
            <span class="text-[10px] font-mono text-muted-foreground/60">600x550 image</span>
          </div> -->
                </div>

                <div class="relative z-10">
                    <h3
                        class="line-clamp-2 flex min-h-[2.5rem] items-center justify-center text-xs font-bold text-foreground transition-colors duration-200 group-hover:text-jack-blue sm:text-sm"
                    >
                        {{ category.name }}
                    </h3>
                </div>
            </Link>
        </div>
    </section>
</template>
