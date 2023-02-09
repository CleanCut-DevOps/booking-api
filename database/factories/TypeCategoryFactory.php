<?php

namespace Database\Factories;

use App\Models\TypeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TypeCategory>
 */
class TypeCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => 'Category label'
        ];
    }

    /**
     * Indicate the model's label.
     *
     * @param string $label
     *
     * @return static
     */
    public function label(string $label): static
    {
        return $this->state(fn(array $attributes) => [
            'label' => $label,
        ]);
    }
}
