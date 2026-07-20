<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['parent_id', 'name', 'slug', 'materialized_path', 'sort_order', 'show_in_nav'])]

class Category extends Model
{
    use SoftDeletes, LogsActivity;

    protected function casts() {
        return [
            'sort_order' => 'integer'
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
            ->logOnly(['parent_id', 'name', 'slug', 'sort_order'])
            ->logOnlyDirty()        // only log fields that actually changed
            ->dontSubmitEmptyLogs() // skip logging if nothing changed
            ->useLogName('category');
    }

    /**
     * The parent category (null for root-level categories).
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children() {
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

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function getAncestorsAttribute(): \Illuminate\Database\Eloquent\Collection
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
}
