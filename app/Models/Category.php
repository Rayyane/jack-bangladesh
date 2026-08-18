<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['parent_id', 'name', 'slug', 'image_path', 'sort_order', 'is_featured', 'show_in_nav'])]
class Category extends Model
{
    use LogsActivity, SoftDeletes;

    public const CACHE_KEY = 'categories.tree';
    public const CACHE_TTL = 60 * 60 * 24;
    public const NAV_PRODUCT_LIMIT = 10;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'show_in_nav' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'parent_id', 
                'name', 
                'slug', 
                'sort_order', 
                'is_featured', 
                'show_in_nav'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('category');
    }

    /** @return BelongsTo<Category, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Category, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /** @return HasMany<Category, $this> */
    public function recursiveChildren(): HasMany
    {
        return $this->children()->with('recursiveChildren');
    }

    /** @return HasMany<Category, $this> */
    public function recursiveChildrenWithProductCount(): HasMany
    {
        return $this->children()
            ->withCount('products')
            ->with('recursiveChildrenWithProductCount');
    }

    /**
     * Recursive tree tailored for the public navigation. It additionally
     * eager-loads the limited product list needed by leaf dropdowns.
     *
     * @return HasMany<Category, $this>
     */
    public function recursiveNavChildren(): HasMany
    {
        return $this->children()->with([
            'recursiveNavChildren',
            'leafProducts.publishedRevision:id,product_id,name',
        ]);
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function leafProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id')
            ->whereNotNull('published_revision_id')
            ->orderBy('id')
            ->limit(self::NAV_PRODUCT_LIMIT + 1);
    }

    /** @return Collection<int, Category> */
    public function getAncestorsAttribute(): Collection
    {
        $ids = array_values(array_filter(
            explode('/', (string) $this->materialized_path),
            static fn (string $id): bool => ctype_digit($id),
        ));

        if ($ids === []) {
            return new Collection;
        }

        $ancestors = static::query()->whereKey($ids)->get()->keyBy('id');

        return (new Collection($ids))
            ->map(static fn (string $id): ?Category => $ancestors->get((int) $id))
            ->filter()
            ->values();
    }

    /** @return Builder<Category> */
    public function descendants(): Builder
    {
        return static::query()->where(function (Builder $query): void {
            $query->where('materialized_path', (string) $this->id)
                ->orWhere('materialized_path', 'like', $this->id.'/%');
        });
    }

    public function rebuildPath(): void
    {
        if ($this->parent_id === null) {
            $this->materialized_path = null;

            return;
        }

        $parent = static::query()->find($this->parent_id);

        if ($parent === null) {
            throw ValidationException::withMessages([
                'parent_id' => 'The selected parent category does not exist or has been deleted.',
            ]);
        }

        $this->materialized_path = $parent->materialized_path
            ? $parent->materialized_path.'/'.$parent->id
            : (string) $parent->id;
    }

    public function rebuildDescendantPaths(): void
    {
        $this->children()->eachById(function (Category $child): void {
            $child->rebuildPath();
            $child->saveQuietly();
            $child->rebuildDescendantPaths();
        });
    }

    /** @return array<int, array{id: int, name: string, slug: string, children: array, products: array, has_more: bool, product_count_label: ?string}> */
    public static function getTree(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, static fn (): array => self::buildTree());
    }

    public static function clearTreeCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<int, array{id: int, name: string, slug: string, children: array}> */
    protected static function buildTree(): array
    {
        $roots = static::with([
            'recursiveNavChildren',
            'leafProducts.publishedRevision:id,product_id,name',
        ])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return static::toTreeArray($roots);
    }

    /** @param Collection<int, Category> $categories
     *  @return array<int, array{id: int, name: string, slug: string, children: array, products: array, has_more: bool, product_count_label: ?string}> */
    protected static function toTreeArray(Collection $categories): array
    {
        return $categories
            // Nav visibility applies only to root (parent) categories. Child
            // categories are always displayed within their parent's dropdown.
            ->filter(static fn (Category $category): bool => static::isVisibleInNav($category))
            ->map(static function (Category $category): array {
                $visibleChildren = $category->recursiveNavChildren
                    ->filter(static fn (Category $child): bool => static::isVisibleInNav($child));
                $isLeaf = $visibleChildren->isEmpty();
                $products = [];
                $hasMore = false;
                $productCountLabel = null;

                if ($isLeaf) {
                    $fetchedProducts = $category->leafProducts;
                    $hasMore = $fetchedProducts->count() > self::NAV_PRODUCT_LIMIT;

                    $products = $fetchedProducts
                        ->take(self::NAV_PRODUCT_LIMIT)
                        ->map(static fn (Product $product): array => [
                            'name' => $product->publishedRevision?->name,
                            'slug' => $product->slug,
                        ])
                        ->filter(static fn (array $product): bool => $product['name'] !== null)
                        ->values()
                        ->all();

                    $productCountLabel = $hasMore
                        ? self::NAV_PRODUCT_LIMIT.'+'
                        : (string) count($products);
                }

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'children' => static::toTreeArray($visibleChildren),
                    'products' => $products,
                    'has_more' => $hasMore,
                    'product_count_label' => $productCountLabel,
                ];
            })
            ->values()
            ->all();
    }

    private static function isVisibleInNav(Category $category): bool
    {
        return $category->parent_id !== null || $category->show_in_nav;
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (! $category->isDirty('parent_id') && $category->exists) {
                return;
            }

            $category->ensureValidParent();
            $category->rebuildPath();
        });

        static::saved(function (Category $category): void {
            if ($category->wasChanged('parent_id')) {
                $category->rebuildDescendantPaths();
            }

            self::clearTreeCache();
        });

        static::forceDeleting(function (Category $category): void {
            $category->children()->withTrashed()->eachById(function (Category $child): void {
                $child->parent_id = null;
                $child->save();
            });
        });

        static::deleted(static function (): void {
            self::clearTreeCache();
        });

        static::restored(static function (): void {
            self::clearTreeCache();
        });
    }

    private function ensureValidParent(): void
    {
        if ($this->parent_id === null) {
            return;
        }

        $parent = static::withTrashed()->find($this->parent_id);

        if ($parent === null || $parent->trashed()) {
            throw ValidationException::withMessages(['parent_id' => 'The selected parent category is unavailable.']);
        }

        if ($this->exists && ($parent->is($this) || in_array((string) $this->id, explode('/', (string) $parent->materialized_path), true))) {
            throw ValidationException::withMessages(['parent_id' => 'A category cannot be its own descendant.']);
        }
    }
}
