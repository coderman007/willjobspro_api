<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobTypeRequest;
use App\Http\Requests\UpdateJobTypeRequest;
use App\Models\JobType;
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

            return response()->json([
                'message' => 'Job types successfully retrieved',
                'data' => $jobTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the job type list!',
                'details' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'message' => 'Job type successfully created',
                'data' => $jobType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the job type!',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Job type detail successfully retrieved',
                'data' => $jobType,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the job type!',
                'details' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'message' => 'Job type successfully updated',
                'data' => $jobType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the job type!',
                'details' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'message' => 'Job type deleted',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the job type!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
