<?php

namespace App\Http\Controllers;

use App\Models\PageRevision;
use App\Models\ProductRevision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewQueueController extends Controller
{
    /**
     * Unified inbox of everything currently sitting at pending_review.
     * Super Admin only (enforced by route middleware).
     *
     * Rather than checking pages and products separately, the Super Admin
     * sees all pending revisions in one place, ordered oldest first so
     * nothing gets buried.
     */
    public function index(): Response
    {
        // Pending and approved page revisions. Approved pages remain visible
        // until publication, just like products.
        $pendingPages = PageRevision::with(['page', 'submittedBy'])
            ->whereIn('status', [PageRevision::STATUS_PENDING_REVIEW, PageRevision::STATUS_APPROVED])
            ->oldest()
            ->get()
            ->map(fn (PageRevision $rev) => [
                'id'           => $rev->id,
                'type'         => 'page',
                'status'       => $rev->status,
                'status'       => $rev->status,
                'label'        => $rev->meta_title ?? $rev->page->template_key,
                'submitted_by' => $rev->submittedBy?->name ?? 'Unknown',
                'submitted_at' => $rev->updated_at->toDateTimeString(),
                'waiting_for'  => $rev->updated_at->diffForHumans(),
 
                // Links the Super Admin needs to act on this revision.
                'links' => [
                    'approve' => $rev->status === PageRevision::STATUS_PENDING_REVIEW ? route('cms.pages.revisions.approve', [$rev->page_id, $rev->id]) : null,
                    'reject'  => $rev->status === PageRevision::STATUS_PENDING_REVIEW ? route('cms.pages.revisions.reject', [$rev->page_id, $rev->id]) : null,
                    'publish' => $rev->status === PageRevision::STATUS_APPROVED ? route('cms.pages.revisions.publish', [$rev->page_id, $rev->id]) : null,
                    'view'    => route('cms.pages.edit', $rev->page_id),
                    'history' => route('cms.pages.revisions.index', $rev->page_id),
                ],
            ]);
 
        // Pending and approved product revisions. Approved revisions stay
        // visible until a Super Admin publishes them.
        $pendingProducts = ProductRevision::with(['product.category', 'submittedBy'])
            ->whereIn('status', [
                ProductRevision::STATUS_PENDING_REVIEW,
                ProductRevision::STATUS_APPROVED,
            ])
            ->oldest()
            ->get()
            ->map(fn (ProductRevision $rev) => [
                'id'           => $rev->id,
                'type'         => 'product',
                'status'       => $rev->status,
                'label'        => $rev->name,
                'category'     => $rev->product->category?->name ?? 'Uncategorised',
                'submitted_by' => $rev->submittedBy?->name ?? 'Unknown',
                'submitted_at' => $rev->updated_at->toDateTimeString(),
                'waiting_for'  => $rev->updated_at->diffForHumans(),
 
                'links' => [
                    'approve' => $rev->status === ProductRevision::STATUS_PENDING_REVIEW
                        ? route('cms.products.revisions.approve', [$rev->product_id, $rev->id])
                        : null,
                    'reject'  => $rev->status === ProductRevision::STATUS_PENDING_REVIEW
                        ? route('cms.products.revisions.reject', [$rev->product_id, $rev->id])
                        : null,
                    'publish' => $rev->status === ProductRevision::STATUS_APPROVED
                        ? route('cms.products.revisions.publish', [$rev->product_id, $rev->id])
                        : null,
                    // Pending revisions are locked; inspect them in history.
                    'view'    => route('cms.products.revisions.index', $rev->product_id),
                    'history' => route('cms.products.revisions.index', $rev->product_id),
                ],
            ]);
 
        // Merge and re-sort by submission time so the oldest item across
        // both types always appears first.
        $queue = $pendingPages
            ->concat($pendingProducts)
            ->sortBy('submitted_at')
            ->values();
 
        return Inertia::render('Cms/ReviewQueue/Index', [
            'queue' => $queue,
            'counts' => [
                'total'    => $queue->count(),
                'pages'    => $pendingPages->count(),
                'products' => $pendingProducts->count(),
            ],
        ]);
    }
}
