<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        // Get all existing IDs from the categories table
        $existingIds = Category::pluck('id')->toArray();

        // Add the 'null' option to the existing IDs
        $possibleIds = array_merge($existingIds, [null]);

        // Randomly select an ID from the list of possible IDs
        $parentId = $this->faker->randomElement($possibleIds);

        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'parent_id' => $parentId
        ];
    }
}
