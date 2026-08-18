<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

type ParentOption = { id: number; label: string };
type Category = {
    id: number;
    name: string;
    parent_id: number | null;
    sort_order: number;
    is_featured: boolean;
    show_in_nav: boolean;
    image_url: string | null;
};

const props = defineProps<{
    title: string;
    submitUrl: string;
    parentOptions: ParentOption[];
    category?: Category;
}>();

const form = useForm({
    _method: props.category ? 'patch' : undefined,
    name: props.category?.name ?? '',
    parent_id: props.category?.parent_id ?? null as number | null,
    sort_order: props.category?.sort_order ?? 0,
    is_featured: props.category?.is_featured ?? false,
    show_in_nav: props.category?.show_in_nav ?? true,
    image: null as File | null,
});

const imagePreview = ref(props.category?.image_url ?? null);
const isParentCategory = computed(() => form.parent_id === null);
const isDraggingImage = ref(false);
const isNewImage = ref(false);
const uploadStatus = ref<'idle' | 'ready' | 'uploading' | 'complete'>('idle');
const showUploadProgress = computed(() => Boolean(form.image) && (uploadStatus.value === 'uploading' || Boolean(form.progress)));
let objectUrl: string | null = null;

watch(isParentCategory, (isParent) => {
    if (!isParent) form.show_in_nav = false;
});

function setImage(file: File | null) {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = null;
    form.image = file;
    isNewImage.value = Boolean(file);
    uploadStatus.value = file ? 'ready' : 'idle';
    imagePreview.value = props.category?.image_url ?? null;

    if (file) {
        objectUrl = URL.createObjectURL(file);
        imagePreview.value = objectUrl;
    }
}

function selectImage(event: Event) {
    setImage((event.target as HTMLInputElement).files?.[0] ?? null);
}

function dropImage(event: DragEvent) {
    isDraggingImage.value = false;
    setImage(event.dataTransfer?.files?.[0] ?? null);
}

function submit() {
    const hasNewImage = Boolean(form.image);

    form.post(props.submitUrl, {
        forceFormData: true,
        onStart: () => {
            if (hasNewImage) uploadStatus.value = 'uploading';
        },
        onSuccess: () => {
            if (hasNewImage) {
                uploadStatus.value = 'complete';
                alert('Image upload complete.');
            }
        },
    });
}

onBeforeUnmount(() => {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
});
</script>

<template>
    <Head :title="title" />

    <div class="mx-auto w-full max-w-2xl p-4 md:p-6">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">{{ title }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Parent categories control the top-level navigation.
                </p>
            </div>
            <Button variant="outline" as-child><Link href="/cms/categories">Cancel</Link></Button>
        </div>

        <form class="space-y-6 rounded-lg border bg-card p-5" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Name</label>
                <input id="name" v-model="form.name" class="w-full rounded-md border bg-background px-3 py-2" autofocus />
                <InputError :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <label for="parent" class="text-sm font-medium">Parent category</label>
                <select id="parent" v-model="form.parent_id" class="w-full rounded-md border bg-background px-3 py-2">
                    <option :value="null">Save as parent category</option>
                    <option v-for="option in parentOptions" :key="option.id" :value="option.id">{{ option.label }}</option>
                </select>
                <p class="text-xs text-muted-foreground">Choose an existing category to save this as its child.</p>
                <InputError :message="form.errors.parent_id" />
            </div>

            <div class="min-w-0 space-y-4 overflow-hidden rounded-lg border-2 border-dashed bg-muted/20 p-4 sm:p-5" :class="isDraggingImage ? 'border-primary bg-primary/5' : 'border-border'" @dragenter.prevent="isDraggingImage = true" @dragover.prevent="isDraggingImage = true" @dragleave.prevent="isDraggingImage = false" @drop.prevent="dropImage">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-medium">Category image</p>
                        <p class="mt-1 text-sm text-muted-foreground">Drag one image here, or choose a file. PNG, JPG, WebP, or GIF up to 5 MB.</p>
                    </div>
                    <label for="image" class="shrink-0 self-start cursor-pointer rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground sm:self-auto">Choose image</label>
                    <input id="image" type="file" accept="image/*" class="sr-only" @change="selectImage" />
                </div>

                <div v-if="imagePreview" class="flex min-w-0 flex-col gap-3 rounded-md border bg-background p-3 sm:flex-row sm:items-center sm:gap-4">
                    <img :src="imagePreview" :alt="isNewImage ? 'New category image preview' : 'Current category image'" class="h-32 w-full max-w-full rounded object-cover sm:h-24 sm:w-32 sm:shrink-0" />
                    <div class="min-w-0 overflow-hidden">
                        <p class="truncate font-medium" :title="isNewImage ? form.image?.name : 'Current image'">{{ isNewImage ? form.image?.name : 'Current image' }}</p>
                        <p class="mt-1 break-words text-sm text-muted-foreground">{{ isNewImage ? 'New image ready to upload' : 'This image is currently in use.' }}</p>
                        <button v-if="isNewImage" type="button" class="mt-2 text-sm text-destructive underline" @click="setImage(null)">Remove selected image</button>
                    </div>
                </div>

                <div v-if="showUploadProgress" class="space-y-1">
                    <div class="h-2 overflow-hidden rounded-full bg-muted"><div class="h-full bg-primary transition-all" :style="{ width: `${form.progress?.percentage ?? 0}%` }" /></div>
                    <p class="text-sm text-muted-foreground">Uploading image: {{ form.progress?.percentage ?? 0 }}%</p>
                </div>
                <p v-if="uploadStatus === 'complete'" class="rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-700">Image upload complete.</p>
                <InputError :message="form.errors.image" />
            </div>

            <label class="flex items-center gap-3 text-sm">
                <input v-model="form.is_featured" type="checkbox" class="size-4" />
                Feature this category on the homepage
            </label>

            <label v-if="isParentCategory" class="flex items-center gap-3 text-sm">
                <input v-model="form.show_in_nav" type="checkbox" class="size-4" />
                Show this parent category in the navigation bar
            </label>

            <div class="flex justify-end gap-3 border-t pt-5">
                <Button variant="outline" as-child><Link href="/cms/categories">Cancel</Link></Button>
                <Button type="submit" :disabled="form.processing">{{ category ? 'Save changes' : 'Create category' }}</Button>
            </div>
        </form>
    </div>
</template>
