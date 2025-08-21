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
        Schema::table('articles', function (Blueprint $table) {
            // Drop existing unique indexes (will be recreated in next migration)
            $table->dropUnique(['slug']); // articles_slug_unique
            $table->dropUnique(['title']); // articles_title_unique
        });

        // Drop indexes on related tables if needed
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']); // categories_slug_unique
            $table->dropUnique(['name']); // categories_name_unique
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['slug']); // tags_slug_unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the original unique indexes
        Schema::table('articles', function (Blueprint $table) {
            $table->unique('slug');
            $table->unique('title');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('slug');
            $table->unique('name');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->unique('slug');
        });
    }
};
