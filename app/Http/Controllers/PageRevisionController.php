<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PageRevisionController extends Controller
{
    /**
     * Show the full revision history for a page.
     * Both roles can view history — neither can alter it here.
     */
    public function index(Page $page): Response
    {
        $revisions = $page->revisions()
            ->with(['submittedBy', 'approvedBy'])
            ->get()
            ->map(fn (PageRevision $rev) => [
                'id' => $rev->id,
                'status' => $rev->status,
                'meta_title' => $rev->meta_title,
                'submitted_by' => $rev->submittedBy?->name,
                'approved_by' => $rev->approvedBy?->name,
                'publish_at' => $rev->publish_at?->toDateTimeString(),
                'published_at' => $rev->published_at?->toDateTimeString(),
                'created_at' => $rev->created_at->toDateTimeString(),
                'is_live' => $page->published_revision_id === $rev->id,
            ]);

        return Inertia::render('Cms/Pages/Revisions', [
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'template_key' => $page->template_key,
            ],
            'revisions' => $revisions,
        ]);
    }

    /**
     * Submit a draft revision for editorial review.
     * Available to both roles.
     *
     * Transitions: draft → pending_review
     */
    public function submit(Page $page, PageRevision $revision): RedirectResponse
    {
        abort_if($revision->page_id !== $page->id, 403);
        abort_if($revision->status !== PageRevision::STATUS_DRAFT, 403, 'Only drafts can be submitted for review.');

        $revision->submitForReview($this->authenticatedUser());

        return back()->with('success', 'Revision submitted for review.');
    }

    /**
     * Approve a pending revision.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: pending_review → approved
     */
    public function approve(Page $page, PageRevision $revision): RedirectResponse
    {
        abort_if($revision->page_id !== $page->id, 403);
        abort_if($revision->status !== PageRevision::STATUS_PENDING_REVIEW, 403, 'Only pending revisions can be approved.');

        $revision->approve($this->authenticatedUser());

        return back()->with('success', 'Revision approved. It can now be published.');
    }

    /**
     * Reject a pending revision back to draft.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: pending_review → draft
     *
     * Optionally accepts a rejection reason to display to the editor.
     */
    public function reject(Page $page, PageRevision $revision): RedirectResponse
    {
        abort_if($revision->page_id !== $page->id, 403);
        abort_if($revision->status !== PageRevision::STATUS_PENDING_REVIEW, 403, 'Only pending revisions can be rejected.');

        request()->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $revision->reject();

        // Store the rejection reason in the session for the editor to see.
        // In a fuller implementation this would trigger a notification.
        return redirect()
            ->route('cms.pages.revisions.index', $page)
            ->with('rejection_reason', request('reason'))
            ->with('success', 'Revision sent back to draft.');
    }

    /**
     * Publish an approved revision, making it live on the public site.
     * Super Admin only (enforced by route middleware).
     *
     * Transitions: approved → published
     * Updates pages.published_revision_id to point to this revision.
     */
    public function publish(Page $page, PageRevision $revision): RedirectResponse
    {
        abort_if($revision->page_id !== $page->id, 403);
        abort_if($revision->status !== PageRevision::STATUS_APPROVED, 403, 'Only approved revisions can be published.');

        // Wrapped in a transaction because publish() touches two tables:
        // page_revisions (status + published_at) and pages (published_revision_id).
        DB::transaction(fn () => $page->publish($revision));

        return redirect()
            ->route('cms.pages.revisions.index', $page)
            ->with('success', 'Page is now live.');
    }

    /**
     * Create a new draft from the currently published revision.
     * Useful when an editor wants to start editing a live page
     * without an existing draft already being in progress.
     *
     * In practice, PageController@edit handles this automatically —
     * this endpoint exists for an explicit "Start new draft" button
     * in the revision history view.
     */
    public function store(Page $page): RedirectResponse
    {
        // Don't allow a new draft if one already exists.
        $existingDraft = $page->revisions()
            ->where('status', PageRevision::STATUS_DRAFT)
            ->exists();

        if ($existingDraft) {
            return back()->withErrors([
                'draft' => 'A draft already exists for this page. Edit that one instead.',
            ]);
        }

        $page->createDraft();

        return redirect()
            ->route('cms.pages.edit', $page)
            ->with('success', 'New draft created.');
    }
}
