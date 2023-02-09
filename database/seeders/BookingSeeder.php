<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Services;
use App\Models\ServiceType;
use Database\Factories\BookingFactory;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $account_api = config('services.account_api');

        $property_api = config('services.property_api');

        $login = Http::post("$account_api/user/login", [
            'email' => 'test@domain.com',
            'password' => 'Passw0rd',
            'remember' => false
        ]);

        if ($login->failed()) throw new Exception('Failed to login to account API');

        $token = $login->json()['token'];

        $getProps = Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/property");

        if ($getProps->failed()) throw new Exception('Failed to get properties from property API');

        $properties = collect($getProps->json()['properties']);

        $serviceTypes = ServiceType::get();

        foreach ($properties as $property) {
            $startTime = Carbon::now()->addDays(7)->setTime(10, 0)->addDays(7);
            $endTime = Carbon::now()->addDays(7)->setTime(15, 0)->addDays(7);

            $booking1 = Booking::factory()->create([
                'property_id' => $property['id'],
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);

            $booking2 = Booking::factory()->create([
                'property_id' => $property['id'],
                'start_time' => $startTime->addDays(7),
                'end_time' => $endTime->addDays(7)
            ]);

            foreach ($serviceTypes as $serviceType) {
                $run = fake()->boolean();

                if ($run && $serviceType->available) {
                    Services::factory()->create([
                        'booking_id' => $booking1->id,
                        'type_id' => $serviceType->id,
                        'quantity' => $serviceType->quantifiable ? fake()->numberBetween(1, 5) : 1
                    ]);
                }
            }

            foreach ($serviceTypes as $serviceType) {
                $run = fake()->boolean();

                if ($run && $serviceType->available) {
                    Services::factory()->create([
                        'booking_id' => $booking2->id,
                        'type_id' => $serviceType->id,
                        'quantity' => $serviceType->quantifiable ? fake()->numberBetween(1, 5) : 1
                    ]);
                }
            }
        }
    }
}
