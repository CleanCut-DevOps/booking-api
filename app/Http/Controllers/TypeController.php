<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;

class TypeController extends Controller
{
    /**
     * Display a listing of the service types.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $serviceTypes = ServiceType::orderBy('label')->get();

        return response()->json([
            'type' => 'Successful request',
            'message' => 'Displaying attributes of all types of services',
            'serviceTypes' => $serviceTypes
        ]);
    }

    /**
     * Display the specified property type.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $propertyType = ServiceType::whereId($id);

        if ($propertyType->exists()) {
            return response()->json([
                'type' => 'Successful request',
                'message' => 'Displaying attributes of this type of property',
                'serviceType' => $propertyType->first()
            ]);
        } else {
            return response()->json([
                'type' => 'Not found',
                'message' => 'The property type with the given ID does not exist'
            ], 404);
        }
    }
}
