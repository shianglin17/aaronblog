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
        // 移除 users 表的 deleted_at 欄位
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // 移除 articles 表的 deleted_at 欄位
        Schema::table('articles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // 移除 categories 表的 deleted_at 欄位
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // 移除 tags 表的 deleted_at 欄位
        Schema::table('tags', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 如果需要回滾，重新添加 deleted_at 欄位
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}; 