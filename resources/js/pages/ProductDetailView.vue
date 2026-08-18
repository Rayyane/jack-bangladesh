<!-- ProductDetailView.vue -->
<script setup lang="ts">
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

// Mock Product Meta Core
const product = {
    name: 'Jack E4S',
    subText:
        'Advanced high-speed overlock machine integrated with a physical L/M/H (Light / Medium / Heavy) material selector switch, designed to instantly adapt torque and feeding characteristics across variable fabric weight profiles.',
    price: '৳ 90,000',
};

// Deep dive alternating feature sections
const dynamicFeatures = [
    {
        id: 1,
        title: 'Improved performance',
        body: 'It can sew 7% thicker material, for example it can easily sew 15 layers of jeans thanks to the AMH1 sewing organs.',
        imgSrc: '/card1.jpg',
    },
    {
        id: 2,
        title: 'Highly reliable electronics',
        body: 'It can continue sewing under unstable currents from 110V to 295V without slowing down on thicknesses. It also withstands static current peaks of up to 12,000V, which is the primary cause of damage to electronic components.',
        imgSrc: '/card2.jpg',
    },
];
</script>

<template>
    <Navbar />
    <div class="min-h-screen bg-background font-sans">
        <!-- ========================================== -->
        <!-- ZONE 1: PRODUCT HERO SECTION               -->
        <!-- ========================================== -->
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-16"
            >
                <!-- Left: Floating Machine Stage (7/12 Width) -->
                <div
                    class="group relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-2xl border border-border/40 bg-muted/30 p-8 lg:col-span-7 lg:p-12"
                >
                    <!-- Ambient structural grid graphics for visual polish -->
                    <div
                        class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"
                    ></div>

                    <img
                        src="/E4S.png"
                        alt="Jack E4S Product Display"
                        class="relative z-10 max-h-full max-w-full object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]"
                    />
                </div>

                <!-- Right: Purchase Hook Specifications (5/12 Width) -->
                <div class="space-y-6 lg:col-span-5">
                    <div class="space-y-2">
                        <span
                            class="font-roboto text-xs font-bold tracking-widest text-jack-blue uppercase"
                            >High-Speed Overlock</span
                        >
                        <h1
                            class="text-4xl font-extrabold tracking-tight text-foreground lg:text-5xl"
                        >
                            {{ product.name }}
                        </h1>
                    </div>

                    <p
                        class="font-sans text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ product.subText }}
                    </p>

                    <p class="text-md font-sans leading-relaxed">
                        {{ product.price }}
                    </p>

                    <!-- Core Call To Action Panel -->
                    <div
                        class="flex flex-col gap-3 pt-4 font-roboto sm:flex-row"
                    >
                        <a
                            href="#"
                            class="flex-1 rounded-lg bg-jack-blue py-3.5 text-center text-xs font-bold text-white shadow-sm transition-all hover:bg-jack-blue/90"
                        >
                            Locate Nearest Dealer
                        </a>
                        <a
                            href="#"
                            class="flex-1 rounded-lg border border-border bg-card py-3.5 text-center text-xs font-bold text-foreground transition-all hover:bg-muted"
                        >
                            Download Official Leaflet
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- ZONE 2: ALTERNATING TECHNICAL FEATURES     -->
        <!-- ========================================== -->
        <section class="border-t border-border/40 bg-muted/10 py-20">
            <div class="mx-auto max-w-7xl space-y-24 px-4 sm:px-6 lg:px-8">
                <div
                    v-for="(feature, idx) in dynamicFeatures"
                    :key="feature.id"
                    class="grid grid-cols-1 items-center gap-8 md:grid-cols-2 lg:gap-16"
                >
                    <!-- Alternating Layout Sorting Mechanics using conditional flex orders -->
                    <div
                        :class="[
                            'space-y-4',
                            idx % 2 === 1 ? 'md:order-2' : '',
                        ]"
                    >
                        <h2
                            class="text-2xl font-extrabold tracking-normal text-foreground sm:text-3xl"
                        >
                            {{ feature.title }}
                        </h2>
                        <p
                            class="font-sans text-lg leading-relaxed text-muted-foreground"
                        >
                            {{ feature.body }}
                        </p>
                    </div>

                    <div
                        :class="[
                            'aspect-[16/10] overflow-hidden rounded-xl border border-border shadow-md',
                            idx % 2 === 1 ? 'md:order-1' : '',
                        ]"
                    >
                        <img
                            :src="feature.imgSrc"
                            :alt="feature.title"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- ZONE 3: PRESTIGE YOUTUBE MEDIA THEATER    -->
        <!-- ========================================== -->
        <section class="bg-neutral-950 py-20 text-white">
            <div
                class="mx-auto max-w-5xl space-y-8 px-4 text-center sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-2xl space-y-2">
                    <h2
                        class="text-2xl font-extrabold tracking-tight sm:text-3xl"
                    >
                        See the Machine in Action
                    </h2>
                    <p class="font-roboto text-sm text-neutral-400">
                        Watch real-world fabric tracking speed tests and
                        operational adjustments.
                    </p>
                </div>

                <!-- Fully Responsive Video Container Frame wrapper maintaining a crisp 16:9 box -->
                <div
                    class="aspect-video w-full overflow-hidden rounded-xl border border-neutral-800 bg-neutral-900 shadow-2xl"
                >
                    <iframe
                        class="h-full w-full"
                        src="https://www.youtube.com/embed/8_ympqgXpK0?si=fg29ftG11Pyfdjrp"
                        title="YouTube video player"
                        frameborder="0"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                            web-share;
                        "
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- ZONE 4: FULL-WIDTH TECHNICAL SPEC SHEET    -->
        <!-- ========================================== -->
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

            <!-- Full-Width Spec Sheet Canvas Frame -->
            <div
                class="w-full overflow-x-auto rounded-xl border border-border bg-card p-2 shadow-sm"
            >
                <!-- Leave the source string handling to you -->
                <img
                    src="/specs.jpg"
                    alt="Technical Specification Matrix Blueprint"
                    class="h-auto w-full min-w-[768px] rounded-lg"
                />
            </div>
        </section>
    </div>
    <Footer />
</template>
