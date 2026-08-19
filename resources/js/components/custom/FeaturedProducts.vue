<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    products?: {
        id: number;
        slug: string;
        name: string;
        description: string;
        image: string | null;
        leaflet: string | null;
    }[];
}>();
const products = computed(() =>
    props.products?.length
        ? props.products
        : [
              {
                  id: 1,
                  name: 'Jack E4S',
                  subText: 'Standard overlock with L/M/H feeding selector',
              },
              {
                  id: 2,
                  name: 'Jack C5T',
                  subText: 'Digital top feed high-speed overlock machine',
              },
              {
                  id: 3,
                  name: 'Jack A4C',
                  subText:
                      'Digitalized flex-stitch lockstitch with auto footlifter',
              },
              {
                  id: 4,
                  name: 'Jack C7 URUS',
                  subText: 'AI-driven ultra-heavy duty smart fabric cutter',
              },
              {
                  id: 5,
                  name: 'Jack K7 UT',
                  subText:
                      'Intelligent cylinder-bed interlock template machine',
              },
              {
                  id: 6,
                  name: 'Jack W4',
                  subText: 'Flatbed high-speed interlock utility system',
              },
              {
                  id: 7,
                  name: 'Jack E4S',
                  subText: 'Standard overlock with L/M/H feeding selector',
              },
              {
                  id: 8,
                  name: 'Jack C5T',
                  subText: 'Digital top feed high-speed overlock machine',
              },
          ].map((product) => ({
              ...product,
              slug: '',
              description: product.subText,
              image: null,
              leaflet: null,
          })),
);
</script>

<template>
    <section
        class="mx-auto max-w-7xl border-t border-border/40 px-4 py-12 font-sans sm:px-6 lg:px-8"
    >
        <div class="my-6 mb-16">
            <h2
                class="text-4xl font-extrabold tracking-normal text-foreground sm:text-3xl"
            >
                Featured Products
            </h2>
            <p class="mt-1 font-roboto text-lg text-muted-foreground">
                Engineered for Precision, Built to Last
            </p>
        </div>

        <div
            class="grid grid-cols-1 justify-center gap-x-6 gap-y-16 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
        >
            <div
                v-for="product in products"
                :key="product.id"
                class="group relative my-5 flex flex-col justify-between rounded-xl border border-border bg-card px-3.5 pt-20 pb-4 text-center shadow-sm transition-all duration-300 hover:border-jack-blue/30 hover:shadow-md"
            >
                <div
                    class="pointer-events-none absolute -top-16 left-1/2 flex aspect-[600/550] w-5/6 max-w-[140px] -translate-x-1/2 items-center justify-center"
                >
                    <img
                        :src="product.image ?? '/E4S.png'"
                        :alt="product.name"
                        class="mx-auto h-full w-full object-contain transition-transform duration-300 group-hover:-translate-y-1"
                    />
                </div>

                <div class="relative z-10 flex flex-1 flex-col justify-start">
                    <h3
                        class="text-xl font-bold text-foreground transition-colors duration-200 group-hover:text-jack-blue"
                    >
                        {{ product.name }}
                    </h3>
                    <p
                        class="mt-1.5 line-clamp-2 min-h-[2.5rem] font-sans text-lg leading-relaxed text-muted-foreground"
                    >
                        {{ product.description }}
                    </p>
                </div>

                <div class="relative z-10 mt-5 space-y-2 font-roboto">
                    <div class="grid grid-cols-2 gap-2">
                        <a
                            href="/contact"
                            class="block rounded-md border border-border/80 bg-muted/10 py-1.5 text-sm text-foreground transition-all duration-150 hover:border-jack-blue/20 hover:bg-muted hover:text-jack-blue"
                        >
                            Where To Buy
                        </a>
                        <a
                            :href="
                                product.leaflet ?? `/products/${product.slug}`
                            "
                            class="block rounded-md border border-border/80 bg-muted/10 py-1.5 text-sm text-foreground transition-all duration-150 hover:border-jack-blue/20 hover:bg-muted hover:text-jack-blue"
                        >
                            Leaflet
                        </a>
                    </div>

                    <a
                        :href="`/products/${product.slug}`"
                        class="block w-full rounded-md bg-jack-blue py-2 text-sm font-bold text-white shadow-sm transition-all duration-150 hover:bg-jack-blue/90 hover:shadow"
                    >
                        See Details
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
