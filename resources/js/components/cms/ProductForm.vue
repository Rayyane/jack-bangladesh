<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus, Plus, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

type Category = { id: number; name: string; label: string; depth: number };
type Section = {
    id?: number;
    title: string;
    description: string;
    sort_order: number;
    image_url?: string | null;
};
type GalleryImage = {
    id: number;
    url: string;
    alt_text: string | null;
    size?: string;
};
type Revision = {
    id: number;
    status: string;
    name: string;
    description: string;
    price: string | null;
    meta_title: string | null;
    meta_description: string | null;
    video_url: string | null;
    primary_image_url: string | null;
    card_image_url: string | null;
    sections: Section[];
    gallery: GalleryImage[];
    specifications: { id: number; url: string } | null;
};

const props = defineProps<{
    product?: {
        id: number;
        slug: string;
        category_id: number | null;
        is_featured: boolean;
        is_published: boolean;
    };
    revision?: Revision;
    categories: Category[];
}>();
const editing = computed(() => Boolean(props.product && props.revision));

const form = useForm({
    _method: editing.value ? 'patch' : (undefined as 'patch' | undefined),
    category_id: props.product?.category_id ?? (null as number | null),
    is_featured: props.product?.is_featured ?? false,
    name: props.revision?.name ?? '',
    description: props.revision?.description ?? '',
    price: props.revision?.price ?? '',
    meta_title: props.revision?.meta_title ?? '',
    meta_description: props.revision?.meta_description ?? '',
    video_url: props.revision?.video_url ?? '',
    primary_image: null as File | null,
    card_image: null as File | null,
    sections: (props.revision?.sections ?? []).map((section, index) => ({
        ...section,
        sort_order: section.sort_order ?? index,
    })),
    section_images: {} as Record<number, File>,
    gallery_images: [] as File[],
    remove_gallery_ids: [] as number[],
    spec_image: null as File | null,
});

const galleryPreviews = ref<{ file: File; url: string }[]>([]);
const sectionPreviews = ref<Record<number, string>>({});
const specificationPreview = ref<string | null>(
    props.revision?.specifications?.url ?? null,
);
const primaryImagePreview = ref<string | null>(
    props.revision?.primary_image_url ?? null,
);
const cardImagePreview = ref<string | null>(
    props.revision?.card_image_url ?? null,
);

function addSection() {
    form.sections.push({
        title: '',
        description: '',
        sort_order: form.sections.length,
    });
}
function removeSection(index: number) {
    form.sections.splice(index, 1);
    delete form.section_images[index];
    delete sectionPreviews.value[index];
    form.sections.forEach(
        (section, position) => (section.sort_order = position),
    );
}
function chooseSectionImage(index: number, event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        return;
    }

    form.section_images[index] = file;
    sectionPreviews.value[index] = URL.createObjectURL(file);
}
function chooseGallery(event: Event) {
    const files = Array.from((event.target as HTMLInputElement).files ?? []);
    form.gallery_images.push(...files);
    galleryPreviews.value.push(
        ...files.map((file) => ({ file, url: URL.createObjectURL(file) })),
    );
}
function removeNewGallery(index: number) {
    URL.revokeObjectURL(galleryPreviews.value[index].url);
    galleryPreviews.value.splice(index, 1);
    form.gallery_images.splice(index, 1);
}
function removeExistingGallery(id: number) {
    if (!form.remove_gallery_ids.includes(id)) {
        form.remove_gallery_ids.push(id);
    }
}
function chooseSpecification(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.spec_image = file;

    if (file) {
        specificationPreview.value = URL.createObjectURL(file);
    }
}
function chooseImage(field: 'primary_image' | 'card_image', event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form[field] = file;

    if (file) {
        if (field === 'primary_image') {
            primaryImagePreview.value = URL.createObjectURL(file);
        } else {
            cardImagePreview.value = URL.createObjectURL(file);
        }
    }
}
function submit() {
    const url = editing.value
        ? `/cms/products/${props.product!.id}/revisions/${props.revision!.id}`
        : '/cms/products';
    form.post(url, { forceFormData: true });
}
</script>

