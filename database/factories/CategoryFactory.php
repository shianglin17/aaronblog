<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Technology', 'Laravel', 'Vue.js', 'JavaScript', 'PHP', 
            'Frontend', 'Backend', 'Database', 'System Design', 'Project Management',
            'DevOps', 'Testing', 'Security', 'Mobile', 'AI', 'Machine Learning'
        ]);
        
        return [
            'name' => $name . ' ' . fake()->randomNumber(4), // 避免重複
            'slug' => Str::slug($name . '-' . fake()->randomNumber(4)),
            'description' => fake()->sentence(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
} 