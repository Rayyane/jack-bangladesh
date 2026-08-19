<!-- HeroSection.vue -->
<script setup lang="ts">
import { computed } from 'vue';

type Content = Record<string, any>;
const props = defineProps<{ content?: Content | null }>();
const value = (paths: string[], fallback: string): string => {
    const found = paths
        .map((path) =>
            path
                .split('.')
                .reduce((current, key) => current?.[key], props.content),
        )
        .find((item) => typeof item === 'string' && item !== '');

    return typeof found === 'string' ? found : fallback;
};
const banner = (
    prefix: string,
    defaults: Record<string, string>,
    legacy: string[] = [],
) => ({
    image: value(
        [
            `${prefix}.image_url`,
            `${prefix}.image`,
            ...legacy.map((key) => `hero.${key}`),
        ],
        defaults.image,
    ),
    eyebrow: value([`${prefix}.eyebrow`, `${prefix}.label`], defaults.eyebrow),
    title: value([`${prefix}.title`, `${prefix}.headline`], defaults.title),
    description: value(
        [`${prefix}.description`, `${prefix}.body`],
        defaults.description,
    ),
    cta: value([`${prefix}.cta_label`, `${prefix}.button`], defaults.cta),
    href: value([`${prefix}.cta_url`, `${prefix}.button_url`], defaults.href),
});
const hero = computed(() => ({
    primary: banner(
        'hero.primary',
        {
            image: '/hero-1.png',
            eyebrow: 'New Arrival',
            title: 'Next-Gen Smart Automation Systems',
            description:
                'Boost your industrial production line precision with our latest AI-driven sewing frameworks.',
            cta: 'Explore Collection',
            href: '/products',
        },
        [
            'image',
            'image_url',
            'eyebrow',
            'label',
            'title',
            'headline',
            'description',
            'body',
            'cta_label',
            'button',
            'cta_url',
            'button_url',
        ],
    ),
    secondary: banner('hero.secondary', {
        image: '/hero-2.png',
        eyebrow: '',
        title: 'Heavy Duty Series',
        description: 'Engineered for leather & canvas.',
        cta: 'View Deals',
        href: '/products',
    }),
    tertiary: banner('hero.tertiary', {
        image: '/hero-3.png',
        eyebrow: 'Up to 30% Off',
        title: 'Genuine Spare Parts',
        description: '',
        cta: 'Shop Parts',
        href: '/products',
    }),
}));
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-6 font-sans sm:px-6 lg:px-8">
        <!-- Main 2-Column Grid Wrapper (2/3 and 1/3 split) -->
        <div
            class="grid grid-cols-1 items-stretch gap-4 md:grid-cols-3 lg:gap-6"
        >
            <!-- ========================================== -->
            <!-- LEFT COLUMN: 2/3 Width, Single Tall Poster -->
            <!-- ========================================== -->
            <div
                class="group relative h-[400px] overflow-hidden rounded-xl border border-border bg-card shadow-md md:col-span-2 md:h-[550px]"
            >
                <!-- Background Image -->
                <img
                    :src="hero.primary.image"
                    alt="Main Promo Poster"
                    class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
                <!-- Dark Overlay Gradient -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"
                ></div>

                <!-- Content Area -->
                <div
                    class="absolute inset-0 flex flex-col justify-end p-6 text-white sm:p-10"
                >
                    <span
                        class="mb-3 inline-block self-start rounded bg-jack-blue px-2.5 py-1 font-roboto text-xs font-bold tracking-wider uppercase"
                    >
                        {{ hero.primary.eyebrow }}
                    </span>
                    <h2
                        class="mb-2 max-w-xl text-2xl font-extrabold sm:text-4xl"
                    >
                        {{ hero.primary.title }}
                    </h2>
                    <p class="mb-6 max-w-md text-sm text-white/80 sm:text-base">
                        {{ hero.primary.description }}
                    </p>
                    <div>
                        <a
                            :href="hero.primary.href"
                            class="inline-block rounded-lg bg-white px-6 py-2.5 font-roboto text-sm font-semibold text-foreground shadow transition-colors duration-150 hover:bg-neutral-100"
                        >
                            {{ hero.primary.cta }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- RIGHT COLUMN: 1/3 Width, 2 Stacked Posters -->
            <!-- ========================================== -->
            <!-- flex-col makes them share the exact height bounds of the row -->
            <div
                class="flex h-auto flex-col justify-between gap-4 md:col-span-1 lg:gap-6"
            >
                <!-- Top Poster -->
                <div
                    class="group relative min-h-[300px] flex-1 overflow-hidden rounded-xl border border-border bg-card shadow-md md:min-h-[190px]"
                >
                    <img
                        :src="hero.secondary.image"
                        :alt="hero.secondary.title"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"
                    ></div>

                    <div
                        class="absolute inset-0 flex flex-col justify-end p-6 text-white"
                    >
                        <h3 class="mb-1 text-lg font-bold">
                            {{ hero.secondary.title }}
                        </h3>
                        <p class="mb-3 text-xs text-white/80">
                            {{ hero.secondary.description }}
                        </p>
                        <div>
                            <a
                                :href="hero.secondary.href"
                                class="inline-block rounded bg-white px-3 py-1.5 font-roboto text-xs font-bold text-jack-blue transition-colors hover:bg-neutral-100"
                            >
                                {{ hero.secondary.cta }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Poster -->
                <div
                    class="group relative min-h-[300px] flex-1 overflow-hidden rounded-xl border border-border bg-card shadow-md md:min-h-[190px]"
                >
                    <img
                        :src="hero.tertiary.image"
                        :alt="hero.tertiary.title"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"
                    ></div>

                    <div
                        class="absolute inset-0 flex flex-col justify-end p-6 text-white"
                    >
                        <span
                            class="mb-1 block text-xs font-bold tracking-wider text-orange-400 uppercase"
                            >{{ hero.tertiary.eyebrow }}</span
                        >
                        <h3 class="mb-3 text-lg font-bold">
                            {{ hero.tertiary.title }}
                        </h3>
                        <div>
                            <a
                                :href="hero.tertiary.href"
                                class="inline-block rounded border border-white/50 px-3 py-1.5 font-roboto text-xs font-bold text-white transition-all hover:bg-white hover:text-foreground"
                            >
                                {{ hero.tertiary.cta }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
