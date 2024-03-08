<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationLevelRequest;
use App\Http\Requests\UpdateEducationLevelRequest;
use App\Models\EducationLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of the education levels.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $educationLevels = EducationLevel::all();
        return response()->json($educationLevels);
    }

    /**
     * Display the specified education level.
     *
     * @param EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function show(EducationLevel $educationLevel): JsonResponse
    {
        return response()->json($educationLevel);
    }

    /**
     * Store a newly created education level in storage.
     *
     * @param StoreEducationLevelRequest $request
     * @return JsonResponse
     */
    public function store(StoreEducationLevelRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $educationLevel = EducationLevel::create($validatedData);
        return response()->json([
            'message' => 'Education level successfully created',
            $educationLevel, 201]);
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
        $validatedData = $request->validated();
        $educationLevel->update($validatedData);
        return response()->json($educationLevel, 200);
    }

    /**
     * Remove the specified education level from storage.
     *
     * @param EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function destroy(EducationLevel $educationLevel): JsonResponse
    {
        $educationLevel->delete();
        return response()->json(null, 204);
    }
}
