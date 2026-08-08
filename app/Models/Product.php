<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['category_id', 'slug', 'published_revision_id'])]
class Product extends Model
{
    use SoftDeletes;

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function revisions() {
        return $this->hasMany(ProductRevision::class)->latest();
    }

    public function publishedRevision() {
        return $this->belongsTo(ProductRevision::class, 'published_revision_id');
    }

    public function slugHistory() {
        return $this->morphMany(SlugHistory::class, 'sluggable');
    }
    
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
 
    /**
     * Publish a specific revision.
     * Flips the pointer on the product and marks the revision as published.
     * Always wrap in a DB::transaction() in the service layer.
     *
     * Usage:
     *   DB::transaction(fn () => $product->publish($revision));
     */
    public function publish(ProductRevision $revision) {
        $revision->update([
            'status'       => ProductRevision::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
 
        $this->update([
            'published_revision_id' => $revision->id,
        ]);
    }

    public function isPublished() {
        return ! is_null($this->published_revision_id);
    }

    /**
     * Create a new draft revision, seeded from the currently published
     * revision so editors start with existing content rather than a blank form.
     * Sections are copied separately by the service layer after calling this.
     *
     * Usage (in service layer):
     *   DB::transaction(function () use ($product) {
     *       $draft = $product->createDraft();
     *       $product->copySectionsToDraft($draft);
     *   });
     */
    public function createDraft(array $attributes = []): ProductRevision
    {
        $seed = $this->publishedRevision
            ? $this->publishedRevision->only([
                'name',
                'description',
                'meta_title',
                'meta_description',
            ])
            : [];
 
        return $this->revisions()->create(array_merge($seed, $attributes, [
            'status' => ProductRevision::STATUS_DRAFT,
        ]));
    }
 
    /**
     * Copy all sections (and their images) from the published revision
     * into a new draft revision. Called by the service layer alongside createDraft().
     *
     * Sections belong to revisions, so a new draft starts with no sections
     * unless we explicitly copy them from the previous published state.
     */
    public function copySectionsToDraft(ProductRevision $draft): void
    {
        if (! $this->publishedRevision) {
            return;
        }
 
        foreach ($this->publishedRevision->sections as $section) {
            $draft->sections()->create($section->only([
                'title',
                'description',
                'image_path',
                'image_alt',
                'sort_order',
            ]));
        }
    }
 
    /**
     * Whether this product is uncategorised (category was deleted).
     * The CMS should flag these for reassignment.
     */
    public function isUncategorised(): bool
    {
        return is_null($this->category_id);
    }
}
