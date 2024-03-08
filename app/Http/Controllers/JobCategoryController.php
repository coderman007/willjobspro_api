<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobCategoryRequest;
use App\Http\Requests\UpdateJobCategoryRequest;
use App\Models\JobCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobCategoryController extends Controller
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
            $perPage = $request->query('per_page', 10); // Obtener el número de elementos por página
            $jobCategories = JobCategory::all();

            // Metadatos de paginación
            $paginationData = [
                'total' => $jobCategories->total(),
                // 'per_page' => $jobCategories->perPage(),
                // 'current_page' => $jobCategories->currentPage(),
                // 'last_page' => $jobCategories->lastPage(),
                // 'from' => $jobCategories->firstItem(),
                // 'to' => $jobCategories->lastItem(),
                // 'next_page_url' => $jobCategories->nextPageUrl(),
                // 'prev_page_url' => $jobCategories->previousPageUrl(),
                // 'path' => $jobCategories->path(),
                // 'data' => $jobCategories->items(),
                // 'links' => $jobCategories->render(),
            ];

            return response()->json([
                'message' => 'Job categories successfully retrieved',
                'data' => $jobCategories],
                200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the job category list!',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Job category successfully created',
                'data' => $jobCategory],
                201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while creating the job category!',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Job category detail successfully retrieved',
                'data' => $jobCategory],
                200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while getting the job category!',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Job category successfully updated',
                'data' => $jobCategory],
                200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while updating the job category!',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Job category deleted',
                'data' => $jobCategory],
                200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while deleting the job category!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
