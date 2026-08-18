<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
type Revision = {
    id: number;
    status: string;
    name: string;
    submitted_by: string | null;
    approved_by: string | null;
    rejection_reason: string | null;
    publish_at: string | null;
    published_at: string | null;
    created_at: string;
    is_live: boolean;
};
const props = defineProps<{
    product: { id: number; slug: string; name: string };
    revisions: Revision[];
    can_create_draft: boolean;
}>();
const page = usePage();
const isSuperAdmin = computed(() =>
    page.props.auth.roles.includes('super_admin'),
);
function post(
    revision: Revision,
    action: 'submit' | 'approve' | 'reject' | 'publish',
) {
    const data: Record<string, string> = {};

    if (action === 'reject') {
        if (!confirm('Return this revision to draft?')) {
            return;
        }

        data.reason = prompt('Optional reason for requesting changes:') ?? '';
    }

    router.post(
        `/cms/products/${props.product.id}/revisions/${revision.id}/${action}`,
        data,
    );
}
function newDraft() {
    router.post(`/cms/products/${props.product.id}/revisions`);
}
function readable(status: string) {
    return status.replace('_', ' ');
}
</script>
<template>
    <Head :title="`Revisions: ${product.name}`" />
    <div class="mx-auto w-full max-w-4xl p-4 md:p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Revision history</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ product.name }} · /products/{{ product.slug }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child
                    ><Link href="/cms/products">All products</Link></Button
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
                            <h2 class="font-medium">{{ revision.name }}</h2>
                            <span
                                class="rounded-full bg-muted px-2 py-1 text-xs capitalize"
                                >{{ readable(revision.status) }}</span
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
                            class="mt-2 rounded-md bg-amber-50 px-3 py-2 text-sm text-amber-800"
                        >
                            Requested changes: {{ revision.rejection_reason }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-if="revision.status === 'draft'"
                            size="sm"
                            variant="outline"
                            as-child
                            ><Link :href="`/cms/products/${product.id}/edit`"
                                >Edit</Link
                            ></Button
                        ><Button
                            v-if="revision.status === 'draft'"
                            size="sm"
                            @click="post(revision, 'submit')"
                            >Submit for review</Button
                        ><template v-if="isSuperAdmin"
                            ><Button
                                v-if="revision.status === 'pending_review'"
                                size="sm"
                                @click="post(revision, 'approve')"
                                >Approve</Button
                            ><Button
                                v-if="revision.status === 'pending_review'"
                                size="sm"
                                variant="outline"
                                @click="post(revision, 'reject')"
                                >Request changes</Button
                            ><Button
                                v-if="revision.status === 'approved'"
                                size="sm"
                                @click="post(revision, 'publish')"
                                >Publish</Button
                            ></template
                        >
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
