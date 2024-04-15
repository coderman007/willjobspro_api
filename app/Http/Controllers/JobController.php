<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Language;
use App\Services\LocationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
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

        // Filtrar por ID de la compañía
        $query->when($request->filled('company_id'), function ($query) use ($request) {
            $companyId = $request->query('company_id');
            return $query->where('company_id', $companyId);
        });

        // Filtrar por ID de la categoría de trabajo
        $query->when($request->filled('job_category_id'), function ($query) use ($request) {
            $jobCategoryId = $request->query('job_category_id');
            return $query->whereHas('jobCategory', function ($q) use ($jobCategoryId) {
                $q->where('id', $jobCategoryId);
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

        // Filtrar por habilidades
        $query->when($request->filled('skill_id'), function ($query) use ($request) {
            $skillId = $request->query('skill_id');
            return $query->whereHas('skills', function ($q) use ($skillId) {
                $q->where('skills.id', $skillId);
            });
        });

        /// Ordenar resultados
        $query->when($request->filled('sort_by') && $request->filled('sort_order'), function ($query) use ($request) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            return $query->orderBy($sortBy, $sortOrder);
        }, function ($query) {
            // Ordenar por defecto si no se especifica
            return $query->orderBy('created_at', 'desc');
        });

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        // Definir reglas de validación para los filtros
        $rules = [
            'search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:title,description,location,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'company_id' => 'nullable|exists:companies,id',
            'skill_id' => 'nullable|exists:skills,id',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'language_id' => 'nullable|exists:languages,id',
            'job_type_id' => 'nullable|exists:job_types,id',
        ];

        // Validar los parámetros de la solicitud
        $validator = Validator::make($request->all(), $rules);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return $this->jsonErrorResponse('Validation Error: ' . $validator->errors()->first(), 422);
        }

        // Verificar si se proporciona el parámetro perPage y si es un número válido
        $perPage = $request->filled('per_page') ? max(1, intval($request->query('per_page'))) : 10;


        try {
            $perPage = $request->query('per_page', 10);

            // Construir la consulta para las ofertas de trabajo y cargar datos relacionados
            $jobs = $this->buildJobQuery($request)->paginate($perPage)->items();

            // Retornar las ofertas de trabajo paginadas junto con datos adicionales
            return $this->jsonResponse(JobResource::collection($jobs), 'Job offers retrieved successfully!', 200);
        } catch (\Exception $e) {
            // Manejar cualquier error y retornar una respuesta de error
            return $this->jsonErrorResponse('Error retrieving jobs: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Obtener el usuario autenticado y asegurarse de que sea una empresa
            $user = auth()->user();
            if (!$user->hasRole('company')) {
                return response()->json(['error' => 'Only users with role Company can create job offers.'], 403);
            }

            // Validar los datos de la solicitud
            $validatedData = $request->validated();

            // Obtener el ID de la compañía del usuario autenticado
            $companyId = $user->company->id;

            // Asignar el ID de la compañía a los datos validados
            $validatedData['company_id'] = $companyId;

            // Crear la oferta de trabajo con los datos validados
            $job = Job::create($validatedData);

            // Asociar ubicación
            $locationService = new LocationService();
            $locationData = $request->input('location');
            $locationResult = $locationService->createAndAssociateLocationForJob($locationData, $job);
            if (isset($locationResult['errors'])) {
                // Revertir la transacción en caso de error
                DB::rollBack();
                return response()->json(['errors' => $locationResult['errors']], 422);
            }

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
            // Confirmar la transacción
            DB::commit();

            // Cargar las relaciones asociadas a la oferta de trabajo
            $job->load('skills', 'jobTypes', 'educationLevels', 'languages');

            // Devolver una respuesta exitosa con la oferta de trabajo creada
            $jobResource = new JobResource($job);
            return response()->json([
                'message' => 'Job offer created successfully!',
                'data' => $jobResource,
            ], 201);
        } catch (\Exception $e) {
            // Manejar cualquier error y revertir la transacción
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Job $job): JsonResponse
    {
        try {
            // Cargar la oferta de trabajo con sus relaciones
            $job->load(['company.user', 'jobCategory', 'skills', 'jobTypes', 'languages', 'educationLevels']);

            // Devolver la oferta de trabajo como un recurso API
            return $this->jsonResponse(new JobResource($job), 'Job offer detail obtained successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->jsonErrorResponse('Job not found.', 404);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving job details: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        try {
            // Comenzar una transacción de base de datos
            DB::beginTransaction();

            // Obtener el usuario autenticado
            $user = auth()->user();

            // Verificar si el usuario está autenticado y es propietario de la oferta a través del modelo Company
            if (!$user || $job->company->user_id !== $user->id) {
                return $this->jsonErrorResponse('Unauthorized', 403);
            }

            // Verificar si el usuario tiene el rol de "company"
            if (!$user->hasRole('company')) {
                return $this->jsonErrorResponse('Only users with role Company can update job offers.', 403);
            }

            // Validar los datos de la solicitud
            $validatedData = $request->validated();

            // Actualizar la oferta de trabajo con los datos validados
            $job->update($validatedData);

            // Asociar ubicación
            $locationService = new LocationService();
            $locationData = $request->input('location');
            $locationResult = $locationService->updateAndAssociateLocationForJob($locationData, $job);
            if (isset($locationResult['errors'])) {
                // Revertir la transacción en caso de error
                DB::rollBack();
                return response()->json(['errors' => $locationResult['errors']], 422);
            }

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

    protected function jsonResponse(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    protected function jsonErrorResponse(?string $message = null, int $status = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        return response()->json($response, $status);
    }

}

