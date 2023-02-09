<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Services;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Services>
 */
class ServicesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'type_id' => ServiceType::factory(),
            'quantity' => fake()->randomElement([0, 1])
        ];
    }
}
