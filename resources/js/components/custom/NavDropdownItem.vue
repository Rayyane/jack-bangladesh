<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import { ref } from 'vue';

defineOptions({
  name: 'NavDropdownItem'
});

interface NavItem {
  id: number;
  name: string;
  slug: string;
  children: NavItem[];
}

defineProps<{
  item: NavItem;
}>();

const isOpen = ref(false);
</script>

<template>
  <div
    v-if="item.children.length"
    class="relative"
    @mouseenter="isOpen = true"
    @mouseleave="isOpen = false"
  >
    <Link :href="`/products?category=${encodeURIComponent(item.slug)}`" class="flex w-full items-center justify-between px-4 py-2.5 text-sm text-foreground transition-colors duration-150 hover:bg-muted hover:text-jack-blue">
      {{ item.name }}
      <ChevronRight :class="['size-4 text-muted-foreground transition-transform duration-300', isOpen && 'rotate-90 text-jack-blue']" />
    </Link>

    <div
      v-show="isOpen"
      class="absolute top-0 left-[98%] min-w-56 animate-in rounded-md border border-border bg-card py-2 shadow-xl duration-200 fade-in slide-in-from-left-2"
    >
      <NavDropdownItem v-for="subChild in item.children" :key="subChild.id" :item="subChild" />
    </div>
  </div>

  <div v-else>
    <Link :href="`/products?category=${encodeURIComponent(item.slug)}`" class="block px-4 py-2.5 text-sm text-foreground transition-colors duration-150 hover:bg-muted hover:text-jack-blue">
      {{ item.name }}
    </Link>
  </div>
</template>
