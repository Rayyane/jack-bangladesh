<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    Check,
    Factory,
    Headphones,
    Settings2,
    Sparkles,
} from '@lucide/vue';
import { computed } from 'vue';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Content = Record<string, any>;
type GalleryImage = { id: number; url: string; alt_text: string | null };
const props = defineProps<{
    content?: Content | null;
    gallery?: GalleryImage[];
}>();
const text = (key: string, fallback: string): string => {
    const value = key
        .split('.')
        .reduce((current, part) => current?.[part], props.content);
    return typeof value === 'string' && value !== '' ? value : fallback;
};

const defaultStats = [
    { value: '24/7', label: 'Support when production matters' },
    { value: '1', label: 'Trusted partner for every line' },
    { value: '100%', label: 'Focused on industrial sewing' },
    { value: '∞', label: 'Possibilities to create' },
];
const defaultPillars = [
    {
        title: 'Machines that keep pace',
        description:
            'From everyday lockstitch to specialized automation, we help factories choose equipment that matches the fabric, operation and output they need.',
        icon: Factory,
    },
    {
        title: 'Confidence after installation',
        description:
            'Our relationship continues beyond delivery, with practical setup guidance and support designed to keep your sewing floor moving.',
        icon: Headphones,
    },
    {
        title: 'A smarter way to sew',
        description:
            'We bring together efficient technology and production know-how so teams can work with greater consistency, control and confidence.',
        icon: Settings2,
    },
];
const stats = computed(() =>
    defaultStats.map((stat, index) => ({
        value: text(`stats.${index}.value`, stat.value),
        label: text(`stats.${index}.label`, stat.label),
    })),
);
const pillars = computed(() =>
    defaultPillars.map((pillar, index) => ({
        ...pillar,
        title: text(`pillars.${index}.title`, pillar.title),
        description: text(`pillars.${index}.description`, pillar.description),
    })),
);
const imageFor = (slot: string, fallback: string): string =>
    props.gallery?.find((image) => image.alt_text === 'about-' + slot)?.url ??
    fallback;
</script>

