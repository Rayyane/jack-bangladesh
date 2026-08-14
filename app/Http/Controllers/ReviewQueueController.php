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
        // Pending page revisions.
        $pendingPages = PageRevision::with(['page', 'submittedBy'])
            ->where('status', PageRevision::STATUS_PENDING_REVIEW)
            ->oldest()
            ->get()
            ->map(fn (PageRevision $rev) => [
                'id'           => $rev->id,
                'type'         => 'page',
                'label'        => $rev->meta_title ?? $rev->page->template_key,
                'submitted_by' => $rev->submittedBy?->name ?? 'Unknown',
                'submitted_at' => $rev->updated_at->toDateTimeString(),
                'waiting_for'  => $rev->updated_at->diffForHumans(),
 
                // Links the Super Admin needs to act on this revision.
                'links' => [
                    'approve' => route('cms.pages.revisions.approve', [$rev->page_id, $rev->id]),
                    'reject'  => route('cms.pages.revisions.reject',  [$rev->page_id, $rev->id]),
                    'view'    => route('cms.pages.edit', $rev->page_id),
                    'history' => route('cms.pages.revisions.index', $rev->page_id),
                ],
            ]);
 
        // Pending product revisions.
        $pendingProducts = ProductRevision::with(['product.category', 'submittedBy'])
            ->where('status', ProductRevision::STATUS_PENDING_REVIEW)
            ->oldest()
            ->get()
            ->map(fn (ProductRevision $rev) => [
                'id'           => $rev->id,
                'type'         => 'product',
                'label'        => $rev->name,
                'category'     => $rev->product->category?->name ?? 'Uncategorised',
                'submitted_by' => $rev->submittedBy?->name ?? 'Unknown',
                'submitted_at' => $rev->updated_at->toDateTimeString(),
                'waiting_for'  => $rev->updated_at->diffForHumans(),
 
                'links' => [
                    'approve' => route('cms.products.revisions.approve', [$rev->product_id, $rev->id]),
                    'reject'  => route('cms.products.revisions.reject',  [$rev->product_id, $rev->id]),
                    'view'    => route('cms.products.edit', $rev->product_id),
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
