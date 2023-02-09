<?php

namespace Database\Factories;

use App\Models\ServiceType;
use App\Models\ServiceTypeProducts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceTypeProducts>
 */
class ServiceTypeProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => 'Product label',
            'quantity' => null,
            'service_type_id' => ServiceType::factory(),
        ];
    }
}
