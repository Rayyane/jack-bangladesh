<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display the full category tree for management.
     * Intentionally bypasses the cache here — admin needs the live DB state,
     * not a potentially stale cached version.
     */
    public function index(): Response
    {
        $categories = Category::withCount('products')
            ->with('recursiveChildrenWithProductCount')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
 
        return Inertia::render('Cms/Categories/Index', [
            'categories' => $this->managementTree($categories),
        ]);
    }

    /**
     * Show the form to create a new category.
     * Passes a flat list of existing categories for the parent picker.
     */
    public function create(): Response
    {
        return Inertia::render('Cms/Categories/Create', [
            'parentOptions' => $this->flatCategoryList(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isFeatured = $request->boolean('is_featured');

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255', 'unique:categories'],
            'parent_id'  => ['nullable', 'exists:categories,id'],
            'sort_order' => ['integer', 'min:0'],
            'image'      => [Rule::requiredIf($isFeatured), 'nullable', 'image', 'max:5120'],
            'is_featured' => ['boolean'],
            'show_in_nav' => ['boolean'],
        ], [
            'image.required' => 'A featured category must have an image.',
        ]);
 
        // Auto-generate slug from name. The Category model's booted()
        // method will compute the correct materialized_path on saving.
        $validated['slug'] = Str::slug($validated['name']);
 
        // Ensure slug uniqueness — append a suffix if it collides.
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }
 
        $validated['image_path'] = isset($validated['image'])
            ? $validated['image']->store('categories', 'public')
            : null;
        unset($validated['image']);
        $validated['is_featured'] = $isFeatured;
        // Only root categories appear in the navbar's first level.
        $validated['show_in_nav'] = $validated['parent_id'] === null
            ? ($validated['show_in_nav'] ?? false)
            : false;
        $validated['sort_order'] = $validated['sort_order']
            ?? $this->nextSortOrder($validated['parent_id']);

        Category::create($validated);
 
        // Tree cache is invalidated automatically by the Category model's
        // saved event — no manual cache clear needed here.
 
        return redirect()
            ->route('cms.categories.index')
            ->with('success', 'Category created.');
    }

    /**
     * Show the edit form for an existing category.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Cms/Categories/Edit', [
            'category'      => [
                'id' => $category->id,
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'sort_order' => $category->sort_order,
                'is_featured' => $category->is_featured,
                'show_in_nav' => $category->show_in_nav,
                'image_path' => $category->image_path,
                'image_url' => $category->image_path ? Storage::url($category->image_path) : null,
            ],
            // Exclude the category itself and its descendants from the
            // parent picker — you can't reparent a category under itself.
            'parentOptions' => $this->flatCategoryList(exclude: $category),
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $isFeatured = $request->boolean('is_featured');

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
            'sort_order'  => ['integer', 'min:0'],
            'image'       => [Rule::requiredIf($isFeatured && ! $category->image_path), 'nullable', 'image', 'max:5120'],
            'is_featured' => ['boolean'],
            'show_in_nav' => ['boolean'],
        ], [
            'image.required' => 'A featured category must have an image.',
        ]);
 
        // Prevent circular reparenting — a category cannot be made a
        // descendant of itself.
        if (! is_null($validated['parent_id'])) {
            $newParent = Category::find($validated['parent_id']);
            if ($this->wouldCreateCircularReference($category, $newParent)) {
                return back()->withErrors([
                    'parent_id' => 'A category cannot be placed under one of its own descendants.',
                ]);
            }
        }
 
        // Only regenerate the slug if the name has changed.
        if ($category->name !== $validated['name']) {
            $newSlug = Str::slug($validated['name']);
            $originalSlug = $newSlug;
            $count = 1;
            while (Category::where('slug', $newSlug)->where('id', '!=', $category->id)->exists()) {
                $newSlug = $originalSlug . '-' . $count++;
            }
            $validated['slug'] = $newSlug;
        }
 
        $oldImagePath = $category->image_path;

        if (isset($validated['image'])) {
            $newImagePath = $validated['image']->store('categories', 'public');
            unset($validated['image']);
            $validated['image_path'] = $newImagePath;
        }

        $validated['is_featured'] = $isFeatured;
        $validated['show_in_nav'] = $validated['parent_id'] === null
            ? ($validated['show_in_nav'] ?? false)
            : false;

        // materialized_path and descendant paths are rebuilt automatically
        // by the Category model's saving/saved events when parent_id changes.
        $category->update($validated);

        if (isset($validated['image_path']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }
 
        return redirect()
            ->route('cms.categories.index')
            ->with('success', 'Category updated.');
    }

    /**
     * Soft-delete a category.
     *
     * Products whose category_id pointed here will have category_id set to
     * null (via the nullOnDelete FK). The CMS flags these as "uncategorised"
     * so they can be reassigned.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Prevent deleting a category that still has active children.
        // Force the admin to reassign or delete children first.
        if ($category->children()->exists()) {
            return back()->withErrors([
                'category' => 'This category has subcategories. Reassign or delete them first.',
            ]);
        }
 
        $imagePath = $category->image_path;

        DB::transaction(function () use ($category) {
            // Manually null out category_id on affected products
            // before soft-deleting, since nullOnDelete() won't fire.
            Product::where('category_id', $category->id)
                ->update(['category_id' => null]);

            $category->forceDelete();
        });

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
 
        return redirect()
            ->route('cms.categories.index')
            ->with('success', 'Category deleted.');
    }

    /**
     * Display only the parent categories that are visible in the navbar.
     */
    public function sort(): Response
    {
        return Inertia::render('Cms/Categories/Sort', [
            'categories' => Category::query()
                ->whereNull('parent_id')
                ->where('show_in_nav', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'sort_order']),
        ]);
    }

    /**
     * Update sort order for multiple categories at once.
     * Called when the admin drags to reorder in the UI.
     *
     * Expects: [{ id: 1, sort_order: 0 }, { id: 2, sort_order: 1 }, ...]
     */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'categories'             => ['required', 'array'],
            'categories.*.id'        => ['required', 'exists:categories,id'],
            'categories.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);
 
        $rootIds = collect($validated['categories'])->pluck('id');
        $rootCount = Category::query()
            ->whereNull('parent_id')
            ->where('show_in_nav', true)
            ->whereIn('id', $rootIds)
            ->count();

        if ($rootCount !== $rootIds->count()) {
            return back()->withErrors([
                'categories' => 'Only parent categories shown in the navigation can be reordered.',
            ]);
        }

        DB::transaction(function () use ($rootIds): void {
            $rootIds->values()->each(function (int $id, int $sortOrder): void {
                Category::whereKey($id)->update(['sort_order' => $sortOrder]);
            });
        });
 
        // Clear the tree cache once after all updates, not once per row.
        Category::clearTreeCache();
 
        return back()->with('success', 'Order saved.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------
 
    /**
     * Returns a flat, indented list of all categories for use in
     * <select> dropdowns / parent pickers in the Vue forms.
     *
     * Shape: [{ id, name, depth, label }, ...]
     * where label = "— — Child Name" reflecting nesting depth.
     */
    private function flatCategoryList(?Category $exclude = null): array
    {
        $roots = Category::with('recursiveChildren')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
 
        $flat = [];
        $this->flattenTree($roots, $flat, 0, $exclude);
 
        return $flat;
    }
 
    private function flattenTree(
        \Illuminate\Database\Eloquent\Collection $categories,
        array &$flat,
        int $depth,
        ?Category $exclude
    ): void {
        foreach ($categories as $category) {
            // Skip the excluded category and its descendants entirely.
            if ($exclude && (
                $category->id === $exclude->id ||
                str_contains($category->materialized_path ?? '', '/' . $exclude->id . '/') ||
                str_starts_with($category->materialized_path ?? '', $exclude->id . '/')
            )) {
                continue;
            }
 
            $flat[] = [
                'id'    => $category->id,
                'name'  => $category->name,
                'depth' => $depth,
                'label' => str_repeat('— ', $depth) . $category->name,
            ];
 
            $this->flattenTree($category->recursiveChildren, $flat, $depth + 1, $exclude);
        }
    }
 
    /**
     * Guard against circular reparenting.
     * Returns true if making $newParent the parent of $category
     * would place $category under one of its own descendants.
     */
    private function wouldCreateCircularReference(Category $category, Category $newParent): bool
    {
        // If the new parent's path contains this category's ID, it's a descendant.
        $path = $newParent->materialized_path ?? '';
 
        return $newParent->id === $category->id
            || str_contains($path, '/' . $category->id . '/')
            || str_starts_with($path, $category->id . '/')
            || str_ends_with($path, '/' . $category->id);
    }

    private function nextSortOrder(?int $parentId): int
    {
        return (int) Category::query()
            ->where('parent_id', $parentId)
            ->max('sort_order') + 1;
    }

    /**
     * Shape the recursive Eloquent relationship into the `children` key
     * consumed by the CMS tree component.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, Category> $categories
     * @return array<int, array{id: int, name: string, image_path: ?string, is_featured: bool, show_in_nav: bool, product_count: int, children: array}>
     */
    private function managementTree(\Illuminate\Database\Eloquent\Collection $categories): array
    {
        return $categories->map(fn (Category $category): array => [
            'id' => $category->id,
            'name' => $category->name,
            'image_path' => $category->image_path,
            'is_featured' => $category->is_featured,
            'show_in_nav' => $category->show_in_nav,
            'product_count' => $category->products_count,
            'children' => $this->managementTree($category->recursiveChildrenWithProductCount),
        ])->all();
    }
}
