<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import NavDropdownItem from './NavDropdownItem.vue';
import MobileNavDropdownItem from './MobileNavDropdownItem.vue';
import {
    Search,
    X,
    MessageSquareText,
    LogIn,
    MapPinHouse,
    ChevronDown,
} from '@lucide/vue';

const isMobileSearchOpen = ref(false);
const isMobileMenuOpen = ref(false); // Mobile menu visibility toggle
const searchQuery = ref('');
const isSearching = ref(false);
const searchResults = ref<{
    categories: SearchCategory[];
    products: SearchProduct[];
}>({
    categories: [],
    products: [],
});
let searchTimer: ReturnType<typeof setTimeout> | undefined;
let searchRequest = 0;

interface NavProduct {
    name: string;
    slug: string;
}
interface SearchProduct extends NavProduct {
    id: number;
    category: { name: string; slug: string } | null;
}
interface SearchCategory {
    id: number;
    name: string;
    slug: string;
}
interface NavCategory {
    id: number;
    name: string;
    slug: string;
    children: NavCategory[];
    products: NavProduct[];
    has_more: boolean;
}

const page = usePage<{ category_tree?: NavCategory[] }>();
const navCategories = computed(() => page.props.category_tree ?? []);
const hasSearchResults = computed(
    () =>
        searchResults.value.categories.length > 0 ||
        searchResults.value.products.length > 0,
);
const showSearchDropdown = computed(
    () =>
        searchQuery.value.trim().length >= 2 &&
        (isSearching.value || hasSearchResults.value),
);

watch(searchQuery, (value) => {
    clearTimeout(searchTimer);
    const query = value.trim();

    if (query.length < 2) {
        isSearching.value = false;
        searchResults.value = { categories: [], products: [] };
        return;
    }

    searchTimer = setTimeout(async () => {
        const request = ++searchRequest;
        isSearching.value = true;
        try {
            const response = await fetch(
                '/search/suggestions?q=' + encodeURIComponent(query),
                {
                    headers: { Accept: 'application/json' },
                },
            );
            if (response.ok && request === searchRequest) {
                searchResults.value = await response.json();
            }
        } finally {
            if (request === searchRequest) isSearching.value = false;
        }
    }, 180);
});

function submitSearch() {
    const query = searchQuery.value.trim();
    if (query) router.get('/search', { q: query });
}

function clearSearch() {
    searchQuery.value = '';
    searchResults.value = { categories: [], products: [] };
}
</script>

