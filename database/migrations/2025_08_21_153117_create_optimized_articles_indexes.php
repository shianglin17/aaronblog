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
            // Recreate unique constraints (business logic required)
            $table->unique('slug', 'articles_slug_unique');
            $table->unique('title', 'articles_title_unique');
            
            // Add performance indexes based on query patterns
            $table->index('status', 'articles_status_index');
            $table->index('created_at', 'articles_created_at_index');
            $table->index('user_id', 'articles_user_id_index');
            $table->index('category_id', 'articles_category_id_index');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'created_at'], 'articles_status_created_at_index');
            $table->index(['user_id', 'status', 'created_at'], 'articles_user_status_created_index');
            $table->index(['category_id', 'status'], 'articles_category_status_index');
            
            // Search optimization indexes
            $table->index('description', 'articles_description_index');
        });

        // Recreate indexes on related tables
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('slug', 'categories_slug_unique');
            $table->unique('name', 'categories_name_unique');
            $table->index('created_at', 'categories_created_at_index');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->unique('slug', 'tags_slug_unique');
            $table->index('created_at', 'tags_created_at_index');
        });

        // Optimize pivot table
        Schema::table('article_tag', function (Blueprint $table) {
            $table->index('article_id', 'article_tag_article_id_index');
            $table->index('tag_id', 'article_tag_tag_id_index');
            $table->index(['article_id', 'tag_id'], 'article_tag_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Drop performance indexes
            $table->dropIndex('articles_status_index');
            $table->dropIndex('articles_created_at_index');
            $table->dropIndex('articles_user_id_index');
            $table->dropIndex('articles_category_id_index');
            $table->dropIndex('articles_status_created_at_index');
            $table->dropIndex('articles_user_status_created_index');
            $table->dropIndex('articles_category_status_index');
            $table->dropIndex('articles_description_index');
            
            // Drop unique constraints (they will be recreated by previous migration's down method)
            $table->dropUnique('articles_slug_unique');
            $table->dropUnique('articles_title_unique');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_created_at_index');
            $table->dropUnique('categories_slug_unique');
            $table->dropUnique('categories_name_unique');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('tags_created_at_index');
            $table->dropUnique('tags_slug_unique');
        });

        Schema::table('article_tag', function (Blueprint $table) {
            $table->dropIndex('article_tag_article_id_index');
            $table->dropIndex('article_tag_tag_id_index');
            $table->dropIndex('article_tag_composite_index');
        });
    }
};
