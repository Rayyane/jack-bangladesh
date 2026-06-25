<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slug_history', function (Blueprint $table) {
            $table->id();
            $table->morphs('sluggable');
            $table->string('slug');

            // is_current = true marks the slug currently in use on the entity.
            // Old rows (is_current = false) are kept to serve 301 redirects.
            $table->boolean('is_current')->default(true);

            $table->timestamps();
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slug_history');
    }
};
