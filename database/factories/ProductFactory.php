<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->sentence(4),
            'slug' => fake()->slug(1),
            'price' => fake()->randomFloat(2, 1, 100),
            'quantity' => fake()->randomNumber(3),
        ];
    }
}
