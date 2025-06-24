<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'PHP', 'Laravel', 'Vue', 'JavaScript', 'MySQL', 'SQLite', 
            'Docker', 'GCP', 'API', 'Testing', 'Frontend', 'Backend'
        ]);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
} 