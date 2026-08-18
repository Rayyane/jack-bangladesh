<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { GripVertical } from '@lucide/vue';
import { Button } from '@/components/ui/button';

type Category = { id: number; name: string; sort_order: number };

const props = defineProps<{ categories: Category[] }>();
const categories = ref([...props.categories]);
const draggedId = ref<number | null>(null);

function dropOn(targetId: number) {
    if (draggedId.value === null || draggedId.value === targetId) return;

    const from = categories.value.findIndex((category) => category.id === draggedId.value);
    const to = categories.value.findIndex((category) => category.id === targetId);
    const [moved] = categories.value.splice(from, 1);
    categories.value.splice(to, 0, moved);
    draggedId.value = null;

    router.post('/cms/categories/reorder', {
        categories: categories.value.map((category, sort_order) => ({ id: category.id, sort_order })),
    }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Sort navigation categories" />

    <div class="mx-auto w-full max-w-2xl p-4 md:p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Sort navigation categories</h1>
                <p class="mt-1 text-sm text-muted-foreground">Only parent categories with “Show in navigation” enabled appear here.</p>
            </div>
            <Button variant="outline" as-child><Link href="/cms/categories">Back to categories</Link></Button>
        </div>

        <div v-if="categories.length" class="overflow-hidden rounded-lg border bg-card">
            <div v-for="category in categories" :key="category.id" draggable="true" class="flex items-center gap-3 border-b px-4 py-3 last:border-b-0" @dragstart="draggedId = category.id" @dragover.prevent @drop="dropOn(category.id)">
                <GripVertical class="size-5 cursor-grab text-muted-foreground" />
                <span class="font-medium">{{ category.name }}</span>
            </div>
        </div>
        <div v-else class="rounded-lg border border-dashed p-10 text-center text-sm text-muted-foreground">No parent categories are currently shown in the navigation.</div>
    </div>
</template>
