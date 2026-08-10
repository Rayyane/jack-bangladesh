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
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['parent_id', 'name', 'slug', 'materialized_path', 'sort_order', 'show_in_nav'])]

class Category extends Model
{
    use SoftDeletes, LogsActivity;

    protected function casts() {
        return [
            'sort_order' => 'integer',
            'show_in_nav' => 'boolean'
        ];
    }

    // -------------------------------------------------------------------------
    // Activity log (spatie/laravel-activitylog)
    // Logs create, update, delete events with before/after diffs automatically.
    // Accessible via: $category->activities
    // -------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['parent_id', 'name', 'slug', 'sort_order', 'show_in_nav'])
            ->logOnlyDirty()        // only log fields that actually changed
            ->dontSubmitEmptyLogs() // skip logging if nothing changed
            ->useLogName('category');
    }

    /**
     * The parent category (null for root-level categories).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Recursively eager-loadable children for building the full nested tree.
     * Usage: Category::with('recursiveChildren')->whereNull('parent_id')->get()
     */
    public function recursiveChildren()
    {
        return $this->children()->with('recursiveChildren');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getAncestorsAttribute()
    {
        if (empty($this->materialized_path)) {
            return collect();
        }
 
        $ids = explode('/', $this->materialized_path);
 
        // Preserve the root → parent ordering from the path.
        return static::whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->get();
    }

    public function descendants(): Builder
    {
        // Direct children store the parent ID as the full path.
        // Deeper descendants store the ancestor chain starting with this ID.
        return static::where('materialized_path', (string) $this->id)
            ->orWhere('materialized_path', 'like', $this->id . '/%');
    }

    public function rebuildPath(): void
    {
        if (is_null($this->parent_id)) {
            $this->materialized_path = null;
        } else {
            $parent = static::find($this->parent_id);
 
            $this->materialized_path = $parent->materialized_path
                ? $parent->materialized_path . '/' . $parent->id
                : (string) $parent->id;
        }
    }

    public function rebuildDescendantPaths(): void
    {
        foreach ($this->children as $child) {
            $child->rebuildPath();
            $child->saveQuietly(); // saveQuietly skips model events to avoid loops
            $child->rebuildDescendantPaths();
        }
    }

    // -------------------------------------------------------------------------
    // Megamenu tree cache
    // Stored as a nested array rather than models to keep serialization simple.
    // The full tree is cached once and invalidated on any category change.
    // -------------------------------------------------------------------------
 
    const CACHE_KEY = 'categories.tree';
    const CACHE_TTL = 60 * 60 * 24; // 24 hours — invalidated on change anyway
 
    /**                                                                                                                ?                                                                                                                                                                                                                                                                                                                                                                                                                                                        
     * Returns the full category tree as a nested array, from cache.
     * Shape: [['id', 'name', 'slug', 'children' => [...]], ...]
     *
     * Usage in controllers/Inertia shared data:
     *   $menu = Category::getTree();
     */
    public static function getTree(): array                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
    {
        return Cache::remember(static::CACHE_KEY, static::CACHE_TTL, function () {
            return static::buildTree();
        });
    }

    public static function clearTreeCache(): void
    {
        Cache::forget(static::CACHE_KEY);
    }

    /**
     * Builds the full nested tree array from the database.
     * Loads all categories in two queries (one for roots, recursive eager load).
     */
    protected static function buildTree(): array
    {
        $roots = static::with('recursiveChildren')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
 
        return static::toTreeArray($roots);
    }

    protected static function toTreeArray(Collection $categories): array
    {
        return $categories->map(fn (Category $cat) => [
            'id'       => $cat->id,
            'name'     => $cat->name, 
            'slug'     => $cat->slug,
            'children' => static::toTreeArray($cat->recursiveChildren),
        ])->all();
    }

    // -------------------------------------------------------------------------
    // Model events
    // Keeps materialized paths and the cache in sync automatically.
    // -------------------------------------------------------------------------
 
    protected static function booted(): void
    {
        // Before saving: rebuild this category's materialized path
        // whenever parent_id has changed (or on first creation).
        static::saving(function (Category $category) {
            if ($category->isDirty('parent_id') || ! $category->exists) {
                $category->rebuildPath();
            }
        });
 
        // After saving: if parent changed, rebuild all descendants' paths
        // and bust the menu cache.
        static::saved(function (Category $category) {
            if ($category->wasChanged('parent_id')) {
                $category->rebuildDescendantPaths();
            }
            static::clearTreeCache();
        });
 
        // Bust the cache on delete and restore too.
        static::deleted(fn () => static::clearTreeCache());
        static::restored(fn () => static::clearTreeCache());
    }
}
