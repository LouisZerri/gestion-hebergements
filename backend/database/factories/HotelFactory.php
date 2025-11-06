<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hotel',
            'address_1' => fake()->streetAddress(),
            'address_2' => fake()->optional()->secondaryAddress(),
            'zip_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => 'France',
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'description' => fake()->optional()->paragraph(3),
            'max_capacity' => fake()->numberBetween(10, 200),
            'price_per_night' => fake()->randomFloat(2, 50, 500),
        ];
    }
}