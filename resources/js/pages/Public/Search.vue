<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { FolderSearch, Search } from '@lucide/vue';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Category = { 
    id: number; 
    name: string; 
    slug: string;
    path: string;
    full_label: string; 
};

type Product = {
    id: number;
    slug: string;
    name: string;
    category: { name: string; slug: string } | null;
};

defineProps<{
    query: string;
    categories: Category[];
    products: Product[];
}>();
</script>

<template>
    <Head :title="query ? 'Search: ' + query : 'Search'" />
    <Navbar />
    <main class="min-h-screen bg-background">
        <section class="bg-jack-blue py-14 text-white sm:py-18">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-orange-300 uppercase"
                >
                    Catalog search
                </p>
                <h1
                    class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    <template v-if="query">Results for “{{ query }}”</template>
                    <template v-else>Search products and categories</template>
                </h1>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="query && (categories.length || products.length)"
                class="grid gap-10 lg:grid-cols-[.7fr_1.3fr]"
            >
                <section>
                    <div class="flex items-center gap-2">
                        <FolderSearch class="size-5 text-jack-blue" />
                        <h2 class="text-xl font-bold">Categories</h2>
                    </div>
                    <div v-if="categories.length" class="mt-5 space-y-2">
                        <Link
                            v-for="category in categories"
                            :key="category.id"
                            :href="
                                '/products?category=' +
                                encodeURIComponent(category.slug)
                            "
                            class="flex items-center justify-between rounded-lg border border-border bg-card px-4 py-3 text-sm font-semibold transition hover:border-jack-blue/40 hover:text-jack-blue"
                        >
                            <div>
                                <p class="font-semibold">{{ category.name }}</p>
                                <!-- Breadcrumb trail — only shown when the category
                                     has ancestors, i.e. is not a root category. -->
                                <p
                                    v-if="category.path"
                                    class="mt-0.5 text-xs text-muted-foreground"
                                >
                                    {{ category.path }} › {{ category.name }}
                                </p>
                            </div>
                            <span class="text-muted-foreground"
                                >View category →</span
                            >
                        </Link>
                    </div>
                    <p
                        v-else
                        class="mt-5 rounded-lg border border-dashed border-border p-5 text-sm text-muted-foreground"
                    >
                        No categories match this search.
                    </p>
                </section>

                <section>
                    <div class="flex items-center gap-2">
                        <Search class="size-5 text-jack-blue" />
                        <h2 class="text-xl font-bold">Products</h2>
                    </div>
                    <div
                        v-if="products.length"
                        class="mt-5 grid gap-3 sm:grid-cols-2"
                    >
                        <Link
                            v-for="product in products"
                            :key="product.id"
                            :href="'/products/' + product.slug"
                            class="rounded-lg border border-border bg-card p-5 transition hover:border-jack-blue/40 hover:shadow-sm"
                        >
                            <p class="text-lg font-bold">{{ product.name }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{
                                    product.category?.name ??
                                    'Industrial sewing equipment'
                                }}
                            </p>
                            <span
                                class="mt-4 inline-block text-sm font-semibold text-jack-blue"
                                >View model →</span
                            >
                        </Link>
                    </div>
                    <p
                        v-else
                        class="mt-5 rounded-lg border border-dashed border-border p-5 text-sm text-muted-foreground"
                    >
                        No published products match this search.
                    </p>
                </section>
            </div>
            <div
                v-else
                class="mx-auto max-w-xl rounded-xl border border-dashed border-border p-10 text-center"
            >
                <Search class="mx-auto size-8 text-jack-blue" />
                <h2 class="mt-4 text-xl font-bold">
                    {{ query ? 'No matches found' : 'Start your search' }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{
                        query
                            ? 'Try a product model or a broader category name.'
                            : 'Use the search in the navigation to find a product or category.'
                    }}
                </p>
            </div>
        </div>
    </main>
    <Footer />
</template>
