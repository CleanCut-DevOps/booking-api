<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * a simple validation method for controller functions
     *
     * @param string $fn
     * @param array $rules
     * @param array $messages
     *
     * @return void
     */
    public function validate(string $fn, array $rules, array $messages = []): void
    {
        $this->middleware(function (Request $request, Closure $next) use ($rules, $messages) {
            try {
                $request->validate($rules, $messages);
            } catch (ValidationException $e) {
                $errors = collect($e->errors());

                return response()->json([
                    'type' => 'Invalid data',
                    'message' => $e->getMessage(),
                    'errors' => $errors->map(fn($e) => $e[0])
                ], 400);
            }

            return $next($request);
        })->only($fn);
    }
}
