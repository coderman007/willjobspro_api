<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        return response()->json(['data' => $cities], 200);
    }

    public function getCitiesByState($stateId): JsonResponse
    {
        // Validar que el estado exista en la base de datos
        $validator = Validator::make(['state_id' => $stateId], [
            'state_id' => 'required|exists:states,id',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener las ciudades asociadas al estado especificado
        $cities = City::where('state_id', $stateId)->get();

        // Devolver las ciudades en formato JSON
        return response()->json(['data' => $cities], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255|unique:cities',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $city = City::create($request->only('state_id', 'name'));

        return response()->json(['data' => $city], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return response()->json(['data' => $city], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities')->ignore($city->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $city->update($request->only('state_id', 'name'));

        return response()->json(['data' => $city], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        return response()->json(['message' => 'City deleted successfully'], 200);
    }
}
