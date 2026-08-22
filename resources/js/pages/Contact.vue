<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    Clock3,
    Mail,
    MapPin,
    Phone,
    Send,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import Footer from '@/components/custom/FooterSection.vue';
import Navbar from '@/components/custom/Navbar.vue';

type Content = Record<string, any>;

const props = defineProps<{ content?: Content | null }>();
const text = (key: string, fallback: string): string => {
    const value = key
        .split('.')
        .reduce((current, part) => current?.[part], props.content);

    return typeof value === 'string' && value !== '' ? value : fallback;
};

const form = ref({ name: '', email: '', phone: '', subject: '', message: '' });
const submitted = ref(false);
const contactCards = computed(() => [
    {
        title: 'Call us',
        detail: text('contact.phone', '+880 1700-000000'),
        note: text(
            'contact.phone_note',
            'Speak with our sales and support team',
        ),
        icon: Phone,
        href: `tel:${text('contact.phone', '+880170000000').replace(/[^+\d]/g, '')}`,
    },
    {
        title: 'Email us',
        detail: text('contact.email', 'info@jackbangladesh.com'),
        note: text(
            'contact.email_note',
            'For product, service and dealer enquiries',
        ),
        icon: Mail,
        href: `mailto:${text('contact.email', 'info@jackbangladesh.com')}`,
    },
    {
        title: 'Visit us',
        detail: text('contact.hours', 'Sunday – Thursday, 9 AM – 6 PM'),
        note: text(
            'contact.hours_note',
            'Please arrange a visit with our team first',
        ),
        icon: Clock3,
    },
]);

function submitForm() {
    submitted.value = true;
}
</script>

