<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ExternalLink, FilePenLine, History, Plus, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

type Product = {
    id: number;
    slug: string;
    name: string;
    is_featured: boolean;
    is_published: boolean;
    is_uncategorised: boolean;
    category: { id: number; name: string } | null;
    active_revision: { id: number; status: string } | null;
    can_edit: boolean;
};
type Pagination = {
    data: Product[];
    links: { url: string | null; label: string; active: boolean }[];
};
const { products, categories, filters } = defineProps<{
    products: Pagination;
    categories: { id: number; name: string }[];
    filters: { category_id?: string; status?: string; uncategorised?: string };
}>();
const page = usePage();
const isSuperAdmin = computed(() =>
    page.props.auth.roles.includes('super_admin'),
);
function filter(event: Event) {
    const form = event.currentTarget as HTMLFormElement;
    const data = new FormData(form);
    router.get(
        '/cms/products',
        Object.fromEntries([...data].filter(([, value]) => value !== '')),
        { preserveState: true, replace: true },
    );
}
function clearFilters() {
    router.get('/cms/products', {}, { preserveState: true, replace: true });
}
function remove(product: Product) {
    if (confirm(`Delete “${product.name}”? This cannot be undone.`)) {
        router.delete(`/cms/products/${product.id}`);
    }
}
function statusLabel(product: Product) {
    return (
        product.active_revision?.status?.replace('_', ' ') ??
        (product.is_published ? 'published' : 'draft')
    );
}
function paginationLabel(label: string) {
    return label.replace('&laquo;', '‹').replace('&raquo;', '›');
}
</script>
<template>
    <Head title="Products" />
    <div class="mx-auto w-full max-w-6xl p-4 md:p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Products</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Create, edit, review, and publish the product catalogue.
                </p>
            </div>
            <Button as-child
                ><Link href="/cms/products/create"
                    ><Plus class="size-4" /> Add product</Link
                ></Button
            >
        </div>
        <form
            class="mb-4 flex flex-wrap gap-3 rounded-lg border bg-card p-3"
            @change="filter"
            @submit.prevent="filter"
        >
            <select
                name="category_id"
                :value="filters.category_id ?? ''"
                class="rounded-md border bg-background px-3 py-2 text-sm"
            >
                <option value="">All categories</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option></select
            ><select
                name="status"
                :value="filters.status ?? ''"
                class="rounded-md border bg-background px-3 py-2 text-sm"
            >
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="pending_review">Pending review</option>
                <option value="approved">Approved</option></select
            ><label class="flex items-center gap-2 px-2 text-sm"
                ><input
                    name="uncategorised"
                    type="checkbox"
                    value="1"
                    :checked="filters.uncategorised === '1'"
                />
                Uncategorised</label
            >
            <Button
                v-if="Object.keys(filters).length"
                type="button"
                variant="ghost"
                size="sm"
                @click="clearFilters"
                >Clear filters</Button
            >
        </form>
        <div
            v-if="products.data.length"
            class="overflow-x-auto rounded-lg border bg-card"
        >
            <table class="w-full min-w-[680px] text-sm">
                <thead class="border-b text-left text-muted-foreground">
                    <tr>
                        <th class="p-4">Product</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Featured</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="product in products.data"
                        :key="product.id"
                        class="border-b last:border-0"
                    >
                        <td class="p-4">
                            <p class="font-medium">{{ product.name }}</p>
                            <p class="text-xs text-muted-foreground">
                                /products/{{ product.slug }}
                            </p>
                        </td>
                        <td class="p-4">
                            <span
                                :class="
                                    product.is_uncategorised
                                        ? 'text-amber-700'
                                        : ''
                                "
                                >{{
                                    product.category?.name ?? 'Uncategorised'
                                }}</span
                            >
                        </td>
                        <td class="p-4">
                            <span
                                class="rounded-full bg-muted px-2 py-1 text-xs capitalize"
                                >{{ statusLabel(product) }}</span
                            >
                        </td>
                        <td class="p-4">
                            {{ product.is_featured ? 'Yes' : '—' }}
                        </td>
                        <td class="p-4">
                            <div class="flex justify-end gap-1">
                                <Button variant="ghost" size="icon" as-child
                                    ><a
                                        v-if="product.is_published"
                                        :href="`/products/${product.slug}`"
                                        target="_blank"
                                        title="View live product"
                                        ><ExternalLink
                                            class="size-4" /></a></Button
                                ><Button
                                    v-if="product.can_edit"
                                    variant="ghost"
                                    size="icon"
                                    as-child
                                    ><Link
                                        :href="`/cms/products/${product.id}/edit`"
                                        title="Edit draft"
                                        ><FilePenLine
                                            class="size-4" /></Link></Button
                                ><Button variant="ghost" size="icon" as-child
                                    ><Link
                                        :href="`/cms/products/${product.id}/revisions`"
                                        title="Revision history"
                                        ><History
                                            class="size-4" /></Link></Button
                                ><Button
                                    v-if="isSuperAdmin"
                                    variant="ghost"
                                    size="icon"
                                    title="Delete product"
                                    @click="remove(product)"
                                    ><Trash2 class="size-4 text-destructive"
                                /></Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div
            v-else
            class="rounded-lg border border-dashed p-10 text-center text-sm text-muted-foreground"
        >
            No products match these filters.
        </div>
        <nav v-if="products.links.length > 3" class="mt-4 flex flex-wrap gap-1">
            <Link
                v-for="link in products.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                    'rounded border px-3 py-1 text-sm',
                    link.active ? 'bg-primary text-primary-foreground' : '',
                    !link.url ? 'pointer-events-none opacity-50' : '',
                ]"
                >{{ paginationLabel(link.label) }}</Link
            >
        </nav>
    </div>
</template>
