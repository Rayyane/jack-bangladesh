<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'page_id',
    'status',
    'content',
    'meta_title',
    'meta_description',
    'og_image_path',
    'submitted_by',
    'approved_by',
    'publish_at',
    'published_at',
])]
class PageRevision extends Model
{

    protected function casts() {
        return [
            'content' => 'array',
            'show_in_nav' => 'publish_at',
            'published_at' => 'datetime'
        ];
    }
    // -------------------------------------------------------------------------
    // Workflow status constants
    // Use these throughout the app instead of raw strings to avoid typos.
    // -------------------------------------------------------------------------
 
    const STATUS_DRAFT          = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED       = 'approved';
    const STATUS_PUBLISHED      = 'published';
 
    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
 
    /**
     * The page this revision belongs to.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
 
    /**
     * The user who submitted this revision for review.
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
 
    /**
     * The user who approved this revision.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    /**
     * All media attached to this revision (polymorphic).
     * Scoped by collection below for convenience.
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
 
    /**
     * Gallery images for this revision.
     * e.g. background images, section photos, OG image uploads.
     */
    public function gallery()
    {
        return $this->media()->where('collection', 'gallery');
    }
 
    // -------------------------------------------------------------------------
    // Workflow helpers
    // -------------------------------------------------------------------------
 
    /**
     * Submit this revision for editorial review.
     * Call from the service layer after permission check.
     */
    public function submitForReview(User $submitter)
    {
        $this->update([
            'status'       => self::STATUS_PENDING_REVIEW,
            'submitted_by' => $submitter->id,
        ]);
    }
 
    /**
     * Approve this revision (allows a publisher to publish it).
     */
    public function approve(User $approver)
    {
        $this->update([
            'status'      => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
        ]);
    }
 
    /**
     * Reject back to draft (e.g. reviewer requests changes).
     */
    public function reject()
    {
        $this->update(['status' => self::STATUS_DRAFT]);
    }
 
    /**
     * Whether this revision is the currently live one on its page.
     */
    public function isLive()
    {
        return $this->page->published_revision_id === $this->id;
    }
 
    // -------------------------------------------------------------------------
    // Content helpers
    // -------------------------------------------------------------------------
 
    /**
     * Safely retrieve a nested value from the content JSON.
     * Avoids null errors when a section key doesn't exist yet.
     *
     * Usage: $revision->getContent('hero.headline', 'Default heading')
     */
    public function getContent(string $key, mixed $default = null)
    {
        return data_get($this->content, $key, $default);
    }
 
    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
 
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }
 
    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }
 
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
 
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}
