<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $existingCategoriesIds = Category::pluck('id')->toArray();
        $categoryId = $this->faker->randomElement($existingCategoriesIds);

        $existingSuppliersIds = Category::pluck('id')->toArray();
        $supplierId = $this->faker->randomElement($existingSuppliersIds);

        return [      
            'name'                  => $this->faker->sentence(),
            'sku_code'              => $this->faker->unique()->bothify('??-###'),
            'description'           => $this->faker->paragraph(),
            'price'                 => $this->faker->randomFloat(2, 10, 1000),
            'cost_price'            => $this->faker->randomFloat(2, 5, 500),
            'status'                => $this->faker->boolean(),
            'additional_features'   => json_encode(['color' => $this->faker->colorName(), 'weight' => $this->faker->randomNumber(2)]),
            'category_id'           => $categoryId,
            'supplier_id'           => $supplierId
        ];
    }
}
