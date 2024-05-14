<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                  => $this->faker->name(),
            'cif'                   => $this->faker->lexify('?????????'),
            'description'           => $this->faker->sentence(),
            'email'                 => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('#########'),
            'address'               => $this->faker->address(),
            'location'              => $this->faker->city(),
            'zip_code'              => $this->faker->postcode(),
            'contact_name'          => $this->faker->name(),
            'contact_title'         => $this->faker->jobTitle(),
            'notes'                 => $this->faker->sentence(),
        ];
    }
}
