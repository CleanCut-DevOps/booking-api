<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class BelongsToAccount
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse|JsonResponse) $next
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $booking = Booking::whereId(request('id'));

        if ($booking->exists()) {
            $token = JWTAuth::getToken()->get();
            $property_api = config('services.property_api');
            $propertyID = $booking->first()->property_id;

            $response = Http::withHeaders(['Authorization' => "Bearer $token"])->get("$property_api/$propertyID");

            if ($response->failed()) {
                return response()->json($response->json(), $response->status());
            }


            if ($response->json()['property']['user_id'] != request('user_id')) {
                return response()->json([
                    'type' => 'Unauthorized',
                    'message' => 'You are not authorized to access this resource'
                ], 401);
            }

            return $next($request);

        } else {
            return response()->json([
                'type' => 'Not found',
                'message' => 'Booking not found'
            ], 404);
        }
    }
}
