<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
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
            $userId = Auth::id();

            $jobs = $this->buildJobQuery($request)->paginate($perPage);

            foreach ($jobs as $job) {
                $job->setAttribute('applied', $job->applications->count() > 0);
                $job->setAttribute('job_types', $job->jobTypes->pluck('name')->implode(', '));
            }

            return $this->jsonResponse(JobResource::collection($jobs), 'Job offers retrieved successfully!', 200)
                ->header('X-Total-Count', $jobs->total());
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving jobs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Build the job query based on the request filters.
     *
     * @param Request $request
     * @return Builder
     */
    private function buildJobQuery(Request $request): Builder
    {
        $query = Job::with(['company', 'jobCategory', 'jobTypes', 'educationLevel', 'subscriptionPlan']);

        $query->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->query('search');
            return $query->where('title', 'like', '%' . $searchTerm . '%');
        });

        $filters = [
            'company_id', 'job_category_id', 'job_type_id', 'education_level_id', 'subscription_plan_id', 'title', 'description', 'status', 'location',
        ];

        foreach ($filters as $filter) {
            $query->when($request->filled($filter), function ($query) use ($request, $filter) {
                return $query->where($filter, $request->query($filter));
            });
        }

        $query->when($request->filled('sort_by') && $request->filled('sort_order'), function ($query) use ($request) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            return $query->orderBy($sortBy, $sortOrder);
        }, function ($query) {
            // Default order if not specified
            return $query->orderBy('created_at', 'desc');
        });

        return $query;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJobRequest $request
     * @return JsonResponse
     */

    public function store(StoreJobRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            if (!$user || !$user->hasRole('company')) {
                return $this->jsonErrorResponse('Only users with role Company can create job offers.', 403);
            }

            $companyId = $user->company->id;
            $validatedData['company_id'] = $companyId;

            $job = Job::create($validatedData);
            $jobTypeIds = $request->input('job_type_ids', []);
            $job->jobTypes()->attach($jobTypeIds);

            $job->load('jobTypes');

            return $this->jsonResponse($job, 'Job offer created successfully', 201);
        } catch (QueryException $e) {
            return $this->jsonErrorResponse('An error occurred in the database while creating the job offer.', 500);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('An error occurred while creating the job offer.', 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function show(Job $job): JsonResponse
    {
        try {
            $job->load(['applications', 'jobCategory', 'jobTypes']); // Cargar relaciones
            $numApplications = $job->applications->count();
            $transformedJob = [
                'job' => new JobResource($job),
                'num_applications' => $numApplications,
            ];

            return $this->jsonResponse($transformedJob, 'Job offer detail obtained successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->jsonErrorResponse('Job not found.', 404);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving job details: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJobRequest $request
     * @param Job $job
     * @return JsonResponse
     */
    public function update(UpdateJobRequest $request, Job $job): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $companyId = $request->user()->company->id;

            if ($job->company_id !== $companyId) {
                return $this->jsonErrorResponse('You do not have permissions to perform this action on this resource.', 403);
            }

            $job->update($validatedData);

            $job->jobTypes()->sync($request->input('job_type_ids', []));

            return $this->jsonResponse($job->fresh()->load('jobTypes'), 'Job offer updated successfully!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error updating the job offer: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function destroy(Job $job): JsonResponse
    {
        try {
            $companyId = auth()->user()->company->id;

            if ($job->company_id !== $companyId) {
                return $this->jsonErrorResponse('You do not have permissions to perform this action on this resource.', 403);
            }

            $job->delete();

            return $this->jsonResponse(null, 'Job offer deleted successfully!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error deleting the job offer: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Function to generate a consistent JSON response.
     *
     * @param mixed $data The data to include in the response
     * @param string|null $message The response message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonResponse(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Function to generate a consistent JSON error response.
     *
     * @param string|null $message The error message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonErrorResponse(?string $message = null, int $status = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        return response()->json($response, $status);
    }

}
