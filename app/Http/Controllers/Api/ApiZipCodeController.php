<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiZipCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zipCodes = ZipCode::all();
        return response()->json(['data' => $zipCodes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'code' => 'required|string|max:255|unique:zip_codes',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $zipCode = ZipCode::create($request->only('city_id', 'code'));

        return response()->json(['data' => $zipCode], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ZipCode $zipCode)
    {
        return response()->json(['data' => $zipCode], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ZipCode $zipCode)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zip_codes')->ignore($zipCode->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $zipCode->update($request->only('city_id', 'code'));

        return response()->json(['data' => $zipCode], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ZipCode $zipCode)
    {
        $zipCode->delete();
        return response()->json(['message' => 'Zip code deleted successfully'], 200);
    }
}