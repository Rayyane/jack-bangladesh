<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
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
});
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
                  {
                      key: `hero.${banner}.image_url`,
                      label: `${banner} banner image URL`,
                      placeholder: `/hero-${banner === 'primary' ? '1' : banner === 'secondary' ? '2' : '3'}.png`,
                  },
              ]),
          ]
        : [
              { key: 'title', label: 'Page title', placeholder: 'Page title' },
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
