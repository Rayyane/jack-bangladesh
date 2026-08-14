<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * List all pages with their current published status and
     * any active draft/pending revision, so the editor can see
     * at a glance what needs attention.
     */
    public function index(): Response
    {
        $pages = Page::with([
            'publishedRevision',
 
            // The most recent non-published revision, if any.
            // Used to show "Draft in progress" or "Pending review" badges.
            'revisions' => fn ($q) => $q
                ->whereNot('status', PageRevision::STATUS_PUBLISHED)
                ->latest()
                ->limit(1),
        ])->get();
 
        return Inertia::render('Cms/Pages/Index', [
            'pages' => $pages->map(fn (Page $page) => [
                'id'           => $page->id,
                'slug'         => $page->slug,
                'template_key' => $page->template_key,
                'is_published' => $page->isPublished(),
 
                // The live revision's meta title gives a human-readable label.
                'title' => $page->publishedRevision?->meta_title
                    ?? $page->template_key,
 
                // Surface any active in-progress revision for status badges.
                'active_revision' => $page->revisions->first()
                    ? [
                        'id'     => $page->revisions->first()->id,
                        'status' => $page->revisions->first()->status,
                    ]
                    : null,
            ]),
        ]);
    }
 
    /**
     * Load the page editor.
     *
     * Always gives the editor a draft to work with:
     * - If a draft already exists, load it.
     * - If not, create one seeded from the published revision.
     *
     * The Vue component uses template_key to decide which form layout
     * to render (homepage fields vs about page fields vs contact fields).
     */
    public function edit(Page $page): Response
    {
        // Find an existing draft, or create a fresh one.
        $draft = $page->revisions()
            ->where('status', PageRevision::STATUS_DRAFT)
            ->latest()
            ->first();
 
        if (! $draft) {
            $draft = $page->createDraft();
        }
 
        // Load gallery media attached to this draft.
        $draft->load('gallery');
 
        return Inertia::render('Cms/Pages/Edit', [
            'page' => [
                'id'           => $page->id,
                'slug'         => $page->slug,
                'template_key' => $page->template_key,
                'is_published' => $page->isPublished(),
            ],
            'revision' => [
                'id'               => $draft->id,
                'status'           => $draft->status,
                'content'          => $draft->content,
                'meta_title'       => $draft->meta_title,
                'meta_description' => $draft->meta_description,
                'gallery'          => $draft->gallery->map(fn ($m) => [
                    'id'       => $m->id,
                    'url'      => $m->url,
                    'alt_text' => $m->alt_text,
                ]),
            ],
 
            // Pass the published revision separately so the editor can
            // compare their draft against what's currently live.
            'published_revision' => $page->publishedRevision
                ? [
                    'id'      => $page->publishedRevision->id,
                    'content' => $page->publishedRevision->content,
                ]
                : null,
        ]);
    }
 
    /**
     * Save changes to an existing draft revision.
     *
     * Does NOT change the workflow status — saving is just saving.
     * The editor explicitly submits for review as a separate action
     * via PageRevisionController@submit.
     */
    public function update(Page $page, PageRevision $revision): \Illuminate\Http\RedirectResponse
    {
        // Ensure the revision actually belongs to this page.
        abort_if($revision->page_id !== $page->id, 403);
 
        // Only drafts can be edited. Pending/approved/published revisions
        // are locked — the editor must create a new draft instead.
        abort_if($revision->status !== PageRevision::STATUS_DRAFT, 403, 'Only draft revisions can be edited.');
 
        $validated = request()->validate([
            'content'          => ['nullable', 'array'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);
 
        $revision->update($validated);
 
        return back()->with('success', 'Draft saved.');
    }
}