<template>
    <Head :title="editing ? `Edit ${product?.slug}` : 'Create product'" />
    <div class="mx-auto w-full max-w-5xl p-4 md:p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">
                    {{ editing ? 'Edit product draft' : 'Create product' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{
                        editing
                            ? `Editing draft • /products/${product?.slug}`
                            : 'New products begin as drafts and must be reviewed before publication.'
                    }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button v-if="editing" variant="outline" as-child
                    ><Link :href="`/cms/products/${product?.id}/revisions`"
                        >Revision history</Link
                    ></Button
                ><Button variant="outline" as-child
                    ><Link href="/cms/products">Cancel</Link></Button
                >
            </div>
        </div>
        <form class="space-y-6" @submit.prevent="submit">
            <section
                class="grid gap-5 rounded-lg border bg-card p-5 md:grid-cols-2"
            >
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium"
                        >Product name</label
                    ><input
                        id="name"
                        v-model="form.name"
                        class="w-full rounded-md border bg-background px-3 py-2"
                        autofocus
                    /><InputError :message="form.errors.name" />
                </div>
                <div class="space-y-2">
                    <label for="category" class="text-sm font-medium"
                        >Category</label
                    ><select
                        id="category"
                        v-model="form.category_id"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    >
                        <option :value="null">Uncategorised</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.label }}
                        </option></select
                    ><InputError :message="form.errors.category_id" />
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label for="description" class="text-sm font-medium"
                        >Description</label
                    ><textarea
                        id="description"
                        v-model="form.description"
                        rows="6"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><InputError :message="form.errors.description" />
                </div>
                <div class="space-y-2">
                    <label for="price" class="text-sm font-medium">Price</label
                    ><input
                        id="price"
                        v-model="form.price"
                        placeholder="e.g. ৳ 90,000"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><InputError :message="form.errors.price" />
                </div>
                <label class="flex items-center gap-3 text-sm md:col-span-2"
                    ><input
                        v-model="form.is_featured"
                        type="checkbox"
                        class="size-4"
                    />
                    Feature this product</label
                >
            </section>
            <section
                class="grid gap-5 rounded-lg border bg-card p-5 md:grid-cols-2"
            >
                <div class="space-y-3">
                    <div>
                        <h2 class="font-semibold">
                            Primary image
                            <span class="text-destructive">*</span>
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            The main hero image on the product page.
                        </p>
                    </div>
                    <label
                        for="primary-image"
                        class="inline-flex cursor-pointer rounded-md border px-3 py-2 text-sm"
                        >Choose primary image</label
                    ><input
                        id="primary-image"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        @change="chooseImage('primary_image', $event)"
                    /><img
                        v-if="primaryImagePreview"
                        :src="primaryImagePreview"
                        alt="Primary product preview"
                        class="h-48 w-full rounded border object-contain"
                    /><InputError :message="form.errors.primary_image" />
                </div>
                <div v-if="form.is_featured" class="space-y-3">
                    <div>
                        <h2 class="font-semibold">
                            Card image <span class="text-destructive">*</span>
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Required for featured-product cards.
                        </p>
                    </div>
                    <label
                        for="card-image"
                        class="inline-flex cursor-pointer rounded-md border px-3 py-2 text-sm"
                        >Choose card image</label
                    ><input
                        id="card-image"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        @change="chooseImage('card_image', $event)"
                    /><img
                        v-if="cardImagePreview"
                        :src="cardImagePreview"
                        alt="Product card preview"
                        class="h-48 w-full rounded border object-cover"
                    /><InputError :message="form.errors.card_image" />
                </div>
            </section>
            <section class="space-y-4 rounded-lg border bg-card p-5">
                <div>
                    <h2 class="font-semibold">Product sections</h2>
                    <p class="text-sm text-muted-foreground">
                        Add ordered content sections for the product page.
                    </p>
                </div>
                <div
                    v-for="(section, index) in form.sections"
                    :key="section.id ?? index"
                    class="space-y-3 rounded-md border p-4"
                >
                    <div class="flex justify-between gap-3">
                        <strong>Section {{ index + 1 }}</strong
                        ><Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeSection(index)"
                            ><Trash2 class="size-4 text-destructive"
                        /></Button>
                    </div>
                    <input
                        v-model="section.title"
                        placeholder="Section title"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><textarea
                        v-model="section.description"
                        rows="3"
                        placeholder="Section description"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    />
                    <div class="flex flex-wrap items-center gap-3">
                        <label
                            :for="`section-image-${index}`"
                            class="cursor-pointer rounded-md border px-3 py-2 text-sm"
                            >Choose section image</label
                        ><input
                            :id="`section-image-${index}`"
                            type="file"
                            accept="image/*"
                            class="sr-only"
                            @change="chooseSectionImage(index, $event)"
                        /><img
                            v-if="sectionPreviews[index] || section.image_url"
                            :src="
                                sectionPreviews[index] ??
                                section.image_url ??
                                ''
                            "
                            class="h-14 w-20 rounded object-cover"
                        />
                    </div>
                    <InputError
                        :message="form.errors[`sections.${index}.title`]"
                    />
                </div>
                <Button type="button" variant="outline" @click="addSection"
                    ><Plus class="size-4" /> Add section</Button
                >
            </section>
            <section
                v-if="editing"
                class="space-y-4 rounded-lg border bg-card p-5"
            >
                <div>
                    <h2 class="font-semibold">Gallery</h2>
                    <p class="text-sm text-muted-foreground">
                        Upload product photos (up to 5 MB each).
                    </p>
                </div>
                <label
                    for="gallery"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm"
                    ><ImagePlus class="size-4" /> Add photos</label
                ><input
                    id="gallery"
                    type="file"
                    accept="image/*"
                    multiple
                    class="sr-only"
                    @change="chooseGallery"
                />
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div
                        v-for="image in revision?.gallery.filter(
                            (item) =>
                                !form.remove_gallery_ids.includes(item.id),
                        )"
                        :key="image.id"
                        class="relative"
                    >
                        <img
                            :src="image.url"
                            :alt="image.alt_text ?? 'Product gallery image'"
                            class="aspect-square w-full rounded object-cover"
                        /><button
                            type="button"
                            class="absolute top-1 right-1 rounded bg-background p-1"
                            @click="removeExistingGallery(image.id)"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                    <div
                        v-for="(image, index) in galleryPreviews"
                        :key="image.url"
                        class="relative"
                    >
                        <img
                            :src="image.url"
                            :alt="image.file.name"
                            class="aspect-square w-full rounded object-cover"
                        /><button
                            type="button"
                            class="absolute top-1 right-1 rounded bg-background p-1"
                            @click="removeNewGallery(index)"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                </div>
            </section>
            <section class="space-y-3 rounded-lg border bg-card p-5">
                <h2 class="font-semibold">
                    Specification sheet <span class="text-destructive">*</span>
                </h2>
                <label
                    for="specification"
                    class="inline-flex cursor-pointer rounded-md border px-3 py-2 text-sm"
                    >Choose specification image</label
                ><input
                    id="specification"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    @change="chooseSpecification"
                /><img
                    v-if="specificationPreview"
                    :src="specificationPreview"
                    alt="Specification preview"
                    class="max-h-48 rounded border object-contain"
                />
                <InputError :message="form.errors.spec_image" />
            </section>
            <section
                class="grid gap-5 rounded-lg border bg-card p-5 md:grid-cols-2"
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
                        rows="2"
                        class="w-full rounded-md border bg-background px-3 py-2"
                    /><InputError :message="form.errors.meta_description" />
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label for="video-url" class="text-sm font-medium"
                        >YouTube video URL</label
                    ><input
                        id="video-url"
                        v-model="form.video_url"
                        type="url"
                        placeholder="https://www.youtube.com/watch?v=..."
                        class="w-full rounded-md border bg-background px-3 py-2"
                    />
                    <p class="text-xs text-muted-foreground">
                        Optional: shown in the “See the Machine in Action”
                        section.
                    </p>
                    <InputError :message="form.errors.video_url" />
                </div>
            </section>
            <div class="flex flex-wrap justify-end gap-3">
                <Button variant="outline" as-child
                    ><Link href="/cms/products">Cancel</Link></Button
                ><Button type="submit" :disabled="form.processing">{{
                    form.processing
                        ? 'Saving…'
                        : editing
                          ? 'Save draft'
                          : 'Create draft'
                }}</Button>
            </div>
        </form>
    </div>
</template>
