<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ChevronRight, Image as ImageIcon, Pencil, Trash2 } from '@lucide/vue';
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

const props = defineProps<{ category: Category; depth?: number }>();

function removeCategory() {
    const message = props.category.product_count > 0
        ? `Delete “${props.category.name}”? ${props.category.product_count} assigned product${props.category.product_count === 1 ? '' : 's'} will become uncategorised.`
        : `Delete “${props.category.name}”?`;

    if (!confirm(message)) return;

    router.delete(`/cms/categories/${props.category.id}`, {
        onError: (errors) => alert(errors.category ?? 'This category could not be deleted.'),
    });
}
</script>

<template>
    <div class="border-t" :style="{ paddingLeft: `${(depth ?? 0) * 1.5}rem` }">
        <div class="flex min-h-14 items-center gap-3 px-3 py-2">
            <ChevronRight v-if="category.children.length" class="size-4 text-muted-foreground" />
            <span v-else class="w-4" />
            <ImageIcon class="size-4 text-muted-foreground" :class="{ 'text-primary': category.image_path }" />
            <span class="min-w-0 flex-1 truncate font-medium">{{ category.name }}</span>
            <span v-if="category.is_featured" class="rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-800">Featured</span>
            <span v-if="depth === 0 && category.show_in_nav" class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800">In nav</span>
            <Button variant="ghost" size="icon" as-child><Link :href="`/cms/categories/${category.id}/edit`" :aria-label="`Edit ${category.name}`"><Pencil class="size-4" /></Link></Button>
            <Button variant="ghost" size="icon" class="text-destructive" :aria-label="`Delete ${category.name}`" @click="removeCategory"><Trash2 class="size-4" /></Button>
        </div>
        <CategoryTreeItem v-for="child in category.children" :key="child.id" :category="child" :depth="(depth ?? 0) + 1" />
    </div>
</template>
