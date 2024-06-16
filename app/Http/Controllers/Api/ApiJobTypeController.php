<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobTypeRequest;
use App\Http\Requests\UpdateJobTypeRequest;
use App\Models\JobType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiJobTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $jobTypes = JobType::paginate($perPage)->items();

            return response()->json([
                'message' => 'Job types successfully retrieved',
                'data' => $jobTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the job types list!',
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
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

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
            // La instancia de $jobType ya estará disponible si se encuentra en la base de datos
            return response()->json([
                'message' => 'Job type detail successfully retrieved',
                'data' => $jobType,
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Maneja el caso en el que el ID no existe en la base de datos
            return response()->json([
                'error' => 'Job type not found',
                'details' => 'The specified job type ID does not exist in the database.',
            ], 404);
        } catch (\Exception $e) {
            // Maneja cualquier otro error que pueda ocurrir
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
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

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
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

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
