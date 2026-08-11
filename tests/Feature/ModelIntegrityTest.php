<?php

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

test('a category cannot be moved beneath one of its descendants', function () {
    $root = Category::create(['name' => 'Root', 'slug' => 'root']);
    $child = Category::create(['name' => 'Child', 'slug' => 'child', 'parent_id' => $root->id]);
    $grandchild = Category::create(['name' => 'Grandchild', 'slug' => 'grandchild', 'parent_id' => $child->id]);

    $root->parent_id = $grandchild->id;
    $root->save();
})->throws(ValidationException::class);

test('descendants exclude soft-deleted categories', function () {
    $root = Category::create(['name' => 'Root', 'slug' => 'root']);
    $child = Category::create(['name' => 'Child', 'slug' => 'child', 'parent_id' => $root->id]);
    $grandchild = Category::create(['name' => 'Grandchild', 'slug' => 'grandchild', 'parent_id' => $child->id]);

    $grandchild->delete();

    expect($root->descendants()->pluck('id')->all())->toBe([$child->id]);
});

test('publishing another products revision is rejected', function () {
    $first = Product::create(['slug' => 'first-product']);
    $second = Product::create(['slug' => 'second-product']);
    $revision = $second->revisions()->create(['name' => 'Second', 'description' => 'Description']);

    $first->publish($revision);
})->throws(InvalidArgumentException::class);

test('only one media item can be primary in a collection', function () {
    $page = Page::create(['slug' => 'media-page', 'template_key' => 'default']);
    $revision = $page->revisions()->create();
    $first = $revision->media()->create(['path' => 'first.jpg', 'is_primary' => true]);
    $second = $revision->media()->create(['path' => 'second.jpg', 'is_primary' => true]);

    expect($first->fresh()->is_primary)->toBeFalse()
        ->and($second->fresh()->is_primary)->toBeTrue();
});

test('force deleting a page removes its revisions, slug history, media records, and media files', function () {
    Storage::fake('public');

    $page = Page::create(['slug' => 'deletable-page', 'template_key' => 'default']);
    $revision = $page->revisions()->create();
    Storage::disk('public')->put('page.jpg', 'file');
    $media = $revision->media()->create(['path' => 'page.jpg']);

    $page->forceDelete();

    $this->assertDatabaseMissing('page_revisions', ['id' => $revision->id]);
    $this->assertDatabaseMissing('media', ['id' => $media->id]);
    $this->assertDatabaseMissing('slug_history', ['slug' => 'deletable-page']);
    Storage::disk('public')->assertMissing('page.jpg');
});

test('force deleting a product removes its revisions and media records', function () {
    $product = Product::create(['slug' => 'deletable-product']);
    $revision = $product->revisions()->create(['name' => 'Product', 'description' => 'Description']);
    $media = $revision->media()->create(['path' => 'product.jpg']);

    $product->forceDelete();

    $this->assertDatabaseMissing('product_revisions', ['id' => $revision->id]);
    $this->assertDatabaseMissing('media', ['id' => $media->id]);
    $this->assertDatabaseMissing('slug_history', ['slug' => 'deletable-product']);
});
