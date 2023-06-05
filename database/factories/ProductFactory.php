<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = collect(['sandals', 'boots', 'sneakers']);
        $category = $categories->random();
        // default SKU creation
        $sku = Str::of($category)->append(mt_rand(1000, 9999))->upper();
        $name = $this->faker->word;

        return [
            'name' => $name,
            'sku' => $sku,
            'category_id' => 1,
        ];
    }
}
