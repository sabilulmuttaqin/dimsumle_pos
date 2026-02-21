<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'price' => fake()->numberBetween(5000, 50000),
            'stock' => fake()->numberBetween(0, 100),
            'image' => null,
        ];
    }
}
