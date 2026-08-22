<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
type Revision = {
    id: number;
    status: string;
    meta_title: string | null;
    submitted_by: string | null;
    approved_by: string | null;
    rejection_reason: string | null;
    created_at: string;
    is_live: boolean;
};
const props = defineProps<{
    page: { id: number; slug: string; template_key: string };
    revisions: Revision[];
    can_create_draft: boolean;
}>();
const canModerate = usePage().props.auth.roles.includes('super_admin');
function action(
    revision: Revision,
    name: 'submit' | 'approve' | 'reject' | 'publish',
) {
    let data = {};

    if (name === 'reject') {
        if (!confirm('Request changes to this page?')) {
return;
}

        data = { reason: prompt('Optional reason:') ?? '' };
    }

    router.post(
        `/cms/pages/${props.page.id}/revisions/${revision.id}/${name}`,
        data,
    );
}
function newDraft() {
    router.post(`/cms/pages/${props.page.id}/revisions`);
}
</script>
<template>
    <Head :title="`Revisions: ${page.template_key}`" />
    <div class="mx-auto w-full max-w-4xl p-4 md:p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Revision history</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    /{{ page.slug }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child
                    ><Link href="/cms/pages">All pages</Link></Button
                ><Button v-if="can_create_draft" @click="newDraft"
                    >New draft</Button
                >
            </div>
        </div>
        <div class="space-y-3">
            <article
                v-for="revision in revisions"
                :key="revision.id"
                class="rounded-lg border bg-card p-4"
            >
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-medium">
                                {{ revision.meta_title ?? page.template_key }}
                            </h2>
                            <span
                                class="rounded-full bg-muted px-2 py-1 text-xs capitalize"
                                >{{ revision.status.replace('_', ' ') }}</span
                            ><span
                                v-if="revision.is_live"
                                class="rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700"
                                >Live</span
                            >
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Created {{ revision.created_at
                            }}<span v-if="revision.submitted_by">
                                · Submitted by {{ revision.submitted_by }}</span
                            ><span v-if="revision.approved_by">
                                · Approved by {{ revision.approved_by }}</span
                            >
                        </p>
                        <p
                            v-if="revision.rejection_reason"
                            class="mt-2 rounded bg-amber-50 px-3 py-2 text-sm text-amber-800"
                        >
                            Requested changes: {{ revision.rejection_reason }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Button
                            v-if="revision.status === 'draft'"
                            size="sm"
                            variant="outline"
                            as-child
                            ><Link :href="`/cms/pages/${page.id}/edit`"
                                >Edit</Link
                            ></Button
                        ><Button
                            v-if="revision.status === 'draft'"
                            size="sm"
                            @click="action(revision, 'submit')"
                            >Submit</Button
                        ><Button
                            v-if="canModerate && revision.status === 'pending_review'"
                            size="sm"
                            @click="action(revision, 'approve')"
                            >Approve</Button
                        ><Button
                            v-if="canModerate && revision.status === 'pending_review'"
                            size="sm"
                            variant="outline"
                            @click="action(revision, 'reject')"
                            >Request changes</Button
                        ><Button
                            v-if="canModerate && revision.status === 'approved'"
                            size="sm"
                            @click="action(revision, 'publish')"
                            >Publish</Button
                        >
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
