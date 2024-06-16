<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationLevelRequest;
use App\Http\Requests\UpdateEducationLevelRequest;
use App\Models\EducationLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiEducationLevelController extends Controller
{
    /**
     * Display a listing of the education levels.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $educationLevels = EducationLevel::paginate($perPage)->items();

            return response()->json([
                'message' => 'Education levels successfully retrieved',
                'data' => $educationLevels
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving education levels',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified education level.
     *
     * @param EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function show(EducationLevel $educationLevel): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Education level detail successfully retrieved',
                'data' => $educationLevel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving education level detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created education level in storage.
     *
     * @param StoreEducationLevelRequest $request
     * @return JsonResponse
     */
    public function store(StoreEducationLevelRequest $request): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $educationLevel = EducationLevel::create($validatedData);

            return response()->json([
                'message' => 'Education level successfully created',
                'data' => $educationLevel
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating education level',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified education level in storage.
     *
     * @param UpdateEducationLevelRequest $request
     * @param EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function update(UpdateEducationLevelRequest $request, EducationLevel $educationLevel): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $educationLevel->update($validatedData);

            return response()->json([
                'message' => 'Education level successfully updated',
                'data' => $educationLevel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating education level',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified education level from storage.
     *
     * @param EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function destroy(EducationLevel $educationLevel): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $educationLevel->delete();

            return response()->json([
                'message' => 'Education level deleted',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting education level',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
