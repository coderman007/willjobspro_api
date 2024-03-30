<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255',
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
            'name' => 'required|string|max:255',
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
