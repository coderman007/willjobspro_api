<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Database\QueryException;
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
            $query = Job::with(['company', 'jobCategory', 'jobType', 'subscriptionPlan']);

            // Búsqueda por título de trabajo
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where('title', 'like', '%' . $searchTerm . '%');
            }

            // Filtros
            $filters = [
                'company_id', 'job_category_id', 'job_type_id', 'subscription_plan_id', 'title', 'description', 'status', 'location',
            ];

            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->query($filter));
                }
            }

            // Ordenación
            if ($request->filled('sort_by') && $request->filled('sort_order')) {
                $sortBy = $request->query('sort_by');
                $sortOrder = $request->query('sort_order');
                $query->orderBy($sortBy, $sortOrder);
            } else {
                // Orden por defecto si no se especifica
                $query->orderBy('created_at', 'desc');
            }

            $jobs = $query->paginate($perPage);

            $paginationData = [
                'total' => $jobs->total(),
                // 'per_page' => $jobs->perPage(),
                // 'current_page' => $jobs->currentPage(),
                // 'last_page' => $jobs->lastPage(),
                // 'from' => $jobs->firstItem(),
                // 'to' => $jobs->lastItem(),
                // 'next_page_url' => $jobs->nextPageUrl(),
                // 'prev_page_url' => $jobs->previousPageUrl(),
                // 'path' => $jobs->path(),
            ];

            return response()->json(['data' => $jobs, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the job offer list!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJobRequest $request
     * @return JsonResponse
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJobRequest $request
     * @return JsonResponse
     */
    public function store(StoreJobRequest $request)
    {
        try {
            // Validación y creación del trabajo
            $validatedData = $request->validated();

            // Verificar si el 'company_id' en la solicitud coincide con el del usuario autenticado
            $companyId = $request->input('company_id');

            if (!$this->userOwnsCompany($companyId)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
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

            // Resto de la lógica, si es necesario...

            return response()->json(['message' => 'Job offer created successfully', 'data' => $job], 201);
        } catch (QueryException $e) {

            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'An error ocurred in database while creating the job offer.',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while creating the job offer.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    protected function userOwnsCompany($companyId)
    {
        $user = auth()->user();
        return $user && $user->company && $user->company->id == $companyId;
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
            return response()->json(['data' => $job], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred whiled getting the job offer!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJobRequest $request
     * @param Job $job
     * @return JsonResponse
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        try {
            // Validación y actualización del trabajo
            $validatedData = $request->validated();

            // Verificar si el 'company_id' en la solicitud coincide con el del usuario autenticado
            $companyId = $request->input('company_id');

            if (!$this->userOwnsCompany($companyId)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Verificar si se proporcionó un nuevo plan de suscripción
            $subscriptionPlanId = $request->input('subscription_plan_id', null);

            // Lógica para actualizar el plan de suscripción si se proporciona uno
            if (!is_null($subscriptionPlanId)) {
                $job->subscription_plan_id = $subscriptionPlanId;
                // Puedes agregar más lógica según tus necesidades...
            }

            // Actualizar el trabajo con los datos validados
            $job->update($validatedData);

            // Resto de la lógica, si es necesario...

            return response()->json(['message' => 'Job offer updated successfully', 'data' => $job], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred whiled updating the job offer!.',
                'details' => $e->getMessage(),
            ], 500);
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
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            $job->delete();

            return response()->json(['message' => 'Job offer deleted!.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred whiled deleting the job offer!.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
