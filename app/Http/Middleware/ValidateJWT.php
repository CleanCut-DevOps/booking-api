<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ValidateJWT
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (JSONResponse|RedirectResponse|Response) $next
     * @return JSONResponse|RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next): RedirectResponse|JSONResponse|Response
    {
        try {
            $request['user_id'] = JWTAuth::getPayload(JWTAuth::getToken())['sub'];
        } catch (\Exception $e) {
            if (request()->header("Authorization")) {
                return response()->json([
                    "type" => "Unauthorized",
                    "message" => "Invalid authorization token"
                ], 401);
            } else {
                return response()->json([
                    "type" => "Unauthorized",
                    "message" => "This route requires authentication"
                ], 401);
            }
        }

        return $next($request);
    }
}
