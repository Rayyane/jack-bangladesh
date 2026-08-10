<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_revision_id', 'title', 'description', 'image_path', 'image_alt', 'sort_order'])]

class ProductSection extends Model
{
    protected function casts() {
        return [
            'sort_order' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
 
    public function revision() {
        return $this->belongsTo(ProductRevision::class, 'product_revision_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
 
    /**
     * Whether this section has an image.
     */
    public function hasImage(): bool
    {
        return ! is_null($this->image_path);
    }
 
    /**
     * Whether this section has a description body.
     */
    public function hasDescription(): bool
    {
        return ! is_null($this->description) && $this->description !== '';
    }
}
