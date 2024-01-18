<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use App\Http\Requests\StoreJobTypeRequest;
use App\Http\Requests\UpdateJobTypeRequest;
use Illuminate\Http\JsonResponse;

class JobTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $jobTypes = JobType::all();
            return response()->json(['data' => $jobTypes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error ocurred while getting the job type list!'], 500);
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
