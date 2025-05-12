<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 先清空所有現有文章和關聯
        Article::query()->forceDelete();
        DB::table('article_tag')->truncate();

        // 取得所有分類和標籤
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        // 建立 20 篇測試文章
        for ($i = 1; $i <= 20; $i++) {
            $article = Article::create([
                'title' => "測試文章 {$i}",
                'slug' => "test-article-{$i}",
                'description' => "這是測試文章 {$i} 的簡短描述，概括了文章的主要內容和關鍵點。",
                'user_id' => $users->random()->id,
                'content' => "這是測試文章 {$i} 的內容。這裡可以放入一些較長的測試內容...",
                'category_id' => $categories->random()->id
            ]);

            // 隨機配置 1-3 個標籤
            $article->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
