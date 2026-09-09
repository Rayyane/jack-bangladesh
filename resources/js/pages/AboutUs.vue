<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowRight, Check, Mail, Phone, Sparkles } from '@lucide/vue';
import { computed } from 'vue';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Content = Record<string, any>;
type GalleryImage = { id: number; url: string; alt_text: string | null };
const props = defineProps<{
    content?: Content | null;
    gallery?: GalleryImage[];
}>();
const contentValue = (key: string): unknown =>
    key
        .split('.')
        .reduce((current, part) => current?.[part], props.content);
const text = (key: string, fallback: string): string => {
    const value = contentValue(key);
    return typeof value === 'string' && value !== '' ? value : fallback;
};

const defaultStats = [
    { value: '24/7', label: 'Support when production matters' },
    { value: '1', label: 'Trusted partner for every line' },
    { value: '100%', label: 'Focused on industrial sewing' },
    { value: '∞', label: 'Possibilities to create' },
];
const defaultLeadershipMessages = [
    {
        key: 'chairman',
        title: "Chairman's Message",
        body: 'A message from our Chairman will be shared here.',
        name: "Chairman's Name",
        designation: 'Chairman',
        imageLabel: 'Chairman portrait',
    },
    {
        key: 'md',
        title: "MD's Message",
        body: 'A message from our Managing Director will be shared here.',
        name: "Managing Director's Name",
        designation: 'Managing Director',
        imageLabel: 'Managing Director portrait',
    },
];
const defaultTeamMembers = [
    {
        name: 'Team Member Name',
        designation: 'Sales Manager',
        phone: '+880 1700-000000',
        email: 'sales@jackbangladesh.com',
    },
    {
        name: 'Team Member Name',
        designation: 'Service Manager',
        phone: '+880 1700-000000',
        email: 'service@jackbangladesh.com',
    },
    {
        name: 'Team Member Name',
        designation: 'Customer Support',
        phone: '+880 1700-000000',
        email: 'support@jackbangladesh.com',
    },
];
const stats = computed(() =>
    defaultStats.map((stat, index) => ({
        value: text(`stats.${index}.value`, stat.value),
        label: text(`stats.${index}.label`, stat.label),
    })),
);
const leadershipMessages = computed(() =>
    defaultLeadershipMessages.map((message) => ({
        ...message,
        title: text(`leadership.${message.key}.title`, message.title),
        body: text(`leadership.${message.key}.body`, message.body),
        name: text(`leadership.${message.key}.name`, message.name),
        designation: text(
            `leadership.${message.key}.designation`,
            message.designation,
        ),
    })),
);
const teamMembers = computed(() => {
    const members = contentValue('team.members');

    if (!Array.isArray(members)) {
        return defaultTeamMembers.map((member, index) => ({
            ...member,
            image_slot: `team-${index + 1}`,
        }));
    }

    return members.map((member, index) => ({
        name:
            typeof member?.name === 'string' && member.name.trim() !== ''
                ? member.name
                : 'Team Member Name',
        designation:
            typeof member?.designation === 'string' && member.designation.trim() !== ''
                ? member.designation
                : 'Team Member',
        phone: typeof member?.phone === 'string' ? member.phone : '',
        email: typeof member?.email === 'string' ? member.email : '',
        image_slot:
            typeof member?.image_slot === 'string'
                ? member.image_slot
                : `team-${index + 1}`,
    }));
});
const imageFor = (slot: string, fallback: string): string =>
    props.gallery?.find((image) => image.alt_text === 'about-' + slot)?.url ??
    fallback;
const uploadedImage = (slot: string): string | undefined =>
    props.gallery?.find((image) => image.alt_text === 'about-' + slot)?.url;
</script>

