<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'product_id',
    'status',
    'name',
    'description',
    'meta_title',
    'meta_description',
    'submitted_by',
    'approved_by',
    'publish_at',
    'published_at'
])]

class ProductRevision extends Model
{
    protected function casts() {
        return [
            'publish_at' => 'datetime',
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
 
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function submittedBy() {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sections() {
        return $this->hasMany(ProductSection::class)->orderBy('sort_order');
    }

    public function media() {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    /**
     * Product gallery images — shown in the photo carousel on the product page.
     */
    public function gallery(): MorphMany
    {
        return $this->media()->where('collection', 'gallery');
    }

    /**
     * The specification sheet image for this revision.
     * Expect at most one row — enforced at the application layer.
     *
     * Usage: $revision->specifications()->first()
     */
    public function specifications(): MorphMany
    {
        return $this->media()->where('collection', 'specifications');
    }

    // -------------------------------------------------------------------------
    // Workflow helpers
    // -------------------------------------------------------------------------
 
    /**
     * Submit for editorial review.
     */
    public function submitForReview(User $submitter): void
    {
        $this->update([
            'status'       => self::STATUS_PENDING_REVIEW,
            'submitted_by' => $submitter->id,
        ]);
    }
 
    /**
     * Approve this revision (a publisher can then publish it).
     */
    public function approve(User $approver): void
    {
        $this->update([
            'status'      => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
        ]);
    }
 
    /**
     * Reject back to draft (reviewer requests changes).
     */
    public function reject(): void
    {
        $this->update(['status' => self::STATUS_DRAFT]);
    }
 
    /**
     * Whether this revision is the currently live one on its product.
     */
    public function isLive(): bool
    {
        return $this->product->published_revision_id === $this->id;
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
