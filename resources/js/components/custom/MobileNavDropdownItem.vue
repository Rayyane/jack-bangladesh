<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineOptions({ name: 'MobileNavDropdownItem' });

interface NavProduct {
    name: string;
    slug: string;
}

interface NavItem {
    id: number;
    name: string;
    slug: string;
    children: NavItem[];
    products: NavProduct[];
    has_more: boolean;
}

defineProps<{ item: NavItem }>();
</script>

<template>
    <div class="border-l border-border pl-3">
        <Link
            :href="`/products?category=${encodeURIComponent(item.slug)}`"
            class="block py-1 text-sm font-medium text-foreground hover:text-jack-blue"
        >
            {{ item.name }}
        </Link>
        <div v-if="item.children.length" class="space-y-1">
            <MobileNavDropdownItem
                v-for="child in item.children"
                :key="child.id"
                :item="child"
            />
        </div>
        <div v-else-if="item.products.length" class="space-y-1 pb-1 pl-2">
            <Link
                v-for="product in item.products"
                :key="product.slug"
                :href="`/products/${product.slug}`"
                class="block py-1 text-xs text-muted-foreground hover:text-jack-blue"
            >
                {{ product.name }}
            </Link>
            <Link
                v-if="item.has_more"
                :href="`/products?category=${encodeURIComponent(item.slug)}`"
                class="block py-1 text-xs font-semibold text-jack-blue"
            >
                Show More
            </Link>
        </div>
    </div>
</template>
