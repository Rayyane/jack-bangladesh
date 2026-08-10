<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['slug', 'template_key', 'published_revision_id'])]
class Page extends Model
{
    use SoftDeletes;

    /**
    * All revisions for this page, newest first.
    * This is the full history — draft, pending, approved, published, all of it.
    */

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class)->latest();
    }

    /**
     * The currently live revision.
     * The public site always reads through this — editors can be mid-draft
     * without affecting what visitors see.
     *
     * Note: this is a belongsTo despite pages being the "parent" conceptually.
     * The FK (published_revision_id) lives on the pages table, pointing into
     * page_revisions — that's what makes it a belongsTo.
     */

    public function publishedRevision(): BelongsTo
    {
        return $this->belongsTo(PageRevision::class, 'published_revision_id');
    }

    /**
     * The latest draft revision — what an editor would pick up and continue.
     * Returns null if no draft exists (e.g. published content with no active edit).
     */

    public function latestDraft(): ?PageRevision
    {
        return $this->revisions()->where('status', 'draft')->first();
    }

    /**
     * Slug history entries for this page (polymorphic).
     * Used to serve 301 redirects when a slug changes.
     */

    public function slugHistory(): MorphMany
    {
        return $this->morphMany(SlugHistory::class, 'sluggable');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
 
    /**
     * Publish a specific revision. Updates the pointer and marks the
     * revision as published. Wrap in a DB transaction in the service layer.
     *
     * Usage:
     *   DB::transaction(fn () => $page->publish($revision));
     */
    public function publish(PageRevision $revision): void
    {
        if ($revision->page_id === $this->id ) {
            $revision->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
 
            $this->update([
                'published_revision_id' => $revision->id,
            ]);
        }
        
    }
 
    /**
     * Whether this page has any live content visible to the public.
     */
    public function isPublished(): bool
    {
        return ! is_null($this->published_revision_id);
    }
 
    /**
     * Create a new draft revision, optionally seeding it from the
     * currently published revision so editors start with existing content.
     */
    public function createDraft(array $attributes = []): PageRevision
    {
        $seed = $this->publishedRevision
            ? $this->publishedRevision->only([
                'content',
                'meta_title',
                'meta_description',
                'og_image_path',
            ])
            : [];
 
        return $this->revisions()->create(array_merge($seed, $attributes, [
            'status' => 'draft',
        ]));
    }
}
