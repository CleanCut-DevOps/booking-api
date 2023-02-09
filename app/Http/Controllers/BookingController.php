<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BelongsToAccount;
use App\Http\Middleware\ValidateJWT;
use App\Models\Booking;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateJWT::class);
        $this->middleware(BelongsToAccount::class)->only(['show', 'update', 'destroy']);
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

    public function requestPropertyAPI(string $path): PromiseInterface|Response
    {
        $token = JWTAuth::getToken()->get();
        $property_api = config('services.property_api');

        return Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/$path");
    }
}
