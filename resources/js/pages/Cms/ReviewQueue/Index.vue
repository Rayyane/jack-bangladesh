<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Check, ExternalLink, History, X } from '@lucide/vue';
import { Button } from '@/components/ui/button';

type QueueItem = {
    id: number;
    type: 'page' | 'product';
    label: string;
    category?: string;
    submitted_by: string;
    submitted_at: string;
    waiting_for: string;
    links: { approve: string; reject: string; view: string; history: string };
};
const props = defineProps<{
    queue: QueueItem[];
    counts: { total: number; pages: number; products: number };
}>();

function approve(item: QueueItem) {
    if (confirm(`Approve “${item.label}”?`)) {
        router.post(item.links.approve);
    }
}
function reject(item: QueueItem) {
    if (!confirm(`Request changes to “${item.label}”?`)) {
        return;
    }

    router.post(item.links.reject, {
        reason: prompt('Optional reason for requesting changes:') ?? '',
    });
}
</script>

<template>
    <Head title="Review queue" />
    <div class="mx-auto w-full max-w-6xl p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Review queue</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Review submitted pages and products before they go live.
            </p>
        </div>
        <div class="mb-6 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Waiting total</p>
                <p class="mt-1 text-2xl font-semibold">{{ counts.total }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Products</p>
                <p class="mt-1 text-2xl font-semibold">{{ counts.products }}</p>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <p class="text-sm text-muted-foreground">Pages</p>
                <p class="mt-1 text-2xl font-semibold">{{ counts.pages }}</p>
            </div>
        </div>
        <div v-if="props.queue.length" class="space-y-3">
            <article
                v-for="item in props.queue"
                :key="`${item.type}-${item.id}`"
                class="rounded-lg border bg-card p-4"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full bg-muted px-2 py-1 text-xs uppercase"
                                >{{ item.type }}</span
                            >
                            <h2 class="font-medium">{{ item.label }}</h2>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ item.category ?? ''
                            }}<span v-if="item.category"> · </span>Submitted by
                            {{ item.submitted_by }} · {{ item.waiting_for }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button size="sm" variant="outline" as-child
                            ><a :href="item.links.view"
                                ><ExternalLink class="size-4" /> Inspect</a
                            ></Button
                        ><Button size="sm" variant="outline" as-child
                            ><a :href="item.links.history"
                                ><History class="size-4" /> History</a
                            ></Button
                        ><Button size="sm" @click="approve(item)"
                            ><Check class="size-4" /> Approve</Button
                        ><Button
                            size="sm"
                            variant="destructive"
                            @click="reject(item)"
                            ><X class="size-4" /> Request changes</Button
                        >
                    </div>
                </div>
            </article>
        </div>
        <div
            v-else
            class="rounded-lg border border-dashed p-12 text-center text-sm text-muted-foreground"
        >
            The review queue is clear.
        </div>
    </div>
</template>
