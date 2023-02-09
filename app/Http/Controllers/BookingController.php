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
            'services.*.quantity' => ['required_with:services', 'numeric', 'integer', 'min:1']
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
            'services.*.quantity.integer' => 'The service quantity must be an integer',
            'services.*.quantity.min' => 'The service quantity must be at least 1'
        ]);

        $this->validate('update', [
            'start_time' => ['date'],
            'end_time' => ['date'],
            'secondary_contact' => ['nullable', 'string', 'max:255'],
            'additional_information' => ['nullable', 'string'],
            'services' => ['array', 'min:1'],
            'services.*.id' => ['required_with:services', Rule::exists('service_types', 'id')],
            'services.*.quantity' => ['nullable', 'numeric', 'integer', 'min:1']
        ], [
            'start_time.date' => 'The start time must be a date',
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
            'services.*.quantity.integer' => 'The service quantity must be an integer',
            'services.*.quantity.min' => 'The service quantity must be at least 1'
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
        if ($request->has('services')) {
            $errors = [];

            foreach (request('services') as $service) {
                $serviceTypeID = $service['id'];

                if (!ServiceType::whereId($serviceTypeID)->first()->available) {
                    $index = array_search($service, request('services'));

                    $errors["services.$index.id"] = "The service type $serviceTypeID is not available.";
                }
            }

            if (count($errors) > 0) {
                return response()->json([
                    'type' => 'Invalid data',
                    'message' => 'Some service type(s) are invalid.',
                    'errors' => $errors
                ], 400);
            }
        }

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
            'message' => 'Booking created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $booking = Booking::whereId($id)->first();

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Booking retrieved successfully',
            'booking' => $booking
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $booking = Booking::whereId($id)->first();

        if ($request->has('services')) {
            $errors = [];

            foreach (request('services') as $service) {
                $serviceTypeID = $service['id'];

                if (!ServiceType::whereId($serviceTypeID)->first()->available) {
                    $index = array_search($service, request('services'));

                    $errors["services.$index.id"] = "The service type $serviceTypeID is not available.";
                }
            }

            if (count($errors) > 0) {
                return response()->json([
                    'type' => 'Invalid data',
                    'message' => 'Some service type(s) are invalid.',
                    'errors' => $errors
                ], 400);
            }

            foreach (request('services') as $service) {
                $serviceType = ServiceType::whereId($service['id'])->first();
                $requestedService = Services::whereBookingId($booking->id)->whereTypeId($serviceType->id);


                if ($service['quantity']) {
                    if ($requestedService->exists()) {
                        // Updates

                        if ($serviceType->quantifiable) {
                            $requestedService->update(['quantity' => $service['quantity']]);
                        } else {
                            $requestedService->update(['quantity' => 1]);
                        }
                    } else {
                        // Creates

                        if ($serviceType->quantifiable) {
                            Services::create([
                                'type_id' => $service['id'],
                                'booking_id' => $booking->id,
                                'quantity' => $service['quantity']
                            ]);
                        } else {
                            Services::create([
                                'quantity' => 1,
                                'type_id' => $service['id'],
                                'booking_id' => $booking->id
                            ]);
                        }
                    }
                } else {
                    if ($requestedService->exists()) $requestedService->delete();
                }
            }
        }

        $booking->update($request->only(['start_time', 'end_time', 'secondary_contact', 'additional_information']));

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Booking updated successfully',
            'booking' => $booking->refresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        Booking::whereId($id)->first()->delete();

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Booking deleted successfully'
        ]);
    }

    public function requestPropertyAPI(string $path): PromiseInterface|Response
    {
        $token = JWTAuth::getToken()->get();
        $property_api = config('services.property_api');

        return Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/$path");
    }
}
