<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sluggable_type',
    'sluggable_id',
    'slug',
    'is_current'
])]

class SlugHistory extends Model
{
    protected function casts() {
        return [
            'is_current' => 'boolean'
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
 
    /**
     * The owning model (Page or Product) for this slug entry.
     */
    public function sluggable()
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------
 
    /**
     * Record a new slug for a model, marking it as current and retiring
     * any previously current slug for the same model.
     *
     * Call this in a model observer or service layer whenever a slug changes.
     *
     * Usage:
     *   SlugHistory::record($product, 'new-product-slug');
     */
    public static function record(Model $model, string $newSlug): static
    {
        // Retire the previously current slug (if any).
        static::where('sluggable_type', $model->getMorphClass())
            ->where('sluggable_id', $model->getKey())
            ->where('is_current', true)
            ->update(['is_current' => false]);
 
        // Record the new current slug.
        return static::create([
            'sluggable_type' => $model->getMorphClass(),
            'sluggable_id'   => $model->getKey(),
            'slug'           => $newSlug,
            'is_current'     => true,
        ]);
    }
 
    /**
     * Resolve a slug to its owning model (Page or Product).
     * Returns null if the slug has never existed.
     *
     * Use this in the route fallback / 301 redirect middleware:
     *
     *   $entry = SlugHistory::resolve($slug);
     *   if ($entry && ! $entry->is_current) {
     *       // Slug is old — find the current slug and 301 redirect.
     *       $currentSlug = SlugHistory::currentSlugFor($entry->sluggable);
     *       return redirect($currentSlug, 301);
     *   }
     */
    public static function resolve(string $slug): ?static
    {
        return static::where('slug', $slug)->first();
    }
 
    /**
     * Get the current (live) slug string for a given model.
     * Returns null if no slug has been recorded yet.
     *
     * Usage:
     *   $currentSlug = SlugHistory::currentSlugFor($product);
     */
    public static function currentSlugFor(Model $model): ?string
    {
        return static::where('sluggable_type', $model->getMorphClass())
            ->where('sluggable_id', $model->getKey())
            ->where('is_current', true)
            ->value('slug');
    }
 
    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
 
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
 
    public function scopeRetired($query)
    {
        return $query->where('is_current', false);
    }
}
