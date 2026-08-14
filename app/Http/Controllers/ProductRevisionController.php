<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductRevisionController extends Controller
{
    /**
     * Show the full revision history for a product.
     * Both roles can view. Timeline ordered newest first.
     */
    public function index(Product $product): Response
    {
        $revisions = $product->revisions()
            ->with(['submittedBy', 'approvedBy'])
            ->get()
            ->map(fn (ProductRevision $rev) => [
                'id' => $rev->id,
                'status' => $rev->status,
                'name' => $rev->name,
                'submitted_by' => $rev->submittedBy?->name,
                'approved_by' => $rev->approvedBy?->name,
                'publish_at' => $rev->publish_at?->toDateTimeString(),
                'published_at' => $rev->published_at?->toDateTimeString(),
                'created_at' => $rev->created_at->toDateTimeString(),
                'is_live' => $product->published_revision_id === $rev->id,
            ]);

        return Inertia::render('Cms/Products/Revisions', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->publishedRevision?->name
                    ?? $revisions->first()['name']
                    ?? '(Untitled)',
            ],
            'revisions' => $revisions,
        ]);
    }

    /**
     * Create a new draft from the published revision.
     * Guards against creating a duplicate draft.
     *
     * Transitions: (none) → draft
     */
    public function store(Product $product): RedirectResponse
    {
        $existingDraft = $product->revisions()
            ->where('status', ProductRevision::STATUS_DRAFT)
            ->exists();

        if ($existingDraft) {
            return back()->withErrors([
                'draft' => 'A draft already exists for this product. Edit that one instead.',
            ]);
        }

        DB::transaction(function () use ($product) {
            $draft = $product->createDraft();
            $product->copySectionsToDraft($draft);
        });

        return redirect()
            ->route('cms.products.edit', $product)
            ->with('success', 'New draft created.');
    }

    /**
     * Submit a draft for editorial review.
     * Available to both roles.
     *
     * Transitions: draft → pending_review
     */
    public function submit(Product $product, ProductRevision $revision): RedirectResponse
    {
        abort_if($revision->product_id !== $product->id, 403);
        abort_if(
            $revision->status !== ProductRevision::STATUS_DRAFT,
            403,
            'Only drafts can be submitted for review.'
        );

        $revision->submitForReview($this->authenticatedUser());

        return back()->with('success', 'Revision submitted for review.');
    }

    /**
     * Approve a pending revision.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: pending_review → approved
     */
    public function approve(Product $product, ProductRevision $revision): RedirectResponse
    {
        abort_if($revision->product_id !== $product->id, 403);
        abort_if(
            $revision->status !== ProductRevision::STATUS_PENDING_REVIEW,
            403,
            'Only pending revisions can be approved.'
        );

        $revision->approve($this->authenticatedUser());

        return back()->with('success', 'Revision approved. It can now be published.');
    }

    /**
     * Reject a pending revision back to draft.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: pending_review → draft
     */
    public function reject(Product $product, ProductRevision $revision): RedirectResponse
    {
        abort_if($revision->product_id !== $product->id, 403);
        abort_if(
            $revision->status !== ProductRevision::STATUS_PENDING_REVIEW,
            403,
            'Only pending revisions can be rejected.'
        );

        request()->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $revision->reject();

        return redirect()
            ->route('cms.products.revisions.index', $product)
            ->with('rejection_reason', request('reason'))
            ->with('success', 'Revision sent back to draft.');
    }

    /**
     * Publish an approved revision, making the product live.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: approved → published
     * Updates products.published_revision_id to point to this revision.
     */
    public function publish(Product $product, ProductRevision $revision): RedirectResponse
    {
        abort_if($revision->product_id !== $product->id, 403);
        abort_if(
            $revision->status !== ProductRevision::STATUS_APPROVED,
            403,
            'Only approved revisions can be published.'
        );

        DB::transaction(fn () => $product->publish($revision));

        return redirect()
            ->route('cms.products.revisions.index', $product)
            ->with('success', 'Product is now live.');
    }
}
