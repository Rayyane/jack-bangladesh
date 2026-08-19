<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Database\Seeder;

class PublicPagesSeeder extends Seeder
{
    /**
     * Bootstrap the public routes with published page records. Existing
     * revisions are never overwritten, so this is safe to run repeatedly.
     */
    public function run(): void
    {
        $pages = [
            'home' => [
                'meta_title' => 'Home',
                'meta_description' => 'Jack Bangladesh industrial sewing solutions.',
                'content' => [],
            ],
            'about' => [
                'meta_title' => 'About us',
                'meta_description' => 'Learn more about Jack Bangladesh.',
                'content' => [
                    'title' => 'About us',
                    'description' => 'Jack Bangladesh delivers industrial sewing technology and support.',
                ],
            ],
            'contact' => [
                'meta_title' => 'Contact',
                'meta_description' => 'Contact Jack Bangladesh.',
                'content' => [
                    'title' => 'Contact us',
                    'description' => 'Get in touch with the Jack Bangladesh team.',
                ],
            ],
        ];

        foreach ($pages as $slug => $data) {
            $page = Page::firstOrCreate(
                ['slug' => $slug],
                ['template_key' => $slug],
            );

            if ($page->revisions()->exists()) {
                continue;
            }

            $revision = $page->revisions()->create([
                'status' => PageRevision::STATUS_PUBLISHED,
                'content' => $data['content'],
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'published_at' => now(),
            ]);

            $page->update(['published_revision_id' => $revision->id]);
        }
    }
}
