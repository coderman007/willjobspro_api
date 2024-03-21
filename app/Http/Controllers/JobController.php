<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
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

            // Obtener el ID del usuario
            $userId = Auth::id();

            // Consulta Eloquent para obtener las ofertas de trabajo
            $jobs = Job::with([
                'applications' => function ($query) use ($userId) {
                    $query->where('candidate_id', $userId);
                },
                'jobTypes',
            ]);

            // Filtrar por búsqueda, si se proporciona
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $jobs->where('title', 'like', '%' . $searchTerm . '%');
            }

            // Filtrar por otros criterios
            $filters = [
                'company_id', 'job_category_id', 'job_type_id', 'education_level_id', 'subscription_plan_id', 'title', 'description', 'status', 'location',
            ];

            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $jobs->where($filter, $request->query($filter));
                }
            }

            // Ordenar por criterio especificado o por defecto
            if ($request->filled('sort_by') && $request->filled('sort_order')) {
                $sortBy = $request->query('sort_by');
                $sortOrder = $request->query('sort_order');
                $jobs->orderBy($sortBy, $sortOrder);
            } else {
                $jobs->orderBy('created_at', 'desc');
            }

            // Obtener la colección de ofertas de trabajo
            $jobs = $jobs->paginate($perPage);

            // Agregar atributo "applied" a cada oferta
            foreach ($jobs as $job) {
                $job->setAttribute('applied', $job->applications->count() > 0);

                // Transformar la información de los tipos de trabajo
                $job->setAttribute('job_types', $job->jobTypes->pluck('name')->implode(', '));
            }

            return $this->jsonResponse(JobResource::collection($jobs), 'Job offers retrieved successfully!', 200)
                ->header('X-Total-Count', $jobs->total());
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving jobs: ' . $e->getMessage(), 500);
        }
    }


    private function buildJobQuery(Request $request)
    {
        $query = Job::with(['company', 'jobCategory', 'jobTypes', 'educationLevel', 'subscriptionPlan']);

        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $filters = [
            'company_id', 'job_category_id', 'job_type_id', 'education_level_id', 'subscription_plan_id', 'title', 'description', 'status', 'location',
        ];

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Orden por defecto si no se especifica
            $query->orderBy('created_at', 'desc');
        }

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
            // Validación y creación del trabajo
            $validatedData = $request->validated();

            // Verificar si el 'company_id' en la solicitud coincide con el del usuario autenticado
            $companyId = $request->input('company_id');

            if (!$this->userOwnsCompany($companyId)) {
                return $this->jsonErrorResponse('You do not have permissions to perform this action on this resource.', 403);
            }

            // Verificar si se proporcionó un plan de suscripción
            $subscriptionPlanId = $request->input('subscription_plan_id', null);

            // Lógica para asignar el plan de suscripción por defecto si no se proporciona uno
            if (is_null($subscriptionPlanId)) {
                // Asignar el ID del plan básico (ajusta esto según tu lógica y estructura de datos)
                $defaultSubscriptionPlanId = 1; // Por ejemplo, el ID del plan básico
                $validatedData['subscription_plan_id'] = $defaultSubscriptionPlanId;
            }

            // Crear el trabajo con los datos validados
            $job = Job::create($validatedData);

            // Asociar los tipos de trabajo proporcionados
            $jobTypeIds = $request->input('job_type_ids', []);
            $job->jobTypes()->attach($jobTypeIds);

            $job->load('jobTypes');

            return $this->jsonResponse($job, 'Job offer created successfully', 201);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return $this->jsonErrorResponse('An error occurred in the database while creating the job offer.' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('An error occurred while creating the job offer.' . $e->getMessage(), 500);
        }
    }


    /**
     * Check if the authenticated user owns the company with the given ID.
     *
     * @param int $companyId
     * @return bool
     */
    protected function userOwnsCompany($companyId)
    {
        $user = auth()->user();

        // Verificar si el usuario está autenticado y tiene el rol 'company'
        if ($user && $user->hasRole('company')) {
            // Obtener la compañía asociada al usuario
            $userCompany = $user->company;

            // Verificar si la compañía existe y su ID coincide con $companyId
            return $userCompany && $userCompany->id == $companyId;
        }

        return false;
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            // Recuperar el trabajo por su ID con la relación de aplicaciones
            $job = Job::with(['applications', 'jobCategory', 'jobTypes'])->findOrFail($id);

            // Obtener el número de aplicaciones
            $numApplications = $job->applications->count();

            // Transformar el trabajo para incluir los tipos de trabajo
            $transformedJob = [
                'job' => new JobResource($job),
                'num_applications' => $numApplications,
            ];

            return $this->jsonResponse($transformedJob, 'Job offer detail obtained successfully', 200);
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción cuando el modelo no se encuentra
            return $this->jsonErrorResponse('Job not found.', 404);
        } catch (\Exception $e) {
            // Manejar otras excepciones generales
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
            // Validación y actualización del trabajo
            $validatedData = $request->validated();

            // Verificar si el 'company_id' en la solicitud coincide con el del usuario autenticado
            $companyId = $request->input('company_id');

            if (!$this->userOwnsCompany($companyId)) {
                return $this->jsonErrorResponse('You do not have permissions to perform this action on this resource.', 403);
            }

            // Actualizar el trabajo con los datos validados
            $job->update($validatedData);

            // Verificar y actualizar el plan de suscripción si se proporciona uno
            if ($request->has('subscription_plan_id')) {
                $subscriptionPlanId = $request->input('subscription_plan_id');
                // Puedes agregar más lógica según tus necesidades...
                $job->subscription_plan_id = $subscriptionPlanId;
            }

            // Actualizar los tipos de trabajo asociados
            if ($request->has('job_type_ids')) {
                $jobTypeIds = $request->input('job_type_ids', []);
                $job->jobTypes()->sync($jobTypeIds);
            }

            // Guardar los cambios en la base de datos
            $job->save();

            // Recargar la relación jobTypes
            $job->load('jobTypes');

            return $this->jsonResponse($job, 'Job offer updated successfully!', 200);
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
            if (!$job) {
                return response()->json(['error' => 'Job offer not found.'], 404);
            }

            // Verificar si el 'company_id' del trabajo coincide con el del usuario autenticado
            $companyId = $job->company_id;

            if (!$this->userOwnsCompany($companyId)) {
                return response()->json(['error' => 'You do not have permissions to perform this action on this resource.'], 403);
            }

            $job->delete();

            return $this->jsonResponse(null, 'Job offer deleted successfully!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error deleting the job offer: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Function to generate a consistent JSON response
     *
     * @param mixed $data The data to include in the response
     * @param string $message The response message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonResponse($data = null, $message = null, $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Function to generate a consistent JSON error response
     *
     * @param string $message The error message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonErrorResponse($message = null, $status = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        return response()->json($response, $status);
    }
}
