<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use App\Http\Requests\StoreJobTypeRequest;
use App\Http\Requests\UpdateJobTypeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobTypeController extends Controller
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
            $jobTypes = JobType::paginate($perPage);

            // Metadatos de paginación
            $paginationData = [
                'total' => $jobTypes->total(),
                'per_page' => $jobTypes->perPage(),
                'current_page' => $jobTypes->currentPage(),
                'last_page' => $jobTypes->lastPage(),
                'from' => $jobTypes->firstItem(),
                'to' => $jobTypes->lastItem(),
                'next_page_url' => $jobTypes->nextPageUrl(),
                'prev_page_url' => $jobTypes->previousPageUrl(),
                'path' => $jobTypes->path(),
                'data' => $jobTypes->items(),
                'links' => $jobTypes->render(),
            ];

            return response()->json(['data' => $jobTypes, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while getting the job type list!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJobTypeRequest $request
     * @return JsonResponse
     */
    public function store(StoreJobTypeRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $jobType = JobType::create($validatedData);
            return response()->json(['data' => $jobType, 'message' => 'Job type created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error ocurred while creating the job type!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param JobType $jobType
     * @return JsonResponse
     */
    public function show(JobType $jobType): JsonResponse
    {
        try {
            return response()->json(['data' => $jobType], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error ocurred while getting the job type!'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJobTypeRequest $request
     * @param JobType $jobType
     * @return JsonResponse
     */
    public function update(UpdateJobTypeRequest $request, JobType $jobType): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $jobType->update($validatedData);
            return response()->json(['data' => $jobType, 'message' => 'Job type updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error ocurred while updating the job type!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param JobType $jobType
     * @return JsonResponse
     */
    public function destroy(JobType $jobType): JsonResponse
    {
        try {
            $jobType->delete();
            return response()->json(['message' => 'Job type deleted!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error ocurred while deleting the job type!'], 500);
        }
    }
}
