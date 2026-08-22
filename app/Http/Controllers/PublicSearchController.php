<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        return Category::query()
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'slug'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
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
            ->where('product_revisions.name', 'like', '%'.$term.'%')
            ->orderBy('product_revisions.name')
            ->limit($limit)
            ->get([
                'products.id',
                'products.slug',
                'product_revisions.name',
                'categories.name as category_name',
                'categories.slug as category_slug',
            ])
            ->map(fn ($product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'category' => $product->category_name
                    ? ['name' => $product->category_name, 'slug' => $product->category_slug]
                    : null,
            ])
            ->all();
    }
}
