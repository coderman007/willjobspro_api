<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            // Construir la consulta para las ofertas de trabajo y cargar datos relacionados
            $jobs = $this->buildJobQuery($request)->paginate($perPage);

            // Iterar sobre las ofertas de trabajo para añadir datos adicionales
            foreach ($jobs as $job) {
                $this->loadAdditionalData($job);
            }

            // Retornar las ofertas de trabajo paginadas junto con datos adicionales
            return $this->jsonResponse(JobResource::collection($jobs), 'Job offers retrieved successfully!', 200)
                ->header('X-Total-Count', $jobs->total());
        } catch (\Exception $e) {
            // Manejar cualquier error y retornar una respuesta de error
            return $this->jsonErrorResponse('Error retrieving jobs: ' . $e->getMessage(), 500);
        }
    }

    private function buildJobQuery(Request $request): Builder
    {
        // Inicializar la consulta con las relaciones necesarias
        $query = Job::with(['company', 'jobCategory', 'applications', 'skills', 'jobTypes', 'educationLevels', 'languages']);

        // Aplicar filtros basados en los parámetros de la solicitud
        $query->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->query('search');
            return $query->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%')
                ->orWhere('location', 'like', '%' . $searchTerm . '%');
        });

        // Filtrar por categoría
        $query->when($request->filled('category_id'), function ($query) use ($request) {
            $categoryId = $request->query('category_id');
            return $query->whereHas('jobCategory', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        });

        // Filtrar por niveles de estudio
        $query->when($request->filled('education_level_id'), function ($query) use ($request) {
            $educationLevelId = $request->query('education_level_id');
            return $query->whereHas('educationLevels', function ($q) use ($educationLevelId) {
                $q->where('education_levels.id', $educationLevelId);
            });
        });

        // Filtrar por idiomas
        $query->when($request->filled('language_id'), function ($query) use ($request) {
            $languageId = $request->query('language_id');
            return $query->whereHas('languages', function ($q) use ($languageId) {
                $q->where('languages.id', $languageId); // Calificar la columna id con el nombre de la tabla
            });
        });


        // Filtrar por tipos de trabajo
        $query->when($request->filled('job_type_id'), function ($query) use ($request) {
            $jobTypeId = $request->query('job_type_id');
            return $query->whereHas('jobTypes', function ($q) use ($jobTypeId) {
                $q->where('job_types.id', $jobTypeId);
            });
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
            if (!$user->hasRole('company')) {
                return $this->jsonErrorResponse('Only users with role Company can create job offers.', 403);
            }

            // Obtener el ID de la compañía del usuario autenticado
            $companyId = $user->company->id;

            // Asignar el ID de la compañía a los datos validados
            $validatedData['company_id'] = $companyId;

            // Crear la oferta de trabajo con los datos validados
            $job = Job::create($validatedData);

            // Asociar habilidades
            if ($request->filled('skills')) {
                $skills = $request->input('skills');
                $job->skills()->syncWithoutDetaching($skills);
            }

            // Asociar tipos de trabajo
            if ($request->filled('job_types')) {
                $jobTypes = $request->input('job_types');
                $job->jobTypes()->syncWithoutDetaching($jobTypes);
            }

            // Asociar niveles educativos
            if ($request->filled('education_levels')) {
                $educationLevels = $request->input('education_levels');
                $job->educationLevels()->syncWithoutDetaching($educationLevels);
            }

            // Asociar idiomas
            if ($request->filled('languages')) {
                $languages = $request->input('languages');
                foreach ($languages as $languageData) {
                    $language = Language::find($languageData['id']);
                    if ($language) {
                        $job->languages()->attach($language->id, ['level' => $languageData['level']]);
                    }
                }
            }

            // Cargar las relaciones asociadas a la oferta de trabajo
            $job->load('skills', 'jobTypes', 'educationLevels', 'languages');

            // Devolver una respuesta exitosa con la oferta de trabajo creada
            return $this->jsonResponse($job, 'Job offer created successfully', 201);
        } catch (\Exception $e) {
            // Manejar cualquier error y devolver una respuesta de error
            return $this->jsonErrorResponse($e->getMessage(), 500);
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
            // Cargar la oferta de trabajo con sus relaciones
            $job->load(['company.user', 'jobCategory', 'skills', 'jobTypes', 'languages', 'educationLevels']);

            // Cargar datos adicionales
            $this->loadAdditionalData($job);

            // Devolver la oferta de trabajo como un recurso API
            return $this->jsonResponse(new JobResource($job), 'Job offer detail obtained successfully', 200);
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

    /**
     * Update the specified resource in storage.
     *
     * @param StoreJobRequest $request
     * @param Job $job
     * @return JsonResponse
     */
    public function update(StoreJobRequest $request, Job $job)
    {
        try {
            // Comenzar una transacción de base de datos
            DB::beginTransaction();

            // Validar los datos de la solicitud
            $validatedData = $request->validated();

            // Obtener el usuario autenticado y asegurarse de que sea una empresa
            $user = auth()->user();
            if (!$user->hasRole('company')) {
                // Devolver un error si el usuario no tiene el rol adecuado
                return $this->jsonErrorResponse('Only users with role Company can update job offers.', 403);
            }

            // Verificar si el usuario es el propietario de la oferta de trabajo
            if ($user->id !== $job->company_id) {
                // Devolver un error si el usuario no es el propietario de la oferta
                return $this->jsonErrorResponse('You are not authorized to update this job offer.', 403);
            }

            // Actualizar la oferta de trabajo con los datos validados
            $job->update($validatedData);

            // Asociar habilidades
            if ($request->filled('skills')) {
                $skills = $request->input('skills');
                $job->skills()->sync($skills);
            } else {
                // Si no se proporcionan habilidades, eliminar todas las asociaciones existentes
                $job->skills()->detach(); // Desasociar todas las habilidades existentes de la oferta de trabajo
            }

            // Asociar tipos de trabajo
            if ($request->filled('job_types')) {
                $jobTypes = $request->input('job_types');
                $job->jobTypes()->sync($jobTypes);
            } else {
                // Si no se proporcionan tipos de trabajo, eliminar todas las asociaciones existentes
                $job->jobTypes()->detach(); // Desasociar todos los tipos de trabajo existentes de la oferta de trabajo
            }

            // Asociar niveles educativos
            if ($request->filled('education_levels')) {
                $educationLevels = $request->input('education_levels');
                $job->educationLevels()->sync($educationLevels);
            } else {
                // Si no se proporcionan niveles educativos, eliminar todas las asociaciones existentes
                $job->educationLevels()->detach(); // Desasociar todos los niveles educativos existentes de la oferta de trabajo
            }

            // Asociar idiomas
            if ($request->filled('languages')) {
                $languages = $request->input('languages');
                $job->languages()->detach(); // Desasociar todos los idiomas existentes de la oferta de trabajo
                foreach ($languages as $languageData) {
                    $language = Language::find($languageData['id']);
                    if ($language) {
                        $job->languages()->attach($language->id, ['level' => $languageData['level']]);
                    }
                }
            } else {
                // Si no se proporcionan idiomas, eliminar todas las asociaciones existentes
                $job->languages()->detach(); // Desasociar todos los idiomas existentes de la oferta de trabajo
            }

            // Confirmar la transacción de base de datos
            DB::commit();

            // Devolver una respuesta exitosa con la oferta de trabajo actualizada
            $jobResource = new JobResource($job);
            return $this->jsonResponse($jobResource, 'Job offer updated successfully', 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            // Manejar cualquier error y devolver una respuesta de error
            return $this->jsonErrorResponse($e->getMessage(), 500);
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
     * Build the job query based on the request filters.
     *
     * @param Request $request
     * @return Builder
     */


    /**
     * Load additional data for a job.
     *
     * @param Job $job
     * @return void
     */
    private function loadAdditionalData(Job $job): void
    {
        // Calcular si el usuario ha aplicado a esta oferta de trabajo
        $job->setAttribute('applied', $job->applications->count() > 0);

        // Obtener los nombres de los tipos de trabajo relacionados
        $job->setAttribute('job_types', $job->jobTypes->pluck('name')->implode(', '));

        // Obtener los nombres de los idiomas relacionados
        $job->setAttribute('languages', $job->languages->pluck('name')->implode(', '));
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