<template>
    <Head :title="text('meta_title', 'Contact Jack Bangladesh')" />
    <Navbar />
    <main class="overflow-hidden bg-background font-sans text-foreground">
        <section
            class="relative isolate overflow-hidden bg-jack-blue py-18 text-white sm:py-24"
        >
            <div
                class="absolute inset-0 [background-image:linear-gradient(to_right,white_1px,transparent_1px),linear-gradient(to_bottom,white_1px,transparent_1px)] [background-size:40px_40px] opacity-20"
            ></div>
            <div
                class="absolute top-1/2 -right-20 size-80 -translate-y-1/2 rounded-full border-[42px] border-white/10"
            ></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p
                        class="flex items-center gap-2 text-xs font-bold tracking-[0.2em] text-orange-300 uppercase"
                    >
                        <span class="size-2 rounded-full bg-orange-300"></span
                        >{{ text('hero.eyebrow', 'Contact Jack Bangladesh') }}
                    </p>
                    <h1
                        class="mt-4 text-4xl font-extrabold tracking-tight sm:text-5xl"
                    >
                        {{
                            text(
                                'hero.title',
                                text(
                                    'title',
                                    'Let’s find the right solution for your floor.',
                                ),
                            )
                        }}
                    </h1>
                    <p
                        class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg"
                    >
                        {{
                            text(
                                'hero.description',
                                text(
                                    'description',
                                    'Tell us what you are making, where you need support, or which machine you are considering. Our team is ready to help.',
                                ),
                            )
                        }}
                    </p>
                </div>
            </div>
        </section>

        <section
            class="relative z-10 mx-auto -mt-7 max-w-7xl px-4 sm:px-6 lg:px-8"
        >
            <div
                class="grid gap-px overflow-hidden rounded-xl border border-border bg-border shadow-lg md:grid-cols-3"
            >
                <a
                    v-for="card in contactCards"
                    :key="card.title"
                    :href="card.href"
                    class="group bg-card px-6 py-5 transition hover:bg-muted/50"
                >
                    <div class="flex gap-4">
                        <span
                            class="grid size-10 shrink-0 place-items-center rounded-lg bg-jack-blue/10 text-jack-blue"
                            ><component :is="card.icon" class="size-5"
                        /></span>
                        <div>
                            <p class="text-sm font-bold">{{ card.title }}</p>
                            <p
                                class="mt-1 text-sm font-medium text-jack-blue group-hover:underline"
                            >
                                {{ card.detail }}
                            </p>
                            <p
                                class="mt-1 text-xs leading-4 text-muted-foreground"
                            >
                                {{ card.note }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <section
            class="mx-auto grid max-w-7xl gap-10 px-4 py-18 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:gap-16 lg:px-8 lg:py-24"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase"
                >
                    Send an enquiry
                </p>
                <h2
                    class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    Tell us how we can help.
                </h2>
                <p
                    class="mt-4 max-w-xl text-sm leading-6 text-muted-foreground"
                >
                    Share a few details about your production needs and the
                    right team will follow up with you.
                </p>

                <form
                    v-if="!submitted"
                    class="mt-8 space-y-5"
                    @submit.prevent="submitForm"
                >
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block text-sm font-semibold"
                            >Full name <span class="text-jack-blue">*</span
                            ><input
                                v-model="form.name"
                                required
                                autocomplete="name"
                                placeholder="Your name"
                                class="mt-2 w-full rounded-lg border border-border bg-card px-3.5 py-3 text-sm font-normal transition outline-none focus:border-jack-blue focus:ring-2 focus:ring-jack-blue/15"
                        /></label>
                        <label class="block text-sm font-semibold"
                            >Work email <span class="text-jack-blue">*</span
                            ><input
                                v-model="form.email"
                                required
                                type="email"
                                autocomplete="email"
                                placeholder="you@company.com"
                                class="mt-2 w-full rounded-lg border border-border bg-card px-3.5 py-3 text-sm font-normal transition outline-none focus:border-jack-blue focus:ring-2 focus:ring-jack-blue/15"
                        /></label>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block text-sm font-semibold"
                            >Phone number<input
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                                placeholder="+880 ..."
                                class="mt-2 w-full rounded-lg border border-border bg-card px-3.5 py-3 text-sm font-normal transition outline-none focus:border-jack-blue focus:ring-2 focus:ring-jack-blue/15"
                        /></label>
                        <label class="block text-sm font-semibold"
                            >What can we help with?<select
                                v-model="form.subject"
                                class="mt-2 w-full rounded-lg border border-border bg-card px-3.5 py-3 text-sm font-normal transition outline-none focus:border-jack-blue focus:ring-2 focus:ring-jack-blue/15"
                            >
                                <option value="" disabled>
                                    Select an enquiry type
                                </option>
                                <option value="machine">
                                    Machine selection or quotation
                                </option>
                                <option value="support">
                                    Service or technical support
                                </option>
                                <option value="parts">
                                    Parts and accessories
                                </option>
                                <option value="dealer">
                                    Dealer partnership
                                </option>
                            </select></label
                        >
                    </div>
                    <label class="block text-sm font-semibold"
                        >Your message <span class="text-jack-blue">*</span
                        ><textarea
                            v-model="form.message"
                            required
                            rows="5"
                            placeholder="Tell us about your production requirements, machine model or support need."
                            class="mt-2 w-full resize-none rounded-lg border border-border bg-card px-3.5 py-3 text-sm font-normal transition outline-none focus:border-jack-blue focus:ring-2 focus:ring-jack-blue/15"
                        ></textarea>
                    </label>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-jack-blue px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-jack-blue/90"
                    >
                        <Send class="size-4" /> Send enquiry
                    </button>
                </form>
                <div
                    v-else
                    class="mt-8 rounded-xl border border-jack-blue/20 bg-jack-blue/5 p-6"
                >
                    <CheckCircle2 class="size-8 text-jack-blue" />
                    <h3 class="mt-3 text-lg font-bold">
                        Thanks for getting in touch.
                    </h3>
                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                        Your enquiry is ready for our team. To send it now,
                        please email us at
                        <a
                            :href="`mailto:${text('contact.email', 'info@jackbangladesh.com')}`"
                            class="font-semibold text-jack-blue underline"
                            >{{
                                text('contact.email', 'info@jackbangladesh.com')
                            }}</a
                        >.
                    </p>
                    <button
                        type="button"
                        class="mt-4 text-sm font-bold text-jack-blue hover:underline"
                        @click="submitted = false"
                    >
                        Send another enquiry
                    </button>
                </div>
            </div>

            <aside class="space-y-5 lg:pt-12">
                <div
                    class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
                >
                    <div
                        class="flex min-h-72 flex-col justify-end bg-[radial-gradient(circle_at_top_right,_var(--color-jack-blue),_transparent_62%)] p-7 text-white"
                    >
                        <Building2 class="size-8 text-orange-300" />
                        <p
                            class="mt-16 text-xs font-bold tracking-[0.18em] text-orange-300 uppercase"
                        >
                            Corporate office
                        </p>
                        <h2 class="mt-2 text-gray-900 text-2xl font-bold">
                            {{ text('location.name', 'Jack Bangladesh') }}
                        </h2>
                        <p
                            class="mt-3 max-w-sm text-sm leading-6 text-gray-900"
                        >
                            {{
                                text(
                                    'location.address',
                                    'Connect with our team for machine advice, service support and production solutions.',
                                )
                            }}
                        </p>
                    </div>
                    <a
                        :href="
                            text(
                                'location.map_url',
                                'https://maps.app.goo.gl/EjxWtjUD2PY7Na628',
                            )
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between px-6 py-5 text-sm font-bold text-jack-blue transition hover:bg-muted/50"
                        ><span class="flex items-center gap-3"
                            ><MapPin class="size-5" /> Open in Google Maps</span
                        ><span>↗</span></a
                    >
                </div>
                <div class="rounded-2xl border border-border bg-muted/35 p-6">
                    <p
                        class="text-xs font-bold tracking-[0.18em] text-jack-blue uppercase"
                    >
                        Before you write
                    </p>
                    <h3 class="mt-3 text-xl font-bold">
                        A little detail goes a long way.
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                        If you can, include the machine model, fabric type,
                        operation and the number of machines you need. It helps
                        us give you a more useful response.
                    </p>
                </div>
            </aside>
        </section>
    </main>
    <Footer />
</template>
