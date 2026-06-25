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
        Schema::create('product_revisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            
            // Workflow: draft → pending_review → approved → published
            $table->string('status')->default('draft');

            $table->string('name');
            $table->text('description');

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->foreignId('submitted_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('publish_at')->nullable();;
            $table->timestamp('published_at')->nullable();;

            $table->timestamps();

            $table->index('status');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_revisions');
    }
};
