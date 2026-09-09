<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineOptions({ name: 'MobileNavDropdownItem' });

interface NavItem {
    id: number;
    name: string;
    slug: string;
    children: NavItem[];
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
    </div>
</template>
