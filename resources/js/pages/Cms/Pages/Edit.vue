<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

type PageData = {
    id: number;
    slug: string;
    template_key: string;
    is_published: boolean;
};
type Revision = {
    id: number;
    status: string;
    content: Record<string, any> | null;
    meta_title: string | null;
    meta_description: string | null;
    gallery: { id: number; url: string; alt_text: string | null }[];
};
const props = defineProps<{
    page: PageData;
    revision: Revision;
    published_revision: {
        id: number;
        content: Record<string, any> | null;
    } | null;
}>();
const form = useForm({
    _method: 'patch' as const,
    content: { ...(props.revision.content ?? {}) },
    meta_title: props.revision.meta_title ?? '',
    meta_description: props.revision.meta_description ?? '',
    about_images: {} as Record<string, File>,
    home_images: {} as Record<string, File>,
});
const imagePreviews = ref<Record<string, string>>({});
const isDraft = computed(() => props.revision.status === 'draft');
const fields = computed(() =>
    props.page.template_key === 'home'
        ? [
              ...['primary', 'secondary', 'tertiary'].flatMap((banner) => [
                  {
                      key: `hero.${banner}.eyebrow`,
                      label: `${banner} banner eyebrow`,
                      placeholder:
                          banner === 'primary'
                              ? 'New Arrival'
                              : 'Optional label',
                  },
                  {
                      key: `hero.${banner}.title`,
                      label: `${banner} banner title`,
                      placeholder: 'Banner headline',
                  },
                  {
                      key: `hero.${banner}.description`,
                      label: `${banner} banner description`,
                      placeholder: 'Short introduction',
                  },
                  {
                      key: `hero.${banner}.cta_label`,
                      label: `${banner} banner button label`,
                      placeholder: 'Explore Collection',
                  },
                  {
                      key: `hero.${banner}.cta_url`,
                      label: `${banner} banner button URL`,
                      placeholder: '/products',
                  },
              ]),
          ]
        : props.page.template_key === 'about'
          ? [
                {
                    key: 'hero.eyebrow',
                    label: 'Hero eyebrow',
                    placeholder: 'Who We Are',
                },
                {
                    key: 'hero.title',
                    label: 'Hero title',
                    placeholder: 'Page headline',
                },
                {
                    key: 'hero.description',
                    label: 'Hero description',
                    placeholder: 'Page introduction',
                },
                ...Array.from({ length: 4 }, (_, index) => [
                    {
                        key: `stats.${index}.value`,
                        label: `Statistic ${index + 1} value`,
                        placeholder: '100+',
                    },
                    {
                        key: `stats.${index}.label`,
                        label: `Statistic ${index + 1} label`,
                        placeholder: 'Statistic label',
                    },
                ]).flat(),
                ...Array.from({ length: 3 }, (_, index) => [
                    {
                        key: `pillars.${index}.title`,
                        label: `Pillar ${index + 1} title`,
                        placeholder: 'Pillar title',
                    },
                    {
                        key: `pillars.${index}.description`,
                        label: `Pillar ${index + 1} description`,
                        placeholder: 'Pillar description',
                    },
                ]).flat(),
                {
                    key: 'cta.title',
                    label: 'CTA title',
                    placeholder: 'Call to action headline',
                },
                {
                    key: 'cta.description',
                    label: 'CTA description',
                    placeholder: 'Call to action description',
                },
                {
                    key: 'cta.label',
                    label: 'CTA button label',
                    placeholder: 'Explore products',
                },
                {
                    key: 'cta.url',
                    label: 'CTA button URL',
                    placeholder: '/products',
                },
            ]
          : props.page.template_key === 'contact'
            ? [
                  {
                      key: 'hero.eyebrow',
                      label: 'Hero eyebrow',
                      placeholder: 'Contact Jack Bangladesh',
                  },
                  {
                      key: 'hero.title',
                      label: 'Hero title',
                      placeholder: 'Page headline',
                  },
                  {
                      key: 'hero.description',
                      label: 'Hero description',
                      placeholder: 'Page introduction',
                  },
                  {
                      key: 'contact.phone',
                      label: 'Phone number',
                      placeholder: '+880 1700-000000',
                  },
                  {
                      key: 'contact.phone_note',
                      label: 'Phone support note',
                      placeholder: 'Speak with our sales and support team',
                  },
                  {
                      key: 'contact.email',
                      label: 'Email address',
                      placeholder: 'info@jackbangladesh.com',
                  },
                  {
                      key: 'contact.email_note',
                      label: 'Email support note',
                      placeholder: 'For product, service and dealer enquiries',
                  },
                  {
                      key: 'contact.hours',
                      label: 'Operating hours',
                      placeholder: 'Sunday – Thursday, 9 AM – 6 PM',
                  },
                  {
                      key: 'contact.hours_note',
                      label: 'Hours note',
                      placeholder: 'Please arrange a visit with our team first',
                  },
                  {
                      key: 'location.name',
                      label: 'Office name',
                      placeholder: 'Jack Bangladesh',
                  },
                  {
                      key: 'location.address',
                      label: 'Office description',
                      placeholder:
                          'Office address or helpful location description',
                  },
                  {
                      key: 'location.map_url',
                      label: 'Google Maps URL',
                      placeholder: 'https://maps.app.goo.gl/...',
                  },
              ]
            : [
                  {
                      key: 'title',
                      label: 'Page title',
                      placeholder: 'Page title',
                  },
                  {
                      key: 'description',
                      label: 'Page description',
                      placeholder: 'Page introduction',
                  },
              ],
);
function get(key: string) {
    return key.split('.').reduce((value, part) => value?.[part], form.content);
}
function getString(key: string): string {
    const value = get(key);

    return typeof value === 'string' ? value : '';
}
function set(key: string, value: string) {
    const parts = key.split('.');
    let target = form.content;
    parts.slice(0, -1).forEach((part) => {
        target[part] ??= {};
        target = target[part];
    });
    target[parts.at(-1)!] = value;
}
function imagePreview(type: 'about' | 'home', slot: string): string | undefined {
    return (
        imagePreviews.value[`${type}-${slot}`] ??
        props.revision.gallery.find(
            (image) => image.alt_text === `${type}-${slot}`,
        )?.url ??
        (type === 'home'
            ? getString(`hero.${slot}.image_url`)
            : undefined)
    );
}
function selectImage(type: 'about' | 'home', slot: string, event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        return;
    }

    form[`${type}_images`][slot] = file;
    imagePreviews.value[`${type}-${slot}`] = URL.createObjectURL(file);
}
function save() {
    form.post(`/cms/pages/${props.page.id}/revisions/${props.revision.id}`, {
        forceFormData: true,
    });
}
function submitForReview() {
    router.post(
        `/cms/pages/${props.page.id}/revisions/${props.revision.id}/submit`,
    );
}
</script>
<template>
    <Head :title="`Edit ${page.template_key}`" />
    <div class="mx-auto w-full max-w-4xl p-4 md:p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">
                    Edit {{ page.template_key }} page
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    /{{ page.slug }} · {{ revision.status.replace('_', ' ') }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child
                    ><Link :href="`/cms/pages/${page.id}/revisions`"
                        >History</Link
                    ></Button
                ><Button variant="outline" as-child
                    ><Link href="/cms/pages">Cancel</Link></Button
                >
            </div>
        </div>
        <form class="space-y-6" @submit.prevent="save">
            <section class="space-y-4 rounded-lg border bg-card p-5">
                <div v-for="field in fields" :key="field.key" class="space-y-2">
                    <label :for="field.key" class="text-sm font-medium">{{
                        field.label
                    }}</label
                    ><textarea
                        v-if="field.key.endsWith('description')"
                        :id="field.key"
                        :value="getString(field.key)"
                        :placeholder="field.placeholder"
                        rows="4"
                        class="w-full rounded-md border bg-background px-3 py-2"
                        @input="
                            set(
                                field.key,
                                ($event.target as HTMLTextAreaElement).value,
                            )
                        "
                    /><input
                        v-else
                        :id="field.key"
                        :value="getString(field.key)"
                        :placeholder="field.placeholder"
                        class="w-full rounded-md border bg-background px-3 py-2"
                        @input="
                            set(
                                field.key,
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                    />
                </div>
            </section>
            <section
                v-if="page.template_key === 'about'"
                class="space-y-5 rounded-lg border bg-card p-5"
            >
                <div>
                    <h2 class="text-base font-semibold">About page images</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Uploading a replacement affects this draft only. The
                        current live image remains unchanged until publishing.
                    </p>
                </div>
                <div class="grid gap-5">
                    <div
                        v-for="image in [
                            {
                                slot: 'hero',
                                label: 'Hero machine image',
                                hint: 'Displayed in the blue hero panel.',
                            },
                        ]"
                        :key="image.slot"
                        class="space-y-3"
                    >
                        <div
                            class="aspect-video overflow-hidden rounded-lg border bg-muted"
                        >
                            <img
                                v-if="imagePreview('about', image.slot)"
                                :src="imagePreview('about', image.slot)"
                                :alt="image.label"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="grid size-full place-items-center px-4 text-center text-sm text-muted-foreground"
                            >
                                No image uploaded yet
                            </div>
                        </div>
                        <div>
                            <label
                                :for="'about-image-' + image.slot"
                                class="text-sm font-medium"
                                >{{ image.label }}</label
                            >
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ image.hint }} JPG, PNG, or WebP up to 5 MB.
                            </p>
                            <input
                                :id="'about-image-' + image.slot"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="mt-3 block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-jack-blue file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-jack-blue/90"
                                @change="selectImage('about', image.slot, $event)"
                            />
                            <InputError
                                :message="form.errors['about_images.hero']"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>
            </section>
            <section
                v-if="page.template_key === 'home'"
                class="space-y-5 rounded-lg border bg-card p-5"
            >
                <div>
                    <h2 class="text-base font-semibold">Homepage hero images</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Uploading a replacement affects this draft only. The current live image remains unchanged until publishing.
                    </p>
                </div>
                <div class="grid gap-5 md:grid-cols-3">
                    <div
                        v-for="image in [
                            { slot: 'primary', label: 'Primary banner image', hint: 'Large hero banner.' },
                            { slot: 'secondary', label: 'Secondary banner image', hint: 'Top-right hero banner.' },
                            { slot: 'tertiary', label: 'Tertiary banner image', hint: 'Bottom-right hero banner.' },
                        ]"
                        :key="image.slot"
                        class="space-y-3"
                    >
                        <div class="aspect-video overflow-hidden rounded-lg border bg-muted">
                            <img
                                v-if="imagePreview('home', image.slot)"
                                :src="imagePreview('home', image.slot)"
                                :alt="image.label"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="grid size-full place-items-center px-4 text-center text-sm text-muted-foreground"
                            >
                                No image uploaded yet
                            </div>
                        </div>
                        <div>
                            <label :for="'home-image-' + image.slot" class="text-sm font-medium">{{ image.label }}</label>
                            <p class="mt-1 text-xs text-muted-foreground">{{ image.hint }} JPG, PNG, or WebP up to 5 MB.</p>
                            <input
                                :id="'home-image-' + image.slot"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="mt-3 block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-jack-blue file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-jack-blue/90"
                                @change="selectImage('home', image.slot, $event)"
                            />
                            <InputError :message="form.errors[`home_images.${image.slot}`]" class="mt-2" />
                        </div>
                    </div>
                </div>
            </section>
            <section
                class="grid gap-4 rounded-lg border bg-card p-5 md:grid-cols-2"
            >
                <div class="space-y-2">
                    <label for="meta-title" class="text-sm font-medium"
                        >SEO title</label
                    ><input
                        id="meta-title"
                        v-model="form.meta_title"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><InputError :message="form.errors.meta_title" />
                </div>
                <div class="space-y-2">
                    <label for="meta-description" class="text-sm font-medium"
                        >SEO description</label
                    ><textarea
                        id="meta-description"
                        v-model="form.meta_description"
                        rows="3"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><InputError :message="form.errors.meta_description" />
                </div>
            </section>
            <div class="flex justify-end gap-3">
                <Button type="submit" :disabled="form.processing || !isDraft"
                    >Save draft</Button
                ><Button
                    v-if="isDraft"
                    type="button"
                    variant="outline"
                    @click="submitForReview"
                    >Submit for review</Button
                >
            </div>
        </form>
    </div>
</template>
