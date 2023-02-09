<?php

namespace Tests\Feature\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Construct a request with a valid token, additionally, created properties if needed
     *
     * @return self
     */
    public function withAuth(): self
    {
        $account_api = config('services.account_api');

        $response = Http::post("$account_api/user/login", [
            'email' => 'test@domain.com',
            'password' => 'Passw0rd',
            'remember' => false
        ]);

        if ($response->failed()) {
            $this->fail('Failed to login to account API');
        } else {
            $token = $response->json()['token'];

            return $this->withHeaders(['Authorization' => "Bearer $token"]);
        }
    }

    /**
     * Get a property from the property API
     *
     * @return array
     */
    public function getProperties(): array
    {
        $account_api = config('services.account_api');

        $property_api = config('services.property_api');

        $user = Http::post("$account_api/user/login", [
            'email' => 'test@domain.com',
            'password' => 'Passw0rd',
            'remember' => false
        ]);

        if ($user->failed()) $this->fail('Failed to login to account API');

        $token = $user->json()['token'];

        $response = Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/property");

        if ($response->failed()) $this->fail('Failed to get properties from property API');

        return $response->json()['properties'];
    }

    // Get all bookings

    /**
     * Feature test to get all bookings with valid credentials.
     *
     * @return void
     */
    public function test_get_all_bookings(): void
    {
        $response = $this->withAuth()->get('/bookings');

        $response->assertOk()->assertJsonStructure([
            'type',
            'message',
            'bookings' => [
                '*' => [
                    'id',
                    'start_time',
                    'end_time',
                    'secondary_contact',
                    'additional_information',
                    'cleaner_remarks',
                    'rejected_at',
                    'complete_at',
                    'created_at',
                    'updated_at',
                    'services' => [
                        '*' => [
                            'quantity',
                            'type' => [
                                'id',
                                'label',
                                'category',
                                'products' => [
                                    '*' => [
                                        'label',
                                        'quantity'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Feature test to get all bookings with invalid credentials.
     *
     * @return void
     */
    public function test_get_all_bookings_with_invalid_credentials(): void
    {
        $response = $this->get('/bookings');

        $response->assertStatus(401);
    }

    // Get specific booking

    /**
     * Feature test to get a booking with valid credentials.
     *
     * @return void
     */
    public function test_get_a_booking(): void
    {
        $get = $this->withAuth()->get('/bookings');

        $response = $this->withAuth()->get("/{$get->json()['bookings'][0]['id']}");

        $response->assertOk()->assertJsonStructure([
            'type',
            'message',
            'booking' => [
                'id',
                'start_time',
                'end_time',
                'secondary_contact',
                'additional_information',
                'cleaner_remarks',
                'rejected_at',
                'complete_at',
                'created_at',
                'updated_at',
                'services' => [
                    '*' => [
                        'quantity',
                        'type' => [
                            'id',
                            'label',
                            'category',
                            'products' => [
                                '*' => [
                                    'label',
                                    'quantity'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Feature test to get a booking with invalid credentials.
     *
     * @return void
     */
    public function test_get_a_booking_with_invalid_credentials(): void
    {
        $id = fake()->uuid();

        $response = $this->get("/$id");

        $response->assertStatus(401);
    }

    /**
     *  Feature test to get a booking with invalid credentials.
     *
     * @return void
     */
    public function test_get_a_booking_with_invalid_id(): void
    {
        $id = fake()->uuid();

        $response = $this->withAuth()->get("/$id");

        $response->assertNotFound();
    }

    // Create a booking

    /**
     * Feature test to create a booking with valid credentials.
     *
     * @return void
     */
    public function test_create_a_booking(): void
    {
        $property = $this->getProperties();

        $types = $this->get('/types');

        $response = $this->withAuth()->post('/bookings', [
            'property_id' => $property[0]['id'],
            'start_time' => Carbon::now()->addDays(1)->setTime(7, 0),
            'end_time' => Carbon::now()->addDays(1)->setTime(19, 0),
            'services' => [
                [
                    'id' => $types['serviceTypes'][0]['id'],
                    'quantity' => 3,
                ],
                [
                    'id' => $types['serviceTypes'][2]['id'],
                    'quantity' => 3,
                ],
                [
                    'id' => $types['serviceTypes'][4]['id'],
                    'quantity' => 3,
                ],
            ]
        ]);

        $response->assertCreated()->assertJsonStructure([
            'type',
            'message'
        ]);
    }

    /**
     * Feature test to create a booking with invalid credentials.
     *
     * @return void
     */
    public function test_create_a_booking_with_invalid_credentials(): void
    {
        $response = $this->post('/bookings');

        $response->assertUnauthorized();
    }

    /**
     * Feature test to create a booking with invalid data.
     *
     * @return void
     */
    public function test_create_a_booking_with_invalid_data(): void
    {
        $property = $this->getProperties();

        $response = $this->withAuth()->post('/bookings', [
            'property_id' => $property[0]['id'],
            'start_time' => Carbon::now()->addDays(1)->setTime(7, 0),
            'end_time' => Carbon::now()->addDays(1)->setTime(19, 0),
            'services' => [
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
            ]
        ]);

        $response->assertBadRequest();
    }

    // Update a booking

    /**
     * Feature test to update a booking with valid credentials.
     *
     * @return void
     */
    public function test_update_a_booking(): void
    {
        $get = $this->withAuth()->get('/bookings');

        $response = $this->withAuth()->put("/{$get->json()['bookings'][0]['id']}", [
            'start_time' => Carbon::now()->addDays(1)->setTime(10, 0),
            'end_time' => Carbon::now()->addDays(1)->setTime(15, 0),
        ]);

        $response->assertOk()->assertJsonStructure([
            'type',
            'message'
        ]);
    }

    /**
     * Feature test to update a booking with invalid credentials.
     *
     * @return void
     */
    public function test_update_a_booking_with_invalid_credentials(): void
    {
        $id = fake()->uuid();

        $response = $this->put("/$id");

        $response->assertUnauthorized();
    }

    /**
     * Feature test to update a booking with invalid data.
     *
     * @return void
     */
    public function test_update_a_booking_with_invalid_data(): void
    {
        $get = $this->withAuth()->get('/bookings');

        $response = $this->withAuth()->put("/{$get->json()['bookings'][0]['id']}", [
            'start_time' => "start time",
            'end_time' => "end time",
            'services' => [
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
                [
                    'id' => fake()->uuid(),
                    'quantity' => 3,
                ],
            ]
        ]);

        $response->assertBadRequest();
    }

    // Delete a booking

    /**
     * Feature test to delete a booking with valid credentials.
     *
     * @return void
     */
    public function test_delete_a_booking(): void
    {
        $get = $this->withAuth()->get('/bookings');

        $response = $this->withAuth()->delete("/{$get->json()['bookings'][0]['id']}");

        $response->assertOk()->assertJsonStructure([
            'type',
            'message'
        ]);
    }

    /**
     * Feature test to delete a booking with invalid credentials.
     *
     * @return void
     */
    public function test_delete_a_booking_with_invalid_credentials(): void
    {
        $id = fake()->uuid();

        $response = $this->delete("/$id");

        $response->assertUnauthorized();
    }

    /**
     * Feature test to delete a booking with invalid id.
     *
     * @return void
     */
    public function test_delete_a_booking_with_invalid_id(): void
    {
        $id = fake()->uuid();

        $response = $this->withAuth()->delete("/$id");

        $response->assertNotFound();
    }
}
