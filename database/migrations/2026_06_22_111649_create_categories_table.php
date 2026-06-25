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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('materialized_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('show_in_nav')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->index('parent_id');
            $table->index('materialized_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
