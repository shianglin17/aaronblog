<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '技術文章',
                'slug' => 'tech',
                'description' => '關於程式開發、技術研究的文章',
            ],
            [
                'name' => '經驗分享',
                'slug' => 'experience',
                'description' => '個人經驗分享',
            ],
            [
                'name' => '閱讀心得',
                'slug' => 'reading',
                'description' => '讀書心得分享',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
