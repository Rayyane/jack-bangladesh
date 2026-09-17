<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Plus, Trash2 } from '@lucide/vue';
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
    leadership_messages: Array.isArray(props.revision.content?.leadership_messages)
        ? props.revision.content.leadership_messages.map(normaliseLeadership)
        : ['chairman', 'md'].map((key) => ({
              title: typeof props.revision.content?.leadership?.[key]?.title === 'string' ? props.revision.content.leadership[key].title : '',
              body: typeof props.revision.content?.leadership?.[key]?.body === 'string' ? props.revision.content.leadership[key].body : '',
              name: typeof props.revision.content?.leadership?.[key]?.name === 'string' ? props.revision.content.leadership[key].name : '',
              designation: typeof props.revision.content?.leadership?.[key]?.designation === 'string' ? props.revision.content.leadership[key].designation : '',
              image_slot: key,
          })),
    team_sections: Array.isArray(props.revision.content?.team?.sections)
        ? props.revision.content.team.sections.map(normaliseTeamSection)
        : Array.isArray(props.revision.content?.team?.members)
          ? [{ title: 'Our team', description: '', members: props.revision.content.team.members.map(normaliseMember) }]
          : [],
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
                {
                    key: 'hero.image_title',
                    label: 'Hero image title',
                    placeholder: 'Built around your floor',
                },
                {
                    key: 'hero.image_caption',
                    label: 'Hero image caption',
                    placeholder: 'Short caption below the hero image',
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
                { key: 'who.eyebrow', label: 'Who we are eyebrow', placeholder: 'Who we are' },
                { key: 'who.title', label: 'Who we are title', placeholder: 'Section headline' },
                { key: 'who.description_first', label: 'Who we are first paragraph', placeholder: 'First paragraph' },
                { key: 'who.description_second', label: 'Who we are second paragraph', placeholder: 'Second paragraph' },
                ...Array.from({ length: 3 }, (_, index) => ({
                    key: `who.bullets.${index}`,
                    label: `Who we are bullet ${index + 1}`,
                    placeholder: 'Bullet point',
                })),
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
function normaliseLeadership(message: Record<string, unknown>, index: number) {
    return {
        title: typeof message.title === 'string' ? message.title : '', body: typeof message.body === 'string' ? message.body : '',
        name: typeof message.name === 'string' ? message.name : '', designation: typeof message.designation === 'string' ? message.designation : '',
        image_slot: typeof message.image_slot === 'string' ? message.image_slot : `leadership-${index + 1}`,
    };
}
function normaliseMember(member: Record<string, unknown>, index: number) {
    return { name: typeof member.name === 'string' ? member.name : '', designation: typeof member.designation === 'string' ? member.designation : '', phone: typeof member.phone === 'string' ? member.phone : '', email: typeof member.email === 'string' ? member.email : '', image_slot: typeof member.image_slot === 'string' ? member.image_slot : `team-${index + 1}` };
}
function normaliseTeamSection(section: Record<string, unknown>, index: number) {
    return { title: typeof section.title === 'string' ? section.title : `Team ${index + 1}`, description: typeof section.description === 'string' ? section.description : '', members: Array.isArray(section.members) ? section.members.map(normaliseMember) : [] };
}
function uniqueSlot(prefix: string) { return `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`; }
function addLeadershipMessage() { form.leadership_messages.push({ title: '', body: '', name: '', designation: '', image_slot: uniqueSlot('leadership') }); }
function removeLeadershipMessage(index: number) {
    const [member] = form.leadership_messages.splice(index, 1);
    if (member?.image_slot) delete form.about_images[member.image_slot];
}
function addTeamSection() { form.team_sections.push({ title: '', description: '', members: [] }); }
function removeTeamSection(index: number) { const [section] = form.team_sections.splice(index, 1); section?.members.forEach((member: { image_slot: string }) => delete form.about_images[member.image_slot]); }
function addTeamMember(sectionIndex: number) { form.team_sections[sectionIndex].members.push({ name: '', designation: '', phone: '', email: '', image_slot: uniqueSlot('team') }); }
function removeTeamMember(sectionIndex: number, memberIndex: number) { const [member] = form.team_sections[sectionIndex].members.splice(memberIndex, 1); if (member?.image_slot) delete form.about_images[member.image_slot]; }
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
                        v-if="field.key.endsWith('description') || field.key.endsWith('body') || field.key.includes('paragraph')"
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
                            { slot: 'hero', label: 'Hero machine image', hint: 'Displayed in the blue hero panel.' },
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
                                :message="form.errors[`about_images.${image.slot}`]"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>
            </section>
            <section v-if="page.template_key === 'about'" class="space-y-5 rounded-lg border bg-card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-base font-semibold">Leadership messages</h2><p class="mt-1 text-sm text-muted-foreground">Add any number of messages and portraits.</p></div><Button type="button" variant="outline" @click="addLeadershipMessage"><Plus class="size-4" /> Add message</Button></div>
                <div v-for="(message, index) in form.leadership_messages" :key="message.image_slot" class="rounded-lg border p-4"><div class="flex justify-between"><h3 class="font-medium">Message {{ index + 1 }}</h3><Button type="button" size="icon" variant="ghost" @click="removeLeadershipMessage(index)"><Trash2 class="size-4 text-destructive" /></Button></div><div class="mt-3 grid gap-3 md:grid-cols-2"><input v-model="message.title" placeholder="Section title" class="rounded-md border bg-background px-3 py-2" /><input v-model="message.name" placeholder="Name" class="rounded-md border bg-background px-3 py-2" /><input v-model="message.designation" placeholder="Title / designation" class="rounded-md border bg-background px-3 py-2" /><textarea v-model="message.body" rows="4" placeholder="Message body" class="rounded-md border bg-background px-3 py-2" /></div><input type="file" accept="image/jpeg,image/png,image/webp" class="mt-3 block w-full text-sm" @change="selectImage('about', message.image_slot, $event)" /></div>
            </section>
            <section v-if="page.template_key === 'about'" class="space-y-5 rounded-lg border bg-card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-base font-semibold">Our teams</h2><p class="mt-1 text-sm text-muted-foreground">Create departments, then add as many members as each needs.</p></div><Button type="button" variant="outline" @click="addTeamSection"><Plus class="size-4" /> Add team section</Button></div>
                <div v-for="(section, sectionIndex) in form.team_sections" :key="sectionIndex" class="rounded-lg border p-4"><div class="flex justify-between"><h3 class="font-medium">Team section {{ sectionIndex + 1 }}</h3><Button type="button" size="icon" variant="ghost" @click="removeTeamSection(sectionIndex)"><Trash2 class="size-4 text-destructive" /></Button></div><div class="mt-3 grid gap-3 md:grid-cols-2"><input v-model="section.title" placeholder="Marketing team" class="rounded-md border bg-background px-3 py-2" /><input v-model="section.description" placeholder="Optional description" class="rounded-md border bg-background px-3 py-2" /></div><div class="mt-4 flex justify-between border-t pt-4"><h4 class="font-medium">Members</h4><Button type="button" size="sm" variant="outline" @click="addTeamMember(sectionIndex)"><Plus class="size-4" /> Add member</Button></div><div v-for="(member, memberIndex) in section.members" :key="member.image_slot" class="mt-3 rounded-md border p-3"><div class="flex justify-between"><span class="text-sm font-medium">Member {{ memberIndex + 1 }}</span><Button type="button" size="icon" variant="ghost" @click="removeTeamMember(sectionIndex, memberIndex)"><Trash2 class="size-4 text-destructive" /></Button></div><div class="mt-2 grid gap-3 md:grid-cols-2"><input v-model="member.name" placeholder="Name" class="rounded-md border bg-background px-3 py-2" /><input v-model="member.designation" placeholder="Title / designation" class="rounded-md border bg-background px-3 py-2" /><input v-model="member.phone" placeholder="Phone number" class="rounded-md border bg-background px-3 py-2" /><input v-model="member.email" type="email" placeholder="Email address" class="rounded-md border bg-background px-3 py-2" /></div><input type="file" accept="image/jpeg,image/png,image/webp" class="mt-3 block w-full text-sm" @change="selectImage('about', member.image_slot, $event)" /></div></div>
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
