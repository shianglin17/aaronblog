<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(2),
            'content' => fake()->paragraphs(5, true),
            'status' => fake()->randomElement(['draft', 'published']),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * 建立已發布狀態的文章
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * 建立草稿狀態的文章
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
} 