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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->comment('slug')->unique();
            $table->string('title', 255)->comment('文章標題')->unique();
            $table->text('content')->comment('文章內容');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('使用者 ID');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->comment('分類 ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
