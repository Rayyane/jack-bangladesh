<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { FolderTree, LayoutGrid, PanelsTopLeft, Package, ShieldCheck } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const cmsDashboardUrl = '/cms';
const page = usePage();

const isSuperAdmin = computed(() => page.props.auth.roles.includes('super_admin'));

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: cmsDashboardUrl,
            icon: LayoutGrid,
        },
        {
            title: 'Pages',
            href: '/cms/pages',
            icon: PanelsTopLeft,
        },
        {
            title: 'Products',
            href: '/cms/products',
            icon: Package,
        },
    ];

    if (isSuperAdmin.value) {
        items.push(
            {
                title: 'Categories',
                href: '/cms/categories',
                icon: FolderTree,
            },
            {
                title: 'Review queue',
                href: '/cms/review-queue',
                icon: ShieldCheck,
            },
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="cmsDashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
