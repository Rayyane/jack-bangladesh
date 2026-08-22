<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'mediable_type',
    'mediable_id',
    'collection',
    'path',
    'disk',
    'mime_type',
    'size',
    'alt_text',
    'is_primary',
    'sort_order',
])]

class Media extends Model
{
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Collection name constants
    // Use these instead of raw strings throughout the app.
    // -------------------------------------------------------------------------

    const COLLECTION_GALLERY = 'gallery';

    const COLLECTION_SPECIFICATIONS = 'specifications';

    const COLLECTION_DEFAULT = 'default';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // URL / storage helpers
    // -------------------------------------------------------------------------

    /**
     * The full public URL to this file.
     * Use this in API responses and Vue components — never expose raw paths.
     *
     * Usage: $media->url
     */
    public function getUrlAttribute(): string
    {
        return self::publicUrl($this->path, $this->disk);
    }

    /**
     * Return a public-disk URL using the URL of the request that rendered it.
     *
     * This avoids sending CMS and public pages back to the APP_URL host (or
     * port) when the application is being accessed through a local vhost.
     */
    public static function publicUrl(string $path, string $disk = 'public'): string
    {
        $url = Storage::disk($disk)->url($path);

        if ($disk !== 'public' || ! app()->bound('request')) {
            return $url;
        }

        $urlPath = parse_url($url, PHP_URL_PATH) ?: $url;
        $storagePosition = strpos($urlPath, '/storage/');

        if ($storagePosition === false) {
            return $url;
        }

        return rtrim(request()->getSchemeAndHttpHost().request()->getBaseUrl(), '/')
            .substr($urlPath, $storagePosition);
    }

    /**
     * Human-readable file size string.
     * Usage: $media->human_size  →  "2.4 MB"
     */
    public function getHumanSizeAttribute(): string
    {
        if (is_null($this->size)) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 1).' '.$units[$unit];
    }

    /**
     * Whether this file is an image (based on mime type).
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Delete the physical file from storage when the model is deleted.
     * Wired into model events in booted() below.
     */
    protected function deleteFile(): void
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            Storage::disk($this->disk)->delete($this->path);
        }
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeInCollection($query, string $collection)
    {
        return $query->where('collection', $collection);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeGallery($query)
    {
        return $query->where('collection', self::COLLECTION_GALLERY);
    }

    public function scopeSpecifications($query)
    {
        return $query->where('collection', self::COLLECTION_SPECIFICATIONS);
    }

    // -------------------------------------------------------------------------
    // Model events
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::saving(static function (Media $media): void {
            $media->collection ??= self::COLLECTION_DEFAULT;

            if (! $media->is_primary || ! $media->mediable_type || ! $media->mediable_id) {
                return;
            }

            static::query()
                ->where('mediable_type', $media->mediable_type)
                ->where('mediable_id', $media->mediable_id)
                ->where('collection', $media->collection)
                ->when($media->exists, static fn ($query) => $query->whereKeyNot($media->getKey()))
                ->update(['is_primary' => false]);
        });

        // Automatically delete the physical file from storage
        // whenever a Media record is deleted from the database.
        // This keeps storage clean without needing manual cleanup calls.
        static::deleted(function (Media $media) {
            $media->deleteFile();
        });
    }
}
