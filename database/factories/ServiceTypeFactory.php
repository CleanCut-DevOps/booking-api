<?php

namespace Database\Factories;

use App\Models\ServiceType;
use App\Models\TypeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_category_id' => TypeCategory::factory(),
            'label' => 'Service type label',
            'price' => fake()->numberBetween(50, 150) / 10,
            'quantifiable' => fake()->randomElement([true, false]),
            'available' => fake()->randomElement([true, false])
        ];
    }

    /**
     * Indicate whether the model should be quantifiable.
     *
     * @param bool $bool
     *
     * @return static
     */
    public function quantifiable(bool $bool = true): static
    {
        return $this->state(fn(array $attributes) => [
            'quantifiable' => $bool,
        ]);
    }

    /**
     * Indicate whether the model should be available.
     *
     * @param bool $bool
     *
     * @return static
     */
    public function available(bool $bool = true): static
    {
        return $this->state(fn(array $attributes) => [
            'available' => $bool,
        ]);
    }
}
