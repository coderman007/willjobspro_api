<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            $idUser = $request->query('user_id', null);

            dd($perPage);


            // Construir la consulta para las ofertas de trabajo
            $jobsQuery = $this->buildJobQuery($request);

            // Paginar las ofertas de trabajo
            $jobs = $jobsQuery->paginate($perPage);

            // Iterar sobre las ofertas de trabajo para añadir datos adicionales
            foreach ($jobs as $job) {
                // Calcular si el usuario ha aplicado a esta oferta de trabajo
                $job->setAttribute('applied', $job->applications->count() > 0);

                // Obtener los nombres de los tipos de trabajo relacionados
                $job->setAttribute('job_types', $job->jobTypes->pluck('name')->implode(', '));

                // Obtener los nombres de los idiomas relacionados
                $job->setAttribute('languages', $job->languages->pluck('name')->implode(', '));

                // Obtener los nombres de los niveles de educación relacionados
                $job->setAttribute('education_levels', $job->educationLevels->pluck('name')->implode(', '));
            }

            // Retornar las ofertas de trabajo paginadas junto con datos adicionales
            return $this->jsonResponse(JobResource::collection($jobs), 'Job offers retrieved successfully!', 200)
                ->header('X-Total-Count', $jobs->total());
        } catch (\Exception $e) {
            // Manejar cualquier error y retornar una respuesta de error
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
        // Inicializar la consulta con las relaciones necesarias
        $query = Job::with(['company', 'jobCategory', 'applications', 'jobTypes', 'languages', 'educationLevels']);

        // Aplicar filtros basados en los parámetros de la solicitud
        $query->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->query('search');
            return $query->where('title', 'like', '%' . $searchTerm . '%');
        });

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
            // Validar los datos de la solicitud
            $validatedData = $request->validated();

            // Obtener el usuario autenticado y asegurarse de que sea una empresa
            $user = auth()->user();
            if (!$user || !$user->hasRole('company')) {
                return $this->jsonErrorResponse('Only users with role Company can create job offers.', 403);
            }

            // Obtener el ID de la empresa del usuario autenticado
            $companyId = $user->company->id;

            // Asignar el ID de la empresa a los datos validados
            $validatedData['company_id'] = $companyId;

            // Crear la oferta de trabajo con los datos validados
            $job = Job::create($validatedData);

            // Obtener las cadenas de texto de habilidades, niveles de estudio e idiomas del formulario y convertirlas en arrays
            $jobTypeIds = $request->input('job_type_ids') ? explode(',', $request->input('job_type_ids')) : [];
            $educationLevels = $request->input('education_levels') ? explode(',', $request->input('education_levels')) : [];
            $languages = $request->input('languages') ? explode(',', $request->input('languages')) : [];

            // Asociar los tipos de trabajo, niveles de estudio e idiomas a la oferta de trabajo recién creada
            $job->jobTypes()->attach($jobTypeIds);
            $job->educationLevels()->attach($educationLevels);
            $job->languages()->attach($languages);

            // Cargar las relaciones asociadas a la oferta de trabajo
            $job->load('jobTypes', 'educationLevels', 'languages');

            // Devolver una respuesta exitosa con la oferta de trabajo creada
            return $this->jsonResponse($job, 'Job offer created successfully', 201);
        } catch (\Exception $e) {
            // Manejar cualquier error y devolver una respuesta de error
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
