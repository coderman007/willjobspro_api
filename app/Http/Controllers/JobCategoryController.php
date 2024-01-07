<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use App\Http\Requests\StoreJobCategoryRequest;
use App\Http\Requests\UpdateJobCategoryRequest;
use Illuminate\Http\JsonResponse;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $jobCategories = JobCategory::all();
            return response()->json(['data' => $jobCategories], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de categorías de trabajo.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJobCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreJobCategoryRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $jobCategory = JobCategory::create($validatedData);
            return response()->json(['data' => $jobCategory, 'message' => 'Categoría de trabajo creada con éxito.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la categoría de trabajo.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param JobCategory $jobCategory
     * @return JsonResponse
     */
    public function show(JobCategory $jobCategory): JsonResponse
    {
        try {
            return response()->json(['data' => $jobCategory], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la categoría de trabajo.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJobCategoryRequest $request
     * @param JobCategory $jobCategory
     * @return JsonResponse
     */
    public function update(UpdateJobCategoryRequest $request, JobCategory $jobCategory): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $jobCategory->update($validatedData);
            return response()->json(['data' => $jobCategory, 'message' => 'Categoría de trabajo actualizada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la categoría de trabajo.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param JobCategory $jobCategory
     * @return JsonResponse
     */
    public function destroy(JobCategory $jobCategory): JsonResponse
    {
        try {
            $jobCategory->delete();
            return response()->json(['message' => 'Categoría de trabajo eliminada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la categoría de trabajo.'], 500);
        }
    }
}