<template>
    <Head :title="text('meta_title', 'About Jack Bangladesh')" />
    <Navbar />
    <main class="overflow-hidden bg-background font-sans text-foreground">
        <section
            class="relative isolate overflow-hidden bg-jack-blue py-18 text-white sm:py-24 lg:py-28"
        >
            <div
                class="absolute inset-0 [background-image:linear-gradient(to_right,white_1px,transparent_1px),linear-gradient(to_bottom,white_1px,transparent_1px)] [background-size:40px_40px] opacity-20"
            ></div>
            <div
                class="absolute top-1/2 -right-20 size-96 -translate-y-1/2 rounded-full border-[48px] border-white/10"
            ></div>
            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:gap-20 lg:px-8"
            >
                <div>
                    <div
                        class="mb-5 flex items-center gap-2 text-xs font-bold tracking-[0.2em] text-orange-300 uppercase"
                    >
                        <span class="size-2 rounded-full bg-orange-300"></span
                        >{{ text('hero.eyebrow', 'Jack Bangladesh') }}
                    </div>
                    <h1
                        class="max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl"
                    >
                        {{
                            text(
                                'hero.title',
                                'Technology that moves the people who make things.',
                            )
                        }}
                    </h1>
                    <p
                        class="mt-6 max-w-2xl text-base leading-7 text-white/80 sm:text-lg"
                    >
                        {{
                            text(
                                'hero.description',
                                'We connect Bangladesh’s apparel makers with dependable industrial sewing technology, practical expertise and the support to grow with confidence.',
                            )
                        }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="/products"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-jack-blue transition hover:bg-white/90"
                            >Explore machines <ArrowRight class="size-4"
                        /></a>
                        <a
                            href="/contact"
                            class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                            >Talk to our team</a
                        >
                    </div>
                </div>
                <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                    <div
                        class="absolute -inset-5 rounded-[2rem] bg-orange-300/15 blur-2xl"
                    ></div>
                    <div
                        class="relative rounded-2xl border border-white/20 bg-white/10 p-5 shadow-2xl backdrop-blur-sm sm:p-7"
                    >
                        <div
                            class="flex items-center justify-between border-b border-white/15 pb-5"
                        >
                            <span class="text-sm font-semibold"
                                >Built around your floor</span
                            ><Sparkles class="size-5 text-orange-300" />
                        </div>
                        <img
                            :src="imageFor('hero', '/F6.png')"
                            alt="Jack industrial sewing machine"
                            class="mx-auto h-55 w-full object-contain sm:h-64"
                        />
                        <div
                            class="grid grid-cols-2 gap-3 border-t border-white/15 pt-5 text-sm"
                        >
                            <div>
                                <span class="block text-xs text-white/60"
                                    >Purpose</span
                                ><strong>Better output</strong>
                            </div>
                            <div>
                                <span class="block text-xs text-white/60"
                                    >Approach</span
                                ><strong>Practical support</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section
            class="relative z-10 mx-auto -mt-7 max-w-7xl px-4 sm:px-6 lg:px-8"
        >
            <div
                class="grid grid-cols-2 rounded-xl border border-border bg-card p-2 shadow-lg md:grid-cols-4 md:p-4"
            >
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="border-border px-4 py-4 text-center even:border-l md:border-l md:first:border-l-0"
                >
                    <p
                        class="text-2xl font-extrabold text-jack-blue sm:text-3xl"
                    >
                        {{ stat.value }}
                    </p>
                    <p class="mt-1 text-xs leading-4 text-muted-foreground">
                        {{ stat.label }}
                    </p>
                </div>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
            <div>
                <p
                    class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase"
                >
                    {{ text('who.eyebrow', 'Who we are') }}
                </p>
                <h2
                    class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    {{ text('who.title', 'A local partner for every stitch of progress.') }}
                </h2>
                <div
                    class="mt-6 space-y-4 text-base leading-7 text-muted-foreground"
                >
                    <p v-if="!contentValue('who.description_first')">
                        Jack Bangladesh serves the people behind one of the
                        world’s most dynamic apparel industries. Our work is
                        rooted in a simple belief: production technology should
                        make skilled work more capable, not more complicated.
                    </p><p v-else>{{ text('who.description_first', '') }}</p>
                    <p v-if="!contentValue('who.description_second')">
                        Whether you are setting up a new line or improving an
                        established one, we help you find practical equipment
                        solutions that fit the way your team works.
                    </p><p v-else>{{ text('who.description_second', '') }}</p>
                </div>
                <ul class="mt-7 space-y-3 text-sm font-medium text-foreground">
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >{{ text('who.bullets.0', 'Industrial sewing expertise, made approachable') }}
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >{{ text('who.bullets.1', 'Solutions for evolving production needs') }}
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="grid size-5 place-items-center rounded-full bg-jack-blue text-white"
                            ><Check class="size-3" /></span
                        >{{ text('who.bullets.2', 'A team that stays close to the work') }}
                    </li>
                </ul>
            </div>
        </section>
        <section class="border-y border-border bg-muted/35 py-18 lg:py-24">
            <div class="mx-auto max-w-7xl space-y-16 px-4 sm:px-6 lg:space-y-24 lg:px-8">
                <article
                    v-for="(message, index) in leadershipMessages"
                    :key="message.title"
                    class="grid items-center gap-8 lg:grid-cols-12 lg:gap-16"
                >
                    <div
                        :class="[
                            'lg:col-span-5',
                            index === 1 ? 'lg:order-2' : '',
                        ]"
                    >
                        <img v-if="uploadedImage(message.key)" :src="uploadedImage(message.key)" :alt="message.imageLabel" class="aspect-square size-full rounded-2xl object-cover lg:aspect-[3/4]" />
                        <div v-else
                            class="grid aspect-square place-items-center overflow-hidden rounded-2xl border border-border bg-jack-blue p-6 text-center text-sm font-medium text-white/70 lg:aspect-[3/4]"
                        >
                            {{ message.imageLabel }}
                        </div>
                    </div>
                    <div
                        :class="[
                            'lg:col-span-7',
                            index === 1 ? 'lg:order-1' : '',
                        ]"
                    >
                        <p class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase">
                            Leadership
                        </p>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                            {{ message.title }}
                        </h2>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-muted-foreground sm:text-lg">
                            {{ message.body }}
                        </p>
                        <div class="mt-7 border-l-4 border-orange-300 pl-4">
                            <p class="font-bold text-foreground">{{ message.name }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ message.designation }}</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
            <div class="max-w-2xl">
                <p class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase">
                    Our team
                </p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    The people behind your progress.
                </h2>
                <p class="mt-4 text-base leading-7 text-muted-foreground">
                    Meet the team ready to help with products, service, and support.
                </p>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="member in teamMembers"
                    :key="member.image_slot"
                    class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
                >
                    <img
                        v-if="uploadedImage(member.image_slot)"
                        :src="uploadedImage(member.image_slot)"
                        :alt="member.name"
                        class="block aspect-square w-full object-cover"
                    />
                    <div v-else class="grid aspect-square place-items-center bg-jack-blue p-6 text-center text-sm font-medium text-white/70">
                        Team member portrait
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold">{{ member.name }}</h3>
                        <p class="mt-1 text-sm font-medium text-jack-blue">{{ member.designation }}</p>
                        <div class="mt-5 space-y-3 border-t border-border pt-5 text-sm text-muted-foreground">
                            <a :href="`tel:${member.phone.replaceAll(' ', '')}`" class="flex items-center gap-2 hover:text-jack-blue">
                                <Phone class="size-4 shrink-0" />
                                {{ member.phone }}
                            </a>
                            <a :href="`mailto:${member.email}`" class="flex items-center gap-2 break-all hover:text-jack-blue">
                                <Mail class="size-4 shrink-0" />
                                {{ member.email }}
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
            <div
                class="rounded-2xl bg-jack-blue px-6 py-10 text-center text-white sm:px-12 sm:py-14"
            >
                <p
                    class="text-xs font-bold tracking-[0.18em] text-orange-300 uppercase"
                >
                    Let’s build what’s next
                </p>
                <h2
                    class="mx-auto mt-3 max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    {{ text('cta.title', 'Ready to make your production line work smarter?') }}
                </h2>
                <p
                    class="mx-auto mt-4 max-w-xl text-sm leading-6 text-white/80"
                >
                    {{ text('cta.description', 'Explore the Jack range or speak with our team about the right solution for your operation.') }}
                </p>
                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a
                        :href="text('cta.url', '/products')"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-jack-blue transition hover:bg-white/90"
                        >{{ text('cta.label', 'Explore the product range') }}
                        <ArrowRight class="size-4" /></a
                    ><a
                        href="/contact"
                        class="rounded-lg border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >Contact us</a
                    >
                </div>
            </div>
        </section>
    </main>
    <Footer />
</template>
