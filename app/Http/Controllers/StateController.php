<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = State::all();
        return response()->json(['data' => $states], 200);
    }

    public function getStatesByCountry($countryId): JsonResponse
    {
        $validator = Validator::make(['country_id' => $countryId], [
            'country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $states = State::where('country_id', $countryId)->get();
        return response()->json(['data' => $states], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255|unique:states',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $state = State::create($request->only('country_id', 'name'));

        return response()->json(['data' => $state], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return response()->json(['data' => $state], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')->ignore($state->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $state->update($request->only('country_id', 'name'));

        return response()->json(['data' => $state], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();
        return response()->json(['message' => 'State deleted successfully'], 200);
    }
}
