<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    /**
     * Serve a public-facing static page by its slug.
     *
     * The route model binding resolves Page by slug.
     * Only published pages are served — unpublished ones return 404.
     *
     * Extra data is loaded per template_key so each page type gets
     * exactly what its Vue component needs and nothing more.
     */
    public function show(string $slug): Response
    {
        // Resolve the page by slug and eager-load the published revision
        // with its gallery media.
        $page = Page::with(['publishedRevision.gallery'])
            ->where('slug', $slug)
            ->firstOrFail();
 
        // If nothing is published yet, treat it as not found.
        abort_if(! $page->isPublished(), 404);
 
        $revision = $page->publishedRevision;
 
        // Base data every page type receives.
        $pageData = [
            'template_key'     => $page->template_key,
            'content'          => $revision->content,
            'meta_title'       => $revision->meta_title,
            'meta_description' => $revision->meta_description,
        ];
 
        // Load template-specific extra data.
        $extraData = match ($page->template_key) {
            'home'    => $this->homepageData(),
            'about'   => [],   // No extras — content JSON is sufficient.
            'contact' => [],   // No extras — content JSON is sufficient.
            default   => [],
        };
 
        return Inertia::render('Public/Page', array_merge($pageData, $extraData));
    }
 
    // -------------------------------------------------------------------------
    // Template-specific data loaders
    // -------------------------------------------------------------------------
 
    /**
     * Extra data for the homepage template.
     *
     * - Featured categories: pulled from cache (same cache the megamenu uses,
     *   filtered to is_featured = true).
     * - Featured products: published products marked is_featured, with their
     *   published revision's name and primary gallery image.
     */
    private function homepageData(): array
    {
        $featuredCategories = Cache::remember(
            'categories.featured',
            60 * 60 * 24,
            fn () => Category::where('is_featured', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug'])
                ->toArray()
        );
 
        $featuredProducts = Product::with([
                'publishedRevision',
                'publishedRevision.gallery' => fn ($q) => $q->where('is_primary', true)->limit(1),
            ])
            ->where('is_featured', true)
            ->whereNotNull('published_revision_id')
            ->get()
            ->map(fn (Product $product) => [
                'id'    => $product->id,
                'slug'  => $product->slug,
                'name'  => $product->publishedRevision->name,
                'image' => $product->publishedRevision->gallery->first()?->url,
            ]);
 
        return [
            'featured_categories' => $featuredCategories,
            'featured_products'   => $featuredProducts,
        ];
    }
}
