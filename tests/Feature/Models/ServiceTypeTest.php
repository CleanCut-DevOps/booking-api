<?php

namespace Tests\Feature\Models;

use App\Models\ServiceType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceTypeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Feature test for getting all service types.
     *
     * @return void
     */
    public function test_get_all_service_types(): void
    {
        $response = $this->get('/types');

        $response->assertOk()->assertJsonStructure([
            'type',
            'message',
            'serviceTypes' => [
                '*' => [
                    'id',
                    'label',
                    'price',
                    'available',
                    'quantifiable',
                    'created_at',
                    'updated_at',
                    'products' => [
                        '*' => [
                            'label',
                            'quantity'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Feature test for getting a service type.
     *
     * @return void
     */
    public function test_get_service_type(): void
    {
        $id = ServiceType::first()->id;

        $response = $this->get("/types/$id");

        $response->assertOk()->assertJsonStructure([
            'type',
            'message',
            'serviceType' => [
                'id',
                'label',
                'price',
                'available',
                'quantifiable',
                'created_at',
                'updated_at',
                'products' => [
                    '*' => [
                        'label',
                        'quantity'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Feature test for getting a service type that doesn't exist or is blocked.
     *
     * @return void
     */
    public function test_get_invalid_service_type(): void
    {
        $id = fake()->uuid();

        $response = $this->get("/types/$id");

        $response->assertNotFound()->assertJsonStructure([
            'type',
            'message'
        ]);
    }
}
