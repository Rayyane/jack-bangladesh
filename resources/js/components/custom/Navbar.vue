<script setup lang="ts">
import { ref } from 'vue';

type MegaMenuGroup = {
    title: string;
    links: string[];
};

type NavItem = {
    label: string;
    groups: MegaMenuGroup[];
};

const openMenu = ref<string | null>(null);

const navItems: NavItem[] = [
    {
        label: 'Lockstitch',
        groups: [
            {
                title: 'Basic',
                links: ['Jack F6', 'Jack F7', 'Jack F8']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
            {
                title: 'Thread Trimmer',
                links: ['Jack A2C', 'Jack A3C', 'Jack A4B', 'Jack A2F']
            },
        ],
    },
    {
        label: 'Overlock',
        groups: [
            {
                title: 'Featured',
                links: ['Consulting', 'Planning', 'Support']
            },
            {
                title: 'Specialties',
                links: ['Custom Orders', 'Corporate', 'Events'],
            },
            {
                title: 'Resources',
                links: ['Guides', 'FAQ', 'Downloads']
            },
        ],
    },
    {
        label: 'Products',
        groups: [
            {
                title: 'Collections',
                links: ['New Arrivals', 'Best Sellers', 'Seasonal'],
            },
            {
                title: 'Categories',
                links: ['Essentials', 'Premium', 'Gift Sets'],
            },
        ],
    },
    {
        label: 'Gallery',
        groups: [
            {
                title: 'Explore',
                links: ['Projects', 'Inspiration', 'Customer Stories'],
            },
            {
                title: 'Media',
                links: ['Photos', 'Videos', 'Press']
            },
        ],
    },
    {
        label: 'Blog',
        groups: [
            {
                title: 'Topics',
                links: ['News', 'Tips', 'Behind the Scenes']
            },
            {
                title: 'Archives',
                links: ['Latest Posts', 'Popular Reads', 'Announcements'],
            },
        ],
    },
    {
        label: 'Contact',
        groups: [
            {
                title: 'Connect',
                links: ['Request Info', 'Book a Call', 'Directions'],
            },
            {
                title: 'Support',
                links: ['Help Center', 'Customer Care', 'Feedback'],
            },
        ],
    },
];

const toggleMenu = (label: string) => {
    openMenu.value = openMenu.value === label ? null : label;
};
</script>

<template>
    <header class="sticky top-0 z-50 w-full border-b border-border bg-background">
        <!-- <div class="border-b border-border bg-primary text-primary-foreground">
            <div
                class="mx-auto flex min-h-8 max-w-7xl items-center justify-between gap-4 px-4 text-xs font-medium sm:px-6 lg:px-8">
                <a class="truncate hover:underline" href="mailto:hello@example.com">
                    hello@example.com
                </a>
                <a class="shrink-0 hover:underline" href="tel:+15550123456">
                    +1 (555) 012-3456
                </a>
            </div>
        </div> -->

        <div class="border-b border-border bg-jack-blue">
            <div class="mx-auto flex min-h-12 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <a href="/" aria-label="Jack home">
                    <img class="h-10 w-auto object-contain" src="/jacklogo.png" alt="Jack logo" />
                </a>
                <input type="search"
                    class="block w-1/3 p-3 my-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Search Products" required />
            </div>
        </div>

        <nav class="relative bg-background" aria-label="Main navigation">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <ul class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1 py-3">
                    <li v-for="item in navItems" :key="item.label">
                        <button
                            class="inline-flex h-10 items-center justify-center rounded-md px-4 text-sm font-semibold tracking-normal text-foreground uppercase transition hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            type="button" :aria-expanded="openMenu === item.label"
                            :aria-controls="`mega-menu-${item.label}`" @click="toggleMenu(item.label)">
                            {{ item.label }}
                        </button>
                    </li>
                </ul>
            </div>

            <div v-for="item in navItems" v-show="openMenu === item.label" :id="`mega-menu-${item.label}`"
                :key="`${item.label}-menu`"
                class="absolute top-full right-0 left-0 border-y border-border bg-popover shadow-lg">
                <div class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:grid-cols-2 sm:px-6 lg:grid-cols-3 lg:px-8">
                    <section v-for="group in item.groups" :key="group.title">
                        <h3 class="text-sm font-semibold tracking-normal text-muted-foreground uppercase">
                            {{ group.title }}
                        </h3>
                        <ul class="mt-3 space-y-2">
                            <li v-for="link in group.links" :key="link">
                                <a class="block rounded-md px-2 py-1.5 text-sm text-popover-foreground transition hover:bg-accent"
                                    href="#">
                                    {{ link }}
                                </a>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </nav>
    </header>
</template>
