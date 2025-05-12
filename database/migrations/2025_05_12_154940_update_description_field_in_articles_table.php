<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 先將現有的 NULL 值填上預設值
        DB::statement("UPDATE articles SET description = '這是自動產生的文章摘要...' WHERE description IS NULL");

        // 再修改 description 欄位為非 NULL
        Schema::table('articles', function (Blueprint $table) {
            $table->string('description', 255)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // 恢復 description 欄位為可為 NULL
            $table->string('description', 255)->nullable()->change();
        });
    }
};
