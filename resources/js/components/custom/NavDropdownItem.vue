<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';

defineOptions({
  name: 'NavDropdownItem'
});

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

defineProps<{
  item: NavItem;
}>();
</script>

<template>
  <div v-if="item.children.length" class="relative group/sub">
    <Link :href="`/products?category=${encodeURIComponent(item.slug)}`" class="flex w-full items-center justify-between px-4 py-2.5 text-sm text-foreground transition-colors duration-150 hover:bg-muted hover:text-jack-blue">
      {{ item.name }}
      <ChevronRight class="size-4 text-muted-foreground transition-transform duration-300 group-hover/sub:rotate-90 group-hover/sub:text-jack-blue" />
    </Link>

    <div class="absolute top-0 left-[98%] hidden min-w-56 animate-in fade-in slide-in-from-left-2 duration-200 group-hover/sub:block rounded-md border border-border bg-card py-2 shadow-xl">
      <NavDropdownItem v-for="subChild in item.children" :key="subChild.id" :item="subChild" />
    </div>
  </div>

  <div v-else-if="item.products.length" class="relative">
    <Link :href="`/products?category=${encodeURIComponent(item.slug)}`" class="block px-4 py-2.5 text-sm text-foreground transition-colors duration-150 hover:bg-muted hover:text-jack-blue">
      {{ item.name }}
    </Link>
    <Link v-for="product in item.products" :key="product.slug" :href="`/products/${product.slug}`" class="block px-4 py-2.5 text-xs text-muted-foreground transition-colors duration-150 hover:bg-muted hover:text-foreground">
      {{ product.name }}
    </Link>
    <Link v-if="item.has_more" :href="`/products?category=${encodeURIComponent(item.slug)}`" class="block px-4 py-2.5 text-xs font-semibold text-jack-blue transition-colors duration-150 hover:bg-muted">
      Show More
    </Link>
  </div>

  <div v-else>
    <Link :href="`/products?category=${encodeURIComponent(item.slug)}`" class="block px-4 py-2.5 text-sm text-foreground transition-colors duration-150 hover:bg-muted hover:text-jack-blue">
      {{ item.name }}
    </Link>
  </div>
</template>
