<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSection;
use App\Models\SlugHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProductController extends Controller
{
    /**
     * Product catalog listing page.
     * Filterable by category (including all descendants via materialized path).
     * Only published products are shown.
     */
    public function index(Request $request): Response
    {
        $query = Product::with([
                'category',
                'publishedRevision',
                'publishedRevision.gallery' => fn ($q) => $q->where('is_primary', true)->limit(1),
            ])
            ->whereNotNull('published_revision_id');
 
        // Filter by category — includes the selected category AND all its
        // descendants, so filtering by "Electronics" also shows "Phones" products.
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
 
            if ($category) {
                $descendantIds = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $descendantIds);
            }
        }
 
        $products = $query->paginate(24)->withQueryString();
 
        return Inertia::render('Public/Products/Index', [
            'products' => $products->through(fn (Product $product) => [
                'id'       => $product->id,
                'slug'     => $product->slug,
                'name'     => $product->publishedRevision->name,
                'category' => $product->category
                    ? ['name' => $product->category->name, 'slug' => $product->category->slug]
                    : null,
                'image'    => $product->publishedRevision->card_image_path
                    ? \Illuminate\Support\Facades\Storage::url($product->publishedRevision->card_image_path)
                    : ($product->publishedRevision->primary_image_path
                        ? \Illuminate\Support\Facades\Storage::url($product->publishedRevision->primary_image_path)
                        : $product->publishedRevision->gallery->first()?->url),
            ]),
 
            // Full category tree for the sidebar/filter panel.
            // Retrieved from cache — same source as the megamenu.
            'category_tree'     => Category::getTree(),
            'active_category'   => $request->category,
        ]);
    }
 
    /**
     * Individual product page.
     *
     * Handles two cases:
     * 1. Current slug — serve the product normally.
     * 2. Old/retired slug — 301 redirect to the current slug.
     * 3. Unknown slug — 404.
     *
     * Only published products are visible to the public.
     */
    public function show(string $slug): Response|\Illuminate\Http\RedirectResponse
    {
        // First try to find the product directly by its current slug.
        $product = Product::where('slug', $slug)
            ->whereNotNull('published_revision_id')
            ->first();
 
        if ($product) {
            return $this->renderProduct($product);
        }
 
        // Slug not found on the products table — check slug history.
        $history = SlugHistory::resolve($slug);
 
        if (! $history) {
            abort(404);
        }
 
        // If the history entry is current, the product just isn't published yet.
        if ($history->is_current) {
            abort(404);
        }
 
        // Old slug — resolve the owning model and find its current slug.
        $owner = $history->sluggable;
 
        if (! $owner || ! ($owner instanceof Product) || ! $owner->isPublished()) {
            abort(404);
        }
 
        $currentSlug = SlugHistory::currentSlugFor($owner);
 
        if (! $currentSlug) {
            abort(404);
        }
 
        // 301 redirect to the current slug — permanent, so search engines
        // update their index.
        return redirect()->route('products.show', $currentSlug, 301);
    }
 
    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------
 
    /**
     * Render the product page with all its published data.
     * Loads the published revision, sections, gallery, and spec sheet.
     */
    private function renderProduct(Product $product): Response
    {
        $revision = $product->publishedRevision;
 
        $revision->load(['sections', 'gallery', 'specifications']);
 
        return Inertia::render('Public/Products/Show', [
            'product' => [
                'id'   => $product->id,
                'slug' => $product->slug,
                'category' => $product->category
                    ? [
                        'name'              => $product->category->name,
                        'slug'              => $product->category->slug,
                        // Ancestors for breadcrumb: Home > Category > Subcategory > Product
                        'breadcrumb'        => $product->category->ancestors
                            ->map(fn ($a) => ['name' => $a->name, 'slug' => $a->slug])
                            ->push(['name' => $product->category->name, 'slug' => $product->category->slug]),
                    ]
                    : null,
            ],
            'revision' => [
                'name'             => $revision->name,
                'description'      => $revision->description,
                'price'            => $revision->price,
                'meta_title'       => $revision->meta_title,
                'meta_description' => $revision->meta_description,
                'video_url'        => $revision->video_url,
                'primary_image_url' => $revision->primary_image_path
                    ? \Illuminate\Support\Facades\Storage::url($revision->primary_image_path)
                    : null,
                'sections'         => $revision->sections->map(fn (ProductSection $s) => [
                    'id'          => $s->id,
                    'title'       => $s->title,
                    'description' => $s->description,
                    'image_url'   => $s->image_path
                        ? \Illuminate\Support\Facades\Storage::url($s->image_path)
                        : null,
                    'image_alt'   => $s->image_alt,
                ]),
                'gallery'        => $revision->gallery->map(fn ($m) => [
                    'url'      => $m->url,
                    'alt_text' => $m->alt_text,
                ]),
                'specifications' => $revision->specifications()->first()
                    ? ['url' => $revision->specifications()->first()->url]
                    : null,
            ],
        ]);
    }
}
