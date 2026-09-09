<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align existing production schemas with the request validation rules.
     *
     * Fresh installations receive these definitions from their original
     * migrations; this migration updates databases that have already run them.
     */
    public function up(): void
    {
        Schema::table('product_revisions', function (Blueprint $table) {
            $table->text('meta_description')->nullable()->change();
            $table->string('video_url', 2048)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_revisions', function (Blueprint $table) {
            $table->string('meta_description')->nullable()->change();
            $table->string('video_url')->nullable()->change();
        });
    }
};
