<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\ProductSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * List all products with their category, published status,
     * and any active in-progress revision.
     * Filterable by category and status for large catalogs.
     */
    public function index(Request $request): Response
    {
        $query = Product::with([
            'category',
            'publishedRevision',
            'revisions' => fn ($q) => $q
                ->whereNot('status', ProductRevision::STATUS_PUBLISHED)
                ->latest()
                ->limit(1),
        ]);
 
        // Filter by category if provided.
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
 
        // Filter to only uncategorised products (no category assigned,
        // or category was deleted and category_id was manually nulled)..
        if ($request->boolean('uncategorised')) {
            $query->whereNull('category_id');
        }
 
        // Filter by active revision status (draft, pending_review etc).
        if ($request->filled('status')) {
            $query->whereHas('revisions', fn ($q) => $q
                ->where('status', $request->status)
                ->whereNot('status', ProductRevision::STATUS_PUBLISHED)
            );
        }
 
        $products = $query->latest()->paginate(30)->withQueryString();
 
        return Inertia::render('Cms/Products/Index', [
            'products'   => $products->through(fn (Product $product) => [
                'id'              => $product->id,
                'slug'            => $product->slug,
                'is_featured'     => $product->is_featured,
                'is_published'    => $product->isPublished(),
                'is_uncategorised' => $product->isUncategorised(),
                'category'        => $product->category
                    ? ['id' => $product->category->id, 'name' => $product->category->name]
                    : null,
                'name'            => $product->publishedRevision?->name
                    ?? $product->revisions->first()?->name
                    ?? '(Untitled)',
                'active_revision' => $product->revisions->first()
                    ? [
                        'id'     => $product->revisions->first()->id,
                        'status' => $product->revisions->first()->status,
                    ]
                    : null,
            ]),
 
            // Pass filter state back so Vue can populate filter controls.
            'filters' => $request->only(['category_id', 'uncategorised', 'status']),
 
            // Flat category list for the filter dropdown.
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }
 
    /**
     * Show the create product form.
     */
    public function create(): Response
    {
        return Inertia::render('Cms/Products/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }
 
    /**
     * Store a new product with its first draft revision.
     *
     * Creating a product always produces:
     *   1. A Product record (identity, slug, category, is_featured)
     *   2. A ProductRevision at status=draft (content fields)
     *   3. A SlugHistory entry recording the initial slug
     *
     * Any sections added on the create form are also stored here.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'              => ['nullable', 'exists:categories,id'],
            'is_featured'              => ['boolean'],
            'name'                     => ['required', 'string', 'max:255'],
            'description'              => ['required', 'string'],
            'meta_title'               => ['nullable', 'string', 'max:255'],
            'meta_description'         => ['nullable', 'string', 'max:500'],
 
            // Sections submitted on initial create.
            'sections'                 => ['nullable', 'array'],
            'sections.*.title'         => ['required', 'string', 'max:255'],
            'sections.*.description'   => ['nullable', 'string'],
            'sections.*.sort_order'    => ['integer', 'min:0'],
        ]);
 
        $slug = $this->generateUniqueSlug($validated['name']);
 
        DB::transaction(function () use ($validated, $slug) {
            // 1. Create the product entity.
            $product = Product::create([
                'category_id' => $validated['category_id'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'slug'        => $slug,
            ]);
 
            // 2. Record the initial slug.
            // SlugHistory::record($product, $slug);
            // not needed since booted method inside model handles it
 
            // 3. Create the first draft revision.
            $revision = $product->revisions()->create([
                'status'           => ProductRevision::STATUS_DRAFT,
                'name'             => $validated['name'],
                'description'      => $validated['description'],
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);
 
            // 4. Store any sections submitted on the create form.
            if (! empty($validated['sections'])) {
                foreach ($validated['sections'] as $index => $section) {
                    $revision->sections()->create([
                        'title'       => $section['title'],
                        'description' => $section['description'] ?? null,
                        'sort_order'  => $section['sort_order'] ?? $index,
                    ]);
                }
            }
 
            return $product;
        });
 
        return redirect()
            ->route('cms.products.index')
            ->with('success', 'Product created as draft.');
    }
 
    /**
     * Load the product editor for an existing product.
     *
     * Like PageController@edit: always loads a draft, creating one
     * if none exists (seeded from the published revision).
     */
    public function edit(Product $product): Response
    {
        $draft = $product->revisions()
            ->where('status', ProductRevision::STATUS_DRAFT)
            ->latest()
            ->first();
 
        if (! $draft) {
            DB::transaction(function () use ($product, &$draft) {
                $draft = $product->createDraft();
                $product->copySectionsToDraft($draft);
            });
        }
 
        $draft->load(['sections', 'gallery', 'specifications']);
 
        return Inertia::render('Cms/Products/Edit', [
            'product' => [
                'id'           => $product->id,
                'slug'         => $product->slug,
                'is_featured'  => $product->is_featured,
                'is_published' => $product->isPublished(),
                'category_id'  => $product->category_id,
            ],
            'revision' => [
                'id'               => $draft->id,
                'status'           => $draft->status,
                'name'             => $draft->name,
                'description'      => $draft->description,
                'meta_title'       => $draft->meta_title,
                'meta_description' => $draft->meta_description,
                'sections'         => $draft->sections->map(fn (ProductSection $s) => [
                    'id'          => $s->id,
                    'title'       => $s->title,
                    'description' => $s->description,
                    'image_path'  => $s->image_path,
                    'image_url'   => $s->image_path
                        ? Storage::url($s->image_path)
                        : null,
                    'image_alt'   => $s->image_alt,
                    'sort_order'  => $s->sort_order,
                ]),
                'gallery'        => $draft->gallery->map(fn (Media $m) => [
                    'id'       => $m->id,
                    'url'      => $m->url,
                    'alt_text' => $m->alt_text,
                    'size'     => $m->human_size,
                ]),
                'specifications' => $draft->specifications()->first()
                    ? [
                        'id'  => $draft->specifications()->first()->id,
                        'url' => $draft->specifications()->first()->url,
                    ]
                    : null,
            ],
            'published_revision' => $product->publishedRevision
                ? ['name' => $product->publishedRevision->name]
                : null,
            'categories' => $this->categoryOptions(),
        ]);
    }
 
    /**
     * Save changes to an existing product draft.
     *
     * Handles:
     * - Product-level fields (category, is_featured, slug regeneration)
     * - Revision content fields (name, description, meta)
     * - Section creates, updates, and deletes in one request
     * - Gallery and specification image uploads
     */
    public function update(Request $request, Product $product, ProductRevision $revision): RedirectResponse
    {
        abort_if($revision->product_id !== $product->id, 403);
        abort_if($revision->status !== ProductRevision::STATUS_DRAFT, 403, 'Only draft revisions can be edited.');
 
        $validated = $request->validate([
            // Product-level
            'category_id'  => ['nullable', 'exists:categories,id'],
            'is_featured'  => ['boolean'],
 
            // Revision content
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
 
            // Sections — full replacement on each save.
            // The Vue form sends the complete current state of all sections.
            'sections' => ['nullable', 'array'],
            'sections.*.id' => [
                'nullable',
                Rule::exists('product_sections', 'id')->where(
                    'product_revision_id', $revision->id
                ),
            ],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.description' => ['nullable', 'string'],
            'sections.*.sort_order' => ['integer', 'min:0'],
 
            // Section image uploads (keyed by section index).
            'section_images' => ['nullable', 'array'],
            'section_images.*' => ['file', 'image', 'max:5120'],
 
            // Gallery images (multiple).
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['file', 'image', 'max:5120'],
 
            // Spec sheet image (single).
            'spec_image' => ['nullable', 'file', 'image', 'max:10240'],
 
            // IDs of gallery images the editor wants to remove.
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['exists:media,id'],
        ]);
 
        DB::transaction(function () use ($validated, $request, $product, $revision) {
 
            // --- Product-level fields ---
 
            $productData = [
                'category_id' => $validated['category_id'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
            ];
 
            // Regenerate slug if the name changed, and record the change.
            if ($product->revisions()->published()->latest()->first()?->name !== $validated['name']) {
                $newSlug = $this->generateUniqueSlug($validated['name'], excludeId: $product->id);
                if ($newSlug !== $product->slug) {
                    $productData['slug'] = $newSlug;
                    // SlugHistory::record($product, $newSlug);
                    // SlugHistory is recorded automatically by Product::booted()
                }
            }
 
            $product->update($productData);
 
            // --- Revision content fields ---
 
            $revision->update([
                'name'             => $validated['name'],
                'description'      => $validated['description'] ?? null,
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);
 
            // --- Sections ---
            // Sync the incoming sections against what's in the DB.
            // New sections (no id) are created; existing ones are updated;
            // sections missing from the payload are deleted.
 
            $incomingSectionIds = collect($validated['sections'] ?? [])
                ->pluck('id')
                ->filter()
                ->all();
 
            // Delete sections not present in the incoming payload.
            $revision->sections()
                ->whereNotIn('id', $incomingSectionIds)
                ->delete();
 
            foreach ($validated['sections'] ?? [] as $index => $sectionData) {
                $section = isset($sectionData['id'])
                    ? ProductSection::find($sectionData['id'])
                    : null;
 
                $sectionPayload = [
                    'title'       => $sectionData['title'],
                    'description' => $sectionData['description'] ?? null,
                    'sort_order'  => $sectionData['sort_order'] ?? $index,
                ];
 
                // Handle section image upload for this index.
                if ($request->hasFile("section_images.{$index}")) {
                    $path = $request->file("section_images.{$index}")
                        ->store('products/sections', 'public');
 
                    // Delete old image file if replacing.
                    if ($section?->image_path) {
                        Storage::disk('public')->delete($section->image_path);
                    }
 
                    $sectionPayload['image_path'] = $path;
                    $sectionPayload['image_alt']  = $sectionData['title'];
                }
 
                if ($section) {
                    $section->update($sectionPayload);
                } else {
                    $revision->sections()->create($sectionPayload);
                }
            }
 
            // --- Gallery images ---
 
            // Remove any gallery images the editor deleted.
            if (! empty($validated['remove_gallery_ids'])) {
                Media::whereIn('id', $validated['remove_gallery_ids'])
                    ->where('mediable_type', ProductRevision::class)
                    ->where('mediable_id', $revision->id)
                    ->get()
                    ->each->delete(); // triggers file deletion via Media::booted()
            }
 
            // Upload new gallery images.
            foreach ($request->file('gallery_images', []) as $file) {
                $path = $file->store('products/gallery', 'public');
 
                $revision->media()->create([
                    'collection' => Media::COLLECTION_GALLERY,
                    'path'       => $path,
                    'disk'       => 'public',
                    'mime_type'  => $file->getMimeType(),
                    'size'       => $file->getSize(),
                    'alt_text'   => $validated['name'],
                ]);
            }
 
            // --- Spec sheet image ---
 
            if ($request->hasFile('spec_image')) {
                // Replace the existing spec image if one exists.
                $revision->specifications()->get()->each->delete();
 
                $file = $request->file('spec_image');
                $path = $file->store('products/specifications', 'public');
 
                $revision->media()->create([
                    'collection' => Media::COLLECTION_SPECIFICATIONS,
                    'path'       => $path,
                    'disk'       => 'public',
                    'mime_type'  => $file->getMimeType(),
                    'size'       => $file->getSize(),
                    'alt_text'   => $validated['name'] . ' specifications',
                ]);
            }
        });
 
        return back()->with('success', 'Draft saved.');
    }
 
    /**
     * Soft-delete a product and all its revisions.
     * Media files are cleaned up via the Media model's deleted event.
     * Super Admin only (enforced by route middleware).
     */
    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            // Delete all media across all revisions before soft-deleting.
            foreach ($product->revisions as $revision) {
                $revision->media()->get()->each->delete();
            }
 
            $product->delete();
        });
 
        return redirect()
            ->route('cms.products.index')
            ->with('success', 'Product deleted.');
    }
 
    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------
 
    /**
     * Generate a unique slug from a product name.
     * Appends a numeric suffix if the base slug already exists.
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base  = Str::slug($name);
        $slug  = $base;
        $count = 1;
 
        $query = Product::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
 
        while ($query->clone()->exists()) {
            $slug = $base . '-' . $count++;
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
 
        return $slug;
    }
 
    /**
     * Flat category list for the category picker dropdown.
     */
    private function categoryOptions(): \Illuminate\Support\Collection
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }
}
