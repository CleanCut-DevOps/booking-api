<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BelongsToAccount;
use App\Http\Middleware\ValidateJWT;
use App\Models\Booking;
use App\Models\Services;
use App\Models\ServiceType;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateJWT::class);
        $this->middleware(BelongsToAccount::class)->only(['show', 'update', 'destroy']);

        $this->validate('store', [
            'property_id' => ['required', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date'],
            'secondary_contact' => ['nullable', 'string', 'max:255'],
            'additional_information' => ['nullable', 'string'],
            'services' => ['array', 'min:1'],
            'services.*.id' => ['required_with:services', Rule::exists('service_types', 'id')],
            'services.*.quantity' => ['required_with:services', 'numeric', 'integer']
        ], [
            'property_id.required' => 'Property ID field is required',
            'property_id.string' => 'The property ID must be a string',
            'start_time.required' => 'Start time field is required',
            'start_time.date' => 'The start time must be a date',
            'end_time.required' => 'End time field is required',
            'end_time.date' => 'The end time must be a date',
            'secondary_contact.string' => 'The secondary contact must be a string',
            'secondary_contact.max' => 'The secondary contact must be less than 255 characters',
            'additional_information.string' => 'The additional information must be a string',
            'services.array' => 'The services field is invalid',
            'services.min' => 'The services field must contain at least one service',
            'services.*.id.required_with' => 'A service ID is required',
            'services.*.id.exists' => 'The service ID must exist',
            'services.*.quantity.required_with' => 'A service quantity is required',
            'services.*.quantity.numeric' => 'The service quantity must be a number',
            'services.*.quantity.integer' => 'The service quantity must be an integer'
        ]);
    }

    /**
     * Display a listing of bookings.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->requestPropertyAPI('property');

        $properties = collect($response->json()['properties'])->map(fn($p) => $p['id']);

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Bookings retrieved successfully',
            'bookings' => Booking::whereIn('property_id', $properties)->get()
        ]);
    }

    /**
     * Display a listing of bookings related to the property.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function indexByProperty(string $id): JsonResponse
    {
        $propertyID = request('id');

        $response = $this->requestPropertyAPI($propertyID);

        if ($response->failed()) {
            return response()->json([
                'type' => 'Not found',
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Property\'s bookings retrieved successfully',
            'bookings' => Booking::wherePropertyId($id)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $token = JWTAuth::getToken()->get();
        $property_api = config('services.property_api');
        $propertyID = request('property_id');

        $response = Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/$propertyID");

        if ($response->failed()) {
            return response()->json($response->json(), $response->status());
        }

        $booking = Booking::create($request->only(['property_id', 'start_time', 'end_time', 'secondary_contact', 'additional_information']));

        if ($request->has('services')) {
            foreach (request('services') as $service) {
                $serviceType = ServiceType::whereId($service['id'])->first();

                if ($serviceType->available) {
                    if ($serviceType->quantifiable) {
                        Services::create([
                            'booking_id' => $booking->id,
                            'type_id' => $service['id'],
                            'quantity' => $service['quantity']
                        ]);
                    } else {
                        Services::create([
                            'booking_id' => $booking->id,
                            'type_id' => $service['id'],
                            'quantity' => 1
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Booking created successfully',
            'booking' => $booking->refresh()
        ]);
    }

    public function requestPropertyAPI(string $path): PromiseInterface|Response
    {
        $token = JWTAuth::getToken()->get();
        $property_api = config('services.property_api');

        return Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/$path");
    }
}
