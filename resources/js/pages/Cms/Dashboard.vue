<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ClipboardCheck, FileText, FolderTree, Package, Plus, ShieldCheck } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/cms',
    },
];

defineOptions({
    layout: {
        breadcrumbs,
    },
});

const page = usePage();
const isSuperAdmin = computed(() => page.props.auth.roles.includes('super_admin'));
</script>

<template>
    <Head title="CMS Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold tracking-normal">CMS Dashboard</h1>
            <p class="text-sm text-muted-foreground">Manage pages, products, and publishing workflows.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card>
                <CardHeader>
                    <FileText class="size-5" />
                    <CardTitle class="text-base">Pages</CardTitle>
                </CardHeader>
                <CardContent>
                    <Button variant="outline" as-child class="w-full justify-between">
                        <Link href="/cms/pages">
                            Manage pages
                            <FileText class="size-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <Package class="size-5" />
                    <CardTitle class="text-base">Products</CardTitle>
                </CardHeader>
                <CardContent>
                    <Button variant="outline" as-child class="w-full justify-between">
                        <Link href="/cms/products">
                            Manage products
                            <Package class="size-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>

            <Card v-if="isSuperAdmin">
                <CardHeader>
                    <FolderTree class="size-5" />
                    <CardTitle class="text-base">Categories</CardTitle>
                </CardHeader>
                <CardContent>
                    <Button variant="outline" as-child class="w-full justify-between">
                        <Link href="/cms/categories">
                            Manage categories
                            <FolderTree class="size-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>

        <Card class="gap-0 py-0">
            <CardHeader class="border-b py-5">
                <div class="flex items-center gap-3">
                    <ClipboardCheck class="size-5" />
                    <CardTitle class="text-base">Content workflow</CardTitle>
                </div>
            </CardHeader>
            <CardContent class="grid gap-3 py-5 sm:grid-cols-2 lg:grid-cols-3">
                <Button variant="outline" as-child class="justify-between">
                    <Link href="/cms/pages">
                        Edit pages
                        <FileText class="size-4" />
                    </Link>
                </Button>
                <Button variant="outline" as-child class="justify-between">
                    <Link href="/cms/products/create">
                        New product
                        <Plus class="size-4" />
                    </Link>
                </Button>
                <Button v-if="isSuperAdmin" variant="outline" as-child class="justify-between">
                    <Link href="/cms/review-queue">
                        Review queue
                        <ShieldCheck class="size-4" />
                    </Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