<template>
    <Head :title="text('meta_title', 'About Jack Bangladesh')" />
    <Navbar />
    <main class="overflow-hidden bg-background font-sans text-foreground">
        <section
            class="relative isolate overflow-hidden bg-jack-blue py-18 text-white sm:py-24 lg:py-28"
        >
            <div
                class="absolute inset-0 [background-image:linear-gradient(to_right,white_1px,transparent_1px),linear-gradient(to_bottom,white_1px,transparent_1px)] [background-size:40px_40px] opacity-20"
            ></div>
            <div
                class="absolute top-1/2 -right-20 size-96 -translate-y-1/2 rounded-full border-[48px] border-white/10"
            ></div>
            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:gap-20 lg:px-8"
            >
                <div>
                    <div
                        class="mb-5 flex items-center gap-2 text-xs font-bold tracking-[0.2em] text-orange-300 uppercase"
                    >
                        <span class="size-2 rounded-full bg-orange-300"></span
                        >{{ text('hero.eyebrow', 'Jack Bangladesh') }}
                    </div>
                    <h1
                        class="max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl"
                    >
                        {{
                            text(
                                'hero.title',
                                'Technology that moves the people who make things.',
                            )
                        }}
                    </h1>
                    <p
                        class="mt-6 max-w-2xl text-base leading-7 text-white/80 sm:text-lg"
                    >
                        {{
                            text(
                                'hero.description',
                                'We connect Bangladesh’s apparel makers with dependable industrial sewing technology, practical expertise and the support to grow with confidence.',
                            )
                        }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="/products"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-jack-blue transition hover:bg-white/90"
                            >Explore machines <ArrowRight class="size-4"
                        /></a>
                        <a
                            href="/contact"
                            class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                            >Talk to our team</a
                        >
                    </div>
                </div>
                <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                    <div
                        class="absolute -inset-5 rounded-[2rem] bg-orange-300/15 blur-2xl"
                    ></div>
                    <div
                        class="relative rounded-2xl border border-white/20 bg-white/10 p-5 shadow-2xl backdrop-blur-sm sm:p-7"
                    >
                        <div
                            class="flex items-center justify-between border-b border-white/15 pb-5"
                        >
                            <span class="text-sm font-semibold"
                                >Built around your floor</span
                            ><Sparkles class="size-5 text-orange-300" />
                        </div>
                        <img
                            :src="imageFor('hero', '/F6.png')"
                            alt="Jack industrial sewing machine"
                            class="mx-auto h-55 w-full object-contain sm:h-64"
                        />
                        <div
                            class="grid grid-cols-2 gap-3 border-t border-white/15 pt-5 text-sm"
                        >
                            <div>
                                <span class="block text-xs text-white/60"
                                    >Purpose</span
                                ><strong>Better output</strong>
                            </div>
                            <div>
                                <span class="block text-xs text-white/60"
                                    >Approach</span
                                ><strong>Practical support</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section
            class="relative z-10 mx-auto -mt-7 max-w-7xl px-4 sm:px-6 lg:px-8"
        >
            <div
                class="grid grid-cols-2 rounded-xl border border-border bg-card p-2 shadow-lg md:grid-cols-4 md:p-4"
            >
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="border-border px-4 py-4 text-center even:border-l md:border-l md:first:border-l-0"
                >
                    <p
                        class="text-2xl font-extrabold text-jack-blue sm:text-3xl"
                    >
                        {{ stat.value }}
                    </p>
                    <p class="mt-1 text-xs leading-4 text-muted-foreground">
                        {{ stat.label }}
                    </p>
                </div>
            </div>
        </section>
        <section
            class="mx-auto grid max-w-7xl gap-12 px-4 py-18 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:gap-20 lg:px-8 lg:py-24"
        >
            <div class="relative order-2 lg:order-1">
                <div
                    class="absolute inset-0 translate-x-3 translate-y-3 rounded-2xl bg-orange-300/50"
                ></div>
                <div
                    class="relative flex min-h-80 flex-col justify-end overflow-hidden rounded-2xl bg-muted p-8"
                >
                    <div class="absolute inset-0 bg-jack-blue/75"></div>
                    <div class="relative max-w-xs text-white">
                        <p
                            class="text-xs font-bold tracking-[0.18em] text-orange-300 uppercase"
                        >
                            Our promise
                        </p>
                        <p
                            class="mt-3 text-2xl leading-tight font-bold text-gray-600"
                        >
                            The right machine is only the beginning.
                        </p>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase"
                >
                    Who we are
                </p>
                <h2
                    class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    A local partner for every stitch of progress.
                </h2>
                <div
                    class="mt-6 space-y-4 text-base leading-7 text-muted-foreground"
                >
                    <p>
                        Jack Bangladesh serves the people behind one of the
                        world’s most dynamic apparel industries. Our work is
                        rooted in a simple belief: production technology should
                        make skilled work more capable, not more complicated.
                    </p>
                    <p>
                        Whether you are setting up a new line or improving an
                        established one, we help you find practical equipment
                        solutions that fit the way your team works.
                    </p>
                </div>
                <ul class="mt-7 space-y-3 text-sm font-medium text-foreground">
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >Industrial sewing expertise, made approachable
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >Solutions for evolving production needs
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >A team that stays close to the work
                    </li>
                </ul>
            </div>
        </section>
        <section class="border-y border-border bg-muted/35 py-18 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p
                        class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase"
                    >
                        How we help
                    </p>
                    <h2
                        class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                    >
                        More than a machine supplier.
                    </h2>
                </div>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <article
                        v-for="pillar in pillars"
                        :key="pillar.title"
                        class="rounded-xl border border-border bg-card p-6 shadow-sm"
                    >
                        <component
                            :is="pillar.icon"
                            class="size-7 text-jack-blue"
                        />
                        <h3 class="mt-5 text-xl font-bold">
                            {{ pillar.title }}
                        </h3>
                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            {{ pillar.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
            <div
                class="rounded-2xl bg-jack-blue px-6 py-10 text-center text-white sm:px-12 sm:py-14"
            >
                <p
                    class="text-xs font-bold tracking-[0.18em] text-orange-300 uppercase"
                >
                    Let’s build what’s next
                </p>
                <h2
                    class="mx-auto mt-3 max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    Ready to make your production line work smarter?
                </h2>
                <p
                    class="mx-auto mt-4 max-w-xl text-sm leading-6 text-white/80"
                >
                    Explore the Jack range or speak with our team about the
                    right solution for your operation.
                </p>
                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a
                        :href="text('cta.url', '/products')"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-jack-blue transition hover:bg-white/90"
                        >{{ text('cta.label', 'Explore the product range') }}
                        <ArrowRight class="size-4" /></a
                    ><a
                        href="/contact"
                        class="rounded-lg border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >Contact us</a
                    >
                </div>
            </div>
        </section>
    </main>
    <Footer />
</template>
