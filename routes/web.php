<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PageRevisionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductRevisionController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\ReviewQueueController;
use Illuminate\Support\Facades\Route;

// Route::inertia('/', 'Home')->name('home');
// Route::inertia('/categories', 'CategoryView')->name('CategoryView');
// Route::inertia('/product-view', 'ProductDetailView')->name('ProductDetailView');
// Route::inertia('/about-us', 'AboutUs')->name('AboutUsView');
// Route::inertia('/contact-us', 'Contact')->name('ContactView');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });

// =============================================================================
// Public routes — no authentication required
// =============================================================================

// Static pages (homepage, about, contact etc.)
// The homepage is served at / as a special case, then other pages by slug.
Route::get('/', fn () => app(PublicPageController::class)->show('home'))->name('home');
Route::get('/about', fn () => app(PublicPageController::class)->show('about'))->name('about');
Route::get('/contact', fn () => app(PublicPageController::class)->show('contact'))->name('contact');

// Product catalog and individual product pages.
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [PublicProductController::class, 'show'])->name('products.show');

// =============================================================================
// CMS routes — authenticated users only
// =============================================================================

Route::middleware(['auth', 'verified'])
    ->get('dashboard', fn () => inertia('Cms/Dashboard'))
    ->name('dashboard');

Route::prefix('cms')
    ->name('cms.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Dashboard (both roles).
        Route::get('/', fn () => inertia('Cms/Dashboard'))->name('dashboard');

        // -----------------------------------------------------------------
        // Pages — both roles can view and edit drafts
        // -----------------------------------------------------------------

        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::patch('pages/{page}/revisions/{revision}', [PageController::class, 'update'])->name('pages.update');

        // Page revision history (both roles).
        Route::get('pages/{page}/revisions', [PageRevisionController::class, 'index'])->name('pages.revisions.index');

        // Start a new draft (both roles).
        Route::post('pages/{page}/revisions', [PageRevisionController::class, 'store'])->name('pages.revisions.store');

        // Submit for review (both roles).
        Route::post('pages/{page}/revisions/{revision}/submit', [PageRevisionController::class, 'submit'])->name('pages.revisions.submit');

        // -----------------------------------------------------------------
        // Products — both roles can view and edit drafts
        // -----------------------------------------------------------------

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::patch('products/{product}/revisions/{revision}', [ProductController::class, 'update'])->name('products.update');

        // Product revision history (both roles).
        Route::get('products/{product}/revisions', [ProductRevisionController::class, 'index'])->name('products.revisions.index');

        // Start a new draft (both roles).
        Route::post('products/{product}/revisions', [ProductRevisionController::class, 'store'])->name('products.revisions.store');

        // Submit for review (both roles).
        Route::post('products/{product}/revisions/{revision}/submit', [ProductRevisionController::class, 'submit'])->name('products.revisions.submit');

        // -----------------------------------------------------------------
        // Super Admin only routes
        // -----------------------------------------------------------------

        Route::middleware('role:super_admin')->group(function () {

            // Categories.
            Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('categories/sort', [CategoryController::class, 'sort'])->name('categories.sort');
            Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

            // Page workflow transitions.
            Route::post('pages/{page}/revisions/{revision}/approve', [PageRevisionController::class, 'approve'])->name('pages.revisions.approve');
            Route::post('pages/{page}/revisions/{revision}/reject', [PageRevisionController::class, 'reject'])->name('pages.revisions.reject');
            Route::post('pages/{page}/revisions/{revision}/publish', [PageRevisionController::class, 'publish'])->name('pages.revisions.publish');

            // Product workflow transitions.
            Route::post('products/{product}/revisions/{revision}/approve', [ProductRevisionController::class, 'approve'])->name('products.revisions.approve');
            Route::post('products/{product}/revisions/{revision}/reject', [ProductRevisionController::class, 'reject'])->name('products.revisions.reject');
            Route::post('products/{product}/revisions/{revision}/publish', [ProductRevisionController::class, 'publish'])->name('products.revisions.publish');

            // Product delete.
            Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

            // Review queue.
            Route::get('review-queue', [ReviewQueueController::class, 'index'])->name('review-queue.index');
        });
    });

require __DIR__.'/settings.php';
