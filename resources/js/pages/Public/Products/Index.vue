<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Product = {
    id: number;
    slug: string;
    name: string;
    image: string | null;
    category: { name: string; slug: string } | null;
};

type CategoryPage = {
    name: string;
    breadcrumbs: { id: number; name: string; slug: string }[];
    children: { id: number; name: string; slug: string; count: number }[];
};

defineProps<{
    products: { data: Product[] };
    category_page: CategoryPage | null;
}>();
</script>

<template>
    <Head :title="category_page?.name ?? 'Products'" />
    <Navbar />

    <main class="min-h-screen bg-background font-sans">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <nav v-if="category_page" aria-label="Breadcrumb" class="mb-6">
                <ol class="flex items-center space-x-2 text-xs text-muted-foreground sm:text-sm">
                    <li class="flex items-center"><Link href="/" class="transition-colors duration-150 hover:text-jack-blue">Home</Link></li>
                    <li v-for="(crumb, index) in category_page.breadcrumbs" :key="crumb.id" class="flex items-center">
                        <svg class="mx-1.5 size-4 shrink-0 text-muted-foreground/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        <Link :href="`/products?category=${encodeURIComponent(crumb.slug)}`" class="transition-colors duration-150 hover:text-jack-blue" :class="{ 'pointer-events-none font-semibold text-foreground': index === category_page.breadcrumbs.length - 1 }">{{ crumb.name }}</Link>
                    </li>
                </ol>
            </nav>

            <div class="mb-8">
                <h1 class="text-3xl font-extrabold tracking-normal text-foreground">{{ category_page?.name ?? 'All Products' }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">Discover industrial machinery solutions suited to your production needs.</p>
            </div>

            <div v-if="category_page?.children.length" class="mb-14 rounded-xl border border-border/60 bg-muted/30 p-5">
                <h2 class="mb-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Refined Sub-Categories</h2>
                <div class="flex flex-wrap gap-3">
                    <Link v-for="child in category_page.children" :key="child.id" :href="`/products?category=${encodeURIComponent(child.slug)}`" class="flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-foreground transition-all duration-150 hover:border-jack-blue/40 hover:text-jack-blue hover:shadow-sm">
                        <span>{{ child.name }}</span><span class="rounded-full bg-muted px-1.5 py-0.5 text-xs text-muted-foreground">{{ child.count }}</span>
                    </Link>
                </div>
            </div>

            <div class="mb-16 border-b border-border/60"></div>

            <section>
                <div class="mb-16">
                    <h2 class="text-4xl font-bold tracking-tight text-foreground sm:text-3xl">Available Equipment Models</h2>
                    <p class="mt-0.5 text-lg text-muted-foreground">Showing models matching your category selection</p>
                </div>

                <div v-if="products.data.length" class="my-12 grid grid-cols-1 justify-center gap-x-6 gap-y-16 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <article v-for="product in products.data" :key="product.id" class="group relative my-5 flex flex-col justify-between rounded-xl border border-border bg-card px-3.5 pt-20 pb-4 text-center shadow-sm transition-all duration-300 hover:border-jack-blue/30 hover:shadow-md">
                        <div class="pointer-events-none absolute -top-16 left-1/2 flex aspect-[600/550] w-5/6 max-w-[140px] -translate-x-1/2 items-center justify-center">
                            <img :src="product.image ?? '/E4S.png'" :alt="product.name" class="size-full object-contain transition-transform duration-300 group-hover:-translate-y-1" />
                        </div>
                        <div class="relative z-10 flex flex-1 flex-col justify-start">
                            <h3 class="text-xl font-bold text-foreground transition-colors duration-200 group-hover:text-jack-blue">{{ product.name }}</h3>
                            <p v-if="product.category" class="mt-1.5 line-clamp-2 min-h-[2.5rem] text-lg leading-relaxed text-muted-foreground">{{ product.category.name }}</p>
                        </div>
                        <div class="relative z-10 mt-5 space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <Link href="/contact" class="block rounded-md border border-border/80 bg-muted/10 py-1.5 text-sm text-foreground transition-all duration-150 hover:border-jack-blue/20 hover:bg-muted hover:text-jack-blue">Where To Buy</Link>
                                <Link :href="`/products/${product.slug}`" class="block rounded-md border border-border/80 bg-muted/10 py-1.5 text-sm text-foreground transition-all duration-150 hover:border-jack-blue/20 hover:bg-muted hover:text-jack-blue">View Model</Link>
                            </div>
                            <Link :href="`/products/${product.slug}`" class="block w-full rounded-md bg-jack-blue py-2 text-sm font-bold text-white shadow-sm transition-all duration-150 hover:bg-jack-blue/90 hover:shadow">See Details</Link>
                        </div>
                    </article>
                </div>
                <p v-else class="rounded-xl border border-dashed border-border p-10 text-center text-muted-foreground">No published products are available in this category yet.</p>
            </section>
        </div>
    </main>

    <Footer />
</template>
