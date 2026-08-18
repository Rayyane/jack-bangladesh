<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpDown, Plus } from '@lucide/vue';
import CategoryTreeItem from '@/components/cms/CategoryTreeItem.vue';
import { Button } from '@/components/ui/button';

type Category = {
    id: number;
    name: string;
    image_path: string | null;
    is_featured: boolean;
    show_in_nav: boolean;
    product_count: number;
    children: Category[];
};

const props = defineProps<{ categories: Category[] }>();
const categories = ref([...props.categories]);

watch(() => props.categories, (updatedCategories) => {
    categories.value = [...updatedCategories];
});
</script>

<template>
    <Head title="Categories" />

    <div class="mx-auto w-full max-w-5xl p-4 md:p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Categories</h1>
                <p class="mt-1 text-sm text-muted-foreground">Create and manage the complete category hierarchy.</p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child><Link href="/cms/categories/sort"><ArrowUpDown class="size-4" /> Sort navigation</Link></Button>
                <Button as-child><Link href="/cms/categories/create"><Plus class="size-4" /> Add category</Link></Button>
            </div>
        </div>

        <div v-if="categories.length" class="overflow-hidden rounded-lg border bg-card">
            <div v-for="category in categories" :key="category.id">
                <CategoryTreeItem :category="category" :depth="0" />
            </div>
        </div>
        <div v-else class="rounded-lg border border-dashed p-10 text-center text-sm text-muted-foreground">No categories yet. Create the first parent category to begin.</div>
    </div>
</template>
