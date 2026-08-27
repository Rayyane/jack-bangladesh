<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class PublicSearchController extends Controller
{
    public function index(Request $request): Response
    {
        $term = trim((string) $request->string('q'));

        return Inertia::render('Public/Search', [
            'query' => $term,
            'categories' => $this->categories($term, 12),
            'products' => $this->products($term, 24),
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $term = trim((string) ($validated['q'] ?? ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['categories' => [], 'products' => []]);
        }

        return response()->json([
            'categories' => $this->categories($term, 5),
            'products' => $this->products($term, 6),
        ]);
    }

    private function categories(string $term, int $limit): array
    {
        if ($term === '') {
            return [];
        }

        $categories = Category::query()
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'materialized_path']);

        // Collect all ancestor IDs from every matched category's path,
        // then load them all in one query. Avoids N+1 per category.
        $ancestorIds = $categories
            ->flatMap(fn(Category $c) => $this->ancestorIdsFromPath($c->materialized_path))
            ->unique()
            ->values();

        // Keyed by ID so we can look up names cheaply below.
        $ancestorMap = Category::query()
            ->whereIn('id', $ancestorIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        return $categories
            ->map(fn(Category $category) => [
                'id'   => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                // e.g. "Lockstitch › Overlock" — the path ABOVE the match,
                // not including the matched category itself.
                'path' => $this->buildPathLabel($category->materialized_path, $ancestorMap),
                // Full label for typeahead display: "Overlock (Lockstitch › Overlock)"
                // Only added when ancestors exist, so root categories show just their name.
                'full_label' => $this->buildFullLabel($category->name, $category->materialized_path, $ancestorMap),
            ])
            ->all();
    }

    private function products(string $term, int $limit): array
    {
        if ($term === '') {
            return [];
        }

        return Product::query()
            ->join('product_revisions', 'products.published_revision_id', '=', 'product_revisions.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('product_revisions.name', 'like', '%' . $term . '%')
            ->orderBy('product_revisions.name')
            ->limit($limit)
            ->get([
                'products.id',
                'products.slug',
                'product_revisions.name',
                'categories.name as category_name',
                'categories.slug as category_slug',
            ])
            ->map(fn($product) => [
                'id'       => $product->id,
                'slug'     => $product->slug,
                'name'     => $product->name,
                'category' => $product->category_name
                    ? [
                        'name' => $product->category_name,
                        'slug' => $product->category_slug,
                    ]
                    : null,
            ])
            ->all();
    }

    /**
     * Parse a materialized_path string into an array of integer ancestor IDs.
     * e.g. "1/4/9" → [1, 4, 9]
     * Returns an empty array for root categories (null or empty path).
     */
    private function ancestorIdsFromPath(?string $path): array
    {
        if (empty($path)) {
            return [];
        }

        return array_map('intval', explode('/', $path));
    }

    /**
     * Build a human-readable ancestor trail from a materialized path.
     * e.g. path "1/4" with names {1: "Lockstitch", 4: "Overlock"}
     *      → "Lockstitch › Overlock"
     *
     * Returns an empty string for root categories (no ancestors).
     */
    private function buildPathLabel(?string $path, Collection $ancestorMap): string
    {
        $ids = $this->ancestorIdsFromPath($path);

        if (empty($ids)) {
            return '';
        }

        return collect($ids)
            ->map(fn(int $id) => $ancestorMap->get($id)?->name)
            ->filter()
            ->implode(' › ');
    }

    /**
     * Build the full label shown in typeahead suggestions and search results
     * when a path exists.
     *
     * Root category:  "Lockstitch"
     * Child category: "Overlock  ·  Lockstitch › Overlock"
     *                  ^name         ^breadcrumb trail
     */
    private function buildFullLabel(string $name, ?string $path, Collection $ancestorMap): string
    {
        $pathLabel = $this->buildPathLabel($path, $ancestorMap);

        if ($pathLabel === '') {
            return $name; // root — no disambiguation needed
        }

        return $name . '  ·  ' . $pathLabel . ' › ' . $name;
    }
}
