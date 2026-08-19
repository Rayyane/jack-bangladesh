<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { FilePenLine, History } from '@lucide/vue';
import { Button } from '@/components/ui/button';

type PageItem = {
    id: number;
    slug: string;
    template_key: string;
    title: string;
    is_published: boolean;
    active_revision: { id: number; status: string } | null;
    can_edit: boolean;
};
defineProps<{ pages: PageItem[] }>();
</script>
<template>
    <Head title="Pages" />
    <div class="mx-auto w-full max-w-5xl p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Pages</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Manage the published content and editorial revisions for public
                pages.
            </p>
        </div>
        <div class="overflow-hidden rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="border-b text-left text-muted-foreground">
                    <tr>
                        <th class="p-4">Page</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="page in pages"
                        :key="page.id"
                        class="border-b last:border-0"
                    >
                        <td class="p-4">
                            <p class="font-medium">{{ page.title }}</p>
                            <p class="text-xs text-muted-foreground">
                                /{{ page.slug }}
                            </p>
                        </td>
                        <td class="p-4">
                            <span
                                class="rounded-full bg-muted px-2 py-1 text-xs capitalize"
                                >{{
                                    page.active_revision?.status?.replace(
                                        '_',
                                        ' ',
                                    ) ??
                                    (page.is_published
                                        ? 'published'
                                        : 'unpublished')
                                }}</span
                            >
                        </td>
                        <td class="p-4">
                            <div class="flex justify-end gap-1">
                                <Button
                                    v-if="page.can_edit"
                                    variant="ghost"
                                    size="icon"
                                    as-child
                                    ><Link
                                        :href="`/cms/pages/${page.id}/edit`"
                                        title="Edit draft"
                                        ><FilePenLine
                                            class="size-4" /></Link></Button
                                ><Button variant="ghost" size="icon" as-child
                                    ><Link
                                        :href="`/cms/pages/${page.id}/revisions`"
                                        title="Revision history"
                                        ><History class="size-4" /></Link
                                ></Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
