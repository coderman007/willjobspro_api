<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBenefitRequest;
use App\Http\Requests\UpdateBenefitRequest;
use App\Models\Benefit;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $benefits = Benefit::paginate($perPage)->items();

            return response()->json([
                'message' => 'Benefits successfully retrieved',
                'data' => $benefits,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the benefits list!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBenefitRequest $request
     * @return JsonResponse
     */
    public function store(StoreBenefitRequest $request): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $benefit = Benefit::create($validatedData);

            return response()->json([
                'message' => 'Benefit successfully created',
                'data' => $benefit,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the benefit!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Benefit $benefit
     * @return JsonResponse
     */
    public function show(Benefit $benefit): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Benefit detail successfully retrieved',
                'data' => $benefit,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Benefit not found',
                'details' => 'The specified benefit ID does not exist in the database.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the benefit!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBenefitRequest $request
     * @param Benefit $benefit
     * @return JsonResponse
     */
    public function update(UpdateBenefitRequest $request, Benefit $benefit): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $benefit->update($validatedData);

            return response()->json([
                'message' => 'Benefit successfully updated',
                'data' => $benefit,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the benefit!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Benefit $benefit
     * @return JsonResponse
     */
    public function destroy(Benefit $benefit): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $benefit->delete();

            return response()->json([
                'message' => 'Benefit deleted',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the benefit!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