<template>
    <div class="sticky top-0 z-50 w-full bg-background shadow-sm">
        <header class="relative z-50 bg-jack-blue text-white shadow-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between gap-4">
                    <div class="flex flex-shrink-0 items-center gap-1.5">
                        <button
                            @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="inline-flex cursor-pointer rounded-md p-2 transition-colors hover:bg-white/10 md:hidden"
                            aria-label="Toggle Navigation Menu"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    v-if="!isMobileMenuOpen"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    v-else
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                        <a href="/" class="block">
                            <img
                                src="/jacklogo.png"
                                alt="Jack Logo"
                                class="h-7 w-auto object-contain brightness-0 invert sm:h-8"
                            />
                        </a>
                    </div>

                    <div class="mx-auto hidden min-w-0 max-w-sm flex-1 md:flex">
                        <form
                            class="relative w-full"
                            @submit.prevent="submitSearch"
                        >
                            <input
                                v-model="searchQuery"
                                type="search"
                                autocomplete="off"
                                placeholder="Search products or categories..."
                                class="w-full rounded-lg border border-white/20 bg-white/10 py-2 pr-10 pl-4 font-sans text-sm text-white placeholder-white/70 transition-all duration-200 focus:bg-white focus:text-foreground focus:placeholder-muted-foreground focus:outline-none"
                            />
                            <span
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                            >
                                <!-- <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg> -->
                                <Search class="size-6" />
                            </span>

                            <div
                                v-if="showSearchDropdown"
                                class="absolute top-[calc(100%+0.5rem)] right-0 left-0 overflow-hidden rounded-lg border border-border bg-card text-foreground shadow-xl"
                            >
                                <p
                                    v-if="isSearching"
                                    class="px-4 py-3 text-sm text-muted-foreground"
                                >
                                    Searching…
                                </p>
                                <template v-else>
                                    <div
                                        v-if="searchResults.categories.length"
                                        class="border-b border-border py-2"
                                    >
                                        <p
                                            class="px-4 py-1 text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                                        >
                                            Categories
                                        </p>
                                        <Link
                                            v-for="category in searchResults.categories"
                                            :key="
                                                'search-category-' + category.id
                                            "
                                            :href="
                                                '/products?category=' +
                                                encodeURIComponent(
                                                    category.slug,
                                                )
                                            "
                                            class="block px-4 py-2 text-sm hover:bg-muted hover:text-jack-blue"
                                            @click="clearSearch"
                                            >{{ category.name }}</Link
                                        >
                                    </div>
                                    <div
                                        v-if="searchResults.products.length"
                                        class="py-2"
                                    >
                                        <p
                                            class="px-4 py-1 text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                                        >
                                            Products
                                        </p>
                                        <Link
                                            v-for="product in searchResults.products"
                                            :key="
                                                'search-product-' + product.id
                                            "
                                            :href="'/products/' + product.slug"
                                            class="block px-4 py-2 hover:bg-muted"
                                            @click="clearSearch"
                                            ><span
                                                class="block text-sm font-medium"
                                                >{{ product.name }}</span
                                            ><span
                                                class="block text-xs text-muted-foreground"
                                                >{{
                                                    product.category?.name ??
                                                    'Product'
                                                }}</span
                                            ></Link
                                        >
                                    </div>
                                    <button
                                        type="submit"
                                        class="flex w-full items-center justify-between border-t border-border px-4 py-3 text-left text-sm font-semibold text-jack-blue hover:bg-muted"
                                    >
                                        View all results <span>→</span>
                                    </button>
                                </template>
                            </div>
                        </form>
                    </div>

                    <div
                        class="flex shrink-0 items-center gap-1 font-roboto lg:gap-2"
                    >
                        <button
                            @click="isMobileSearchOpen = !isMobileSearchOpen"
                            class="cursor-pointer rounded-full p-2 transition-colors hover:bg-white/10 md:hidden"
                            aria-label="Toggle Search"
                        >
                            <!-- <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path v-if="!isMobileSearchOpen" stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg> -->
                            <div v-if="!isMobileSearchOpen">
                                <Search class="size-5" />
                            </div>
                            <div v-else>
                                <X class="size-5" />
                            </div>
                        </button>

                        <a
                            href="/about"
                            class="items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors duration-150 hover:bg-white/10 md:flex"
                        >
                            <!-- <svg class="w-4 h-4 text-orange-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1014 0c0-1.307-.349-2.518-1-3.523a11.398 11.398 0 00-3.35-3.882zm-3.86 10.13a.75.75 0 001.05-.143 2.5 2.5 0 014.076.544.75.75 0 001.332-.691 4 4 0 00-6.53-1.096.75.75 0 00.073 1.385z"
                  clip-rule="evenodd" />
              </svg> -->
                            <MapPinHouse class="size-4" />
                            <span class="hidden lg:inline">About Us</span>
                        </a>

                        <a
                            href="/contact"
                            class="items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors duration-150 hover:bg-white/10 md:flex"
                        >
                            <!-- <svg class="w-4 h-4 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
              </svg> -->
                            <MessageSquareText class="size-4" />
                            <span class="hidden lg:inline">Contact Us</span>
                        </a>

                        <a
                            href="#"
                            class="flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors duration-150 hover:bg-white/10"
                        >
                            <!-- <svg class="w-4 h-4 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h7a3 3 0 013 3v1" />
              </svg> -->
                            <LogIn class="size-4" />
                            <!-- <span>Login</span> -->
                            <Link
                                v-if="$page.props.auth.user"
                                :href="dashboard()"
                            >
                                Dashboard
                            </Link>

                            <template v-else>
                                <Link :href="login()"> Login </Link>

                                <!-- <Link
                    :href="register()"
                  >
                    Register
                  </Link> -->
                            </template>
                        </a>
                    </div>
                </div>
            </div>

            <div
                v-if="isMobileSearchOpen"
                class="animate-in border-t border-white/10 bg-jack-blue px-4 py-3 duration-200 fade-in slide-in-from-top-1 md:hidden"
            >
                <form class="relative w-full" @submit.prevent="submitSearch">
                    <input
                        v-model="searchQuery"
                        type="search"
                        autocomplete="off"
                        placeholder="Search products or categories..."
                        class="w-full rounded-lg bg-white py-2 pr-10 pl-4 font-sans text-sm text-foreground focus:outline-none"
                    />
                    <span
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                    >
                        <svg
                            class="h-4 w-4 text-muted-foreground"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </span>
                    <div
                        v-if="showSearchDropdown"
                        class="mt-2 overflow-hidden rounded-lg bg-card text-foreground shadow-lg"
                    >
                        <p
                            v-if="isSearching"
                            class="px-4 py-3 text-sm text-muted-foreground"
                        >
                            Searching…
                        </p>
                        <template v-else>
                            <div
                                v-if="searchResults.categories.length"
                                class="border-b border-border py-2"
                            >
                                <p
                                    class="px-4 py-1 text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Categories
                                </p>
                                <Link
                                    v-for="category in searchResults.categories"
                                    :key="
                                        'mobile-search-category-' + category.id
                                    "
                                    :href="
                                        '/products?category=' +
                                        encodeURIComponent(category.slug)
                                    "
                                    class="block px-4 py-2 text-sm hover:bg-muted hover:text-jack-blue"
                                    @click="clearSearch"
                                    >{{ category.name }}</Link
                                >
                            </div>
                            <div
                                v-if="searchResults.products.length"
                                class="py-2"
                            >
                                <p
                                    class="px-4 py-1 text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Products
                                </p>
                                <Link
                                    v-for="product in searchResults.products"
                                    :key="'mobile-search-product-' + product.id"
                                    :href="'/products/' + product.slug"
                                    class="block px-4 py-2 hover:bg-muted"
                                    @click="clearSearch"
                                    ><span class="block text-sm font-medium">{{
                                        product.name
                                    }}</span
                                    ><span
                                        class="block text-xs text-muted-foreground"
                                        >{{
                                            product.category?.name ?? 'Product'
                                        }}</span
                                    ></Link
                                >
                            </div>
                            <button
                                type="submit"
                                class="flex w-full items-center justify-between border-t border-border px-4 py-3 text-left text-sm font-semibold text-jack-blue"
                            >
                                View all results <span>→</span>
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </header>

        <nav
            class="relative z-40 hidden border-b border-border bg-background md:block"
            aria-label="Main navigation"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <ul
                    class="flex flex-wrap items-center justify-center gap-x-1 gap-y-1 py-2"
                >
                    <li
                        v-for="category in navCategories"
                        :key="category.id"
                        class="group relative px-3 pt-2 transition-all duration-100 hover:z-50"
                    >
                        <Link
                            :href="`/products?category=${encodeURIComponent(category.slug)}`"
                            class="relative flex cursor-pointer items-center gap-1.5 pb-1 font-medium text-foreground transition-colors duration-200 hover:text-jack-blue"
                        >
                            {{ category.name }}

                            <!-- <svg v-if="category.children && category.children.length > 0"
                class="w-3 h-3 transition-transform duration-300 transform group-hover:rotate-180 text-muted-foreground group-hover:text-jack-blue"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg> -->

                            <ChevronDown
                                v-if="
                                    category.children.length > 0 ||
                                    category.products.length > 0
                                "
                                class="size-4 transform text-muted-foreground transition-transform duration-300 group-hover:rotate-180 group-hover:text-jack-blue"
                            />

                            <span
                                class="absolute bottom-0 left-0 h-[2px] w-0 bg-jack-blue transition-all duration-300 ease-out group-hover:w-full"
                            ></span>
                        </Link>

                        <div
                            v-if="
                                category.children.length > 0 ||
                                category.products.length > 0
                            "
                            class="absolute top-full left-1/2 hidden min-w-56 -translate-x-1/2 animate-in rounded-md border border-border bg-card py-2 shadow-xl duration-200 fade-in slide-in-from-top-2 group-hover:block"
                        >
                            <NavDropdownItem
                                v-for="child in category.children"
                                :key="child.id"
                                :item="child"
                            />
                            <template v-if="category.products.length">
                                <Link
                                    v-for="product in category.products"
                                    :key="product.slug"
                                    :href="`/products/${product.slug}`"
                                    class="block px-4 py-2.5 font-roboto text-xs text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground"
                                >
                                    {{ product.name }}
                                </Link>
                                <Link
                                    v-if="category.has_more"
                                    :href="`/products?category=${encodeURIComponent(category.slug)}`"
                                    class="block px-4 py-2.5 text-xs font-semibold text-jack-blue transition-colors duration-150 hover:bg-muted"
                                >
                                    Show More
                                </Link>
                            </template>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div
            v-if="isMobileMenuOpen"
            class="absolute top-full left-0 z-50 max-h-[calc(100vh-4rem)] w-full animate-in overflow-y-auto border-b border-border bg-card font-sans shadow-2xl duration-200 fade-in slide-in-from-top-4 md:hidden"
        >
            <div class="space-y-1 px-4 py-3">
                <div
                    v-for="category in navCategories"
                    :key="'mob-' + category.id"
                    class="border-b border-border/50 py-1 last:border-0"
                >
                    <div
                        class="flex items-center justify-between py-2 font-medium text-foreground"
                    >
                        <Link
                            :href="`/products?category=${encodeURIComponent(category.slug)}`"
                            class="text-md hover:text-jack-blue"
                            >{{ category.name }}</Link
                        >
                    </div>

                    <div
                        v-if="
                            category.children.length || category.products.length
                        "
                        class="space-y-1.5 pb-2 pl-4"
                    >
                        <MobileNavDropdownItem
                            v-for="child in category.children"
                            :key="child.id"
                            :item="child"
                        />
                        <template v-if="category.products.length">
                            <Link
                                v-for="product in category.products"
                                :key="product.slug"
                                :href="`/products/${product.slug}`"
                                class="block py-1 font-roboto text-xs text-muted-foreground hover:text-jack-blue"
                            >
                                {{ product.name }}
                            </Link>
                            <Link
                                v-if="category.has_more"
                                :href="`/products?category=${encodeURIComponent(category.slug)}`"
                                class="block py-1 text-xs font-semibold text-jack-blue"
                            >
                                Show More
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
