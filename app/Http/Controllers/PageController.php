<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
                'id' => $page->id,
                'slug' => $page->slug,
                'template_key' => $page->template_key,
                'is_published' => $page->isPublished(),

                // The live revision's meta title gives a human-readable label.
                'title' => $page->publishedRevision?->meta_title
                    ?? $page->template_key,

                // Surface any active in-progress revision for status badges.
                'active_revision' => $page->revisions->first()
                    ? [
                        'id' => $page->revisions->first()->id,
                        'status' => $page->revisions->first()->status,
                    ]
                    : null,
                'can_edit' => ! $page->revisions->first()
                    || $page->revisions->first()->status === PageRevision::STATUS_DRAFT,
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
            abort_if($page->revisions()->whereNot('status', PageRevision::STATUS_PUBLISHED)->exists(), 403, 'This page revision is locked while it is under review.');
            $draft = $page->createDraft();
        }

        // Load gallery media attached to this draft.
        $draft->load('gallery');

        return Inertia::render('Cms/Pages/Edit', [
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'template_key' => $page->template_key,
                'is_published' => $page->isPublished(),
            ],
            'revision' => [
                'id' => $draft->id,
                'status' => $draft->status,
                'content' => $draft->content,
                'meta_title' => $draft->meta_title,
                'meta_description' => $draft->meta_description,
                'gallery' => $draft->gallery->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->url,
                    'alt_text' => $m->alt_text,
                ]),
            ],

            // Pass the published revision separately so the editor can
            // compare their draft against what's currently live.
            'published_revision' => $page->publishedRevision
                ? [
                    'id' => $page->publishedRevision->id,
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
    public function update(Page $page, PageRevision $revision): RedirectResponse
    {
        // Ensure the revision actually belongs to this page.
        abort_if($revision->page_id !== $page->id, 403);

        // Only drafts can be edited. Pending/approved/published revisions
        // are locked — the editor must create a new draft instead.
        abort_if($revision->status !== PageRevision::STATUS_DRAFT, 403, 'Only draft revisions can be edited.');

        $request = request();
        $validated = $request->validate([
            'content' => ['nullable', 'array'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'about_images' => ['nullable', 'array'],
            'about_images.hero' => ['nullable', 'file', 'image', 'max:5120'],
            'home_images' => ['nullable', 'array'],
            'home_images.primary' => ['nullable', 'file', 'image', 'max:5120'],
            'home_images.secondary' => ['nullable', 'file', 'image', 'max:5120'],
            'home_images.tertiary' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $revision, $validated): void {
            $revision->update(collect($validated)->only([
                'content',
                'meta_title',
                'meta_description',
            ])->all());

            foreach ([
                'about_images' => ['hero'],
                'home_images' => ['primary', 'secondary', 'tertiary'],
            ] as $field => $slots) {
                foreach ($slots as $slot) {
                    if (! $request->hasFile("{$field}.{$slot}")) {
                        continue;
                    }

                    // The image belongs only to this draft revision. Deleting it
                    // is safe because Page::createDraft copied any live media.
                    $revision->gallery()
                        ->where('alt_text', str_replace('_images', '', $field)."-{$slot}")
                        ->get()
                        ->each
                        ->delete();

                    $file = $request->file("{$field}.{$slot}");
                    $path = $file->store(
                        "pages/{$revision->page_id}/revisions/{$revision->id}",
                        'public',
                    );

                    $revision->media()->create([
                        'collection' => Media::COLLECTION_GALLERY,
                        'path' => $path,
                        'disk' => 'public',
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'alt_text' => str_replace('_images', '', $field)."-{$slot}",
                        'sort_order' => 0,
                    ]);
                }
            }
        });

        return back()->with('success', 'Draft saved.');
    }
}
