<!-- CategoryView.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import Navbar from '@/components/custom/Navbar.vue';
import Footer from '@/components/custom/FooterSection.vue';

// Mock active breadcrumb data path
const breadcrumbs = [
    { id: 1, name: 'Home', url: '/' },
    { id: 2, name: 'Sewing Machines', url: '/categories/sewing' },
    { id: 3, name: 'Lockstitch', url: '/categories/lockstitch' },
];

// Mock data for nested sub-categories inside the current selection
const childCategories = [
    { id: 101, name: 'Basic Mechanical', count: 4 },
    { id: 102, name: 'Computerized Auto-Trimmer', count: 8 },
    { id: 103, name: 'Heavy Duty Thick Fabric', count: 5 },
    { id: 104, name: 'Integrated Direct-Drive', count: 3 },
];

// Mock product data leveraging your exact template specs
const products = [
    { id: 1, name: 'Jack A2B', subText: 'Power-saving lockstitch machine with automatic thread trimmer' },
    { id: 2, name: 'Jack A4F', subText: 'Smart computerized lockstitch with digital stitch adaptation' },
    { id: 3, name: 'Jack A5E-A', subText: 'Artificial Intelligence lockstitch framework for adaptive feeding' },
    { id: 4, name: 'Jack A7', subText: 'Digitalized dynamic feeding system for variable fabric weight profiles' },
];
</script>

<template>
    <Navbar />
    <div class="min-h-screen bg-background font-sans">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">

            <!-- ========================================== -->
            <!-- 1. BREADCRUMBS OPTION                      -->
            <!-- ========================================== -->
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex items-center space-x-2 text-xs sm:text-sm font-roboto text-muted-foreground">
                    <li v-for="(crumb, index) in breadcrumbs" :key="crumb.id" class="flex items-center">
                        <!-- Chevron divider icon before items, except the first item -->
                        <svg v-if="index > 0" class="w-4 h-4 mx-1.5 text-muted-foreground/50 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>

                        <!-- Safe Link state changes -->
                        <a :href="crumb.url" class="hover:text-jack-blue transition-colors duration-150"
                            :class="{ 'text-foreground font-semibold pointer-events-none': index === breadcrumbs.length - 1 }">
                            {{ crumb.name }}
                        </a>
                    </li>
                </ol>
            </nav>

            <!-- Current Page Heading Context -->
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold tracking-normal text-foreground">Lockstitch Machines</h1>
                <p class="text-sm text-muted-foreground mt-1">Discover standard industrial frameworks and computerized
                    automated machinery solutions.</p>
            </div>

            <!-- ========================================== -->
            <!-- 2. CHILD CATEGORIES BLOCK                  -->
            <!-- ========================================== -->
            <div v-if="childCategories.length > 0" class="mb-14 bg-muted/30 border border-border/60 rounded-xl p-5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-4 font-roboto">
                    Refined Sub-Categories
                </h2>
                <!-- Flexible pill/card list for nested sub-categories -->
                <div class="flex flex-wrap gap-3">
                    <a v-for="subCat in childCategories" :key="subCat.id" href="#"
                        class="bg-card border border-border rounded-lg px-4 py-2.5 text-sm font-medium text-foreground hover:border-jack-blue/40 hover:text-jack-blue hover:shadow-sm transition-all duration-150 flex items-center gap-2">
                        <span>{{ subCat.name }}</span>
                        <span
                            class="text-xs bg-muted text-muted-foreground px-1.5 py-0.5 rounded-full group-hover:bg-jack-blue/10">
                            {{ subCat.count }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- Separator line for aesthetic rhythm -->
            <div class="border-b border-border/60 mb-16"></div>

            <!-- ========================================== -->
            <!-- 3. FEATURED PRODUCTS SECTION               -->
            <!-- ========================================== -->
            <div>
                <div class="mb-16">
                    <h2 class="text-4xl sm:text-3xl font-bold tracking-tight text-foreground">
                        Available Equipment Models
                    </h2>
                    <p class="text-lg text-muted-foreground font-roboto mt-0.5">
                        Showing baseline solutions matching your configuration parameters
                    </p>
                </div>

                <!-- Integrated with your specific modified product card grid -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-16 justify-center my-12">

                    <div v-for="product in products" :key="product.id"
                        class="relative bg-card border border-border rounded-xl pb-4 px-3.5 text-center shadow-sm hover:shadow-md hover:border-jack-blue/30 transition-all duration-300 group pt-20 my-5 flex flex-col justify-between">
                        <!-- Floated Image Hook using your -top-16 specification -->
                        <div
                            class="absolute -top-16 left-1/2 -translate-x-1/2 w-5/6 max-w-[140px] aspect-[600/550] flex items-center justify-center pointer-events-none">
                            <img src="/E4S.png" alt="Product Image"
                                class="w-full h-full object-contain mx-auto transition-transform duration-300 group-hover:-translate-y-1" />
                        </div>

                        <!-- Metadata Layout Context -->
                        <div class="relative z-10 flex-1 flex flex-col justify-start">
                            <h3
                                class="text-xl font-bold text-foreground group-hover:text-jack-blue transition-colors duration-200">
                                {{ product.name }}
                            </h3>
                            <p
                                class="text-lg text-muted-foreground mt-1.5 font-sans leading-relaxed min-h-[2.5rem] line-clamp-2">
                                {{ product.subText }}
                            </p>
                        </div>

                        <!-- Interactive Core Options tray -->
                        <div class="relative z-10 mt-5 space-y-2 font-roboto">
                            <div class="grid grid-cols-2 gap-2">
                                <a href="#"
                                    class="block text-sm text-foreground bg-muted/10 border border-border/80 rounded-md py-1.5 hover:bg-muted hover:text-jack-blue hover:border-jack-blue/20 transition-all duration-150">
                                    Where To Buy
                                </a>
                                <a href="#"
                                    class="block text-sm text-foreground bg-muted/10 border border-border/80 rounded-md py-1.5 hover:bg-muted hover:text-jack-blue hover:border-jack-blue/20 transition-all duration-150">
                                    Leaflet
                                </a>
                            </div>

                            <a href="#"
                                class="block w-full text-sm font-bold text-white bg-jack-blue rounded-md py-2 shadow-sm hover:bg-jack-blue/90 hover:shadow transition-all duration-150">
                                See Details
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <Footer />
</template>