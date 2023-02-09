<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseTime = Carbon::now()->setTime(10, 0)->addDays(7);

        return [
            'start_time' => $baseTime,
            'cleaner_id' => fake()->uuid(),
            'property_id' => fake()->uuid(),
            'end_time' => $baseTime->addHours(5)
        ];
    }

    /**
     * Indicate the model's cleaner_id.
     *
     * @param string $property_id
     * @param string $cleaner_id
     *
     * @return static
     */
    public function id(string $property_id, string $cleaner_id = ""): static
    {
        return $this->state(fn(array $attributes) => [
            'cleaner_id' => $cleaner_id,
            '$property_id' => $property_id,
        ]);
    }
}
