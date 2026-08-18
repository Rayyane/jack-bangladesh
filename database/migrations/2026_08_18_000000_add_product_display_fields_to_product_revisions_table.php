<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_revisions', function (Blueprint $table) {
            $table->string('price')->nullable()->after('description');
            $table->string('video_url')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('product_revisions', function (Blueprint $table) {
            $table->dropColumn(['price', 'video_url']);
        });
    }
};
