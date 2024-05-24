<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\EducationLevel;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Language;
use App\Services\LocationService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rules = [
            'search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:title,description,location,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1',
            'company_name' => 'nullable|string',
            'skill_name' => 'nullable|string',
            'education_level_name' => 'nullable|string',
            'language_name' => 'nullable|string',
            'benefit_name' => 'nullable|string',
            'job_type_name' => 'nullable|string',
            'job_category_name' => 'nullable|string',
            'country_name' => 'nullable|string',
            'state_name' => 'nullable|string',
            'city_name' => 'nullable|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorResponse('Validation Error: ' . $validator->errors()->first(), 422);
        }

        try {
            $perPage = $request->filled('per_page') ? max(1, intval($request->query('per_page'))) : 10;

            // Get minimum and maximum salary directly from database
            $minSalary = Job::select(DB::raw('MIN(salary) as min_salary'))->first()->min_salary;
            $maxSalary = Job::select(DB::raw('MAX(salary) as max_salary'))->first()->max_salary;

            // Usar paginate en lugar de items()
            $jobs = $this->buildJobQuery($request)->paginate($perPage);

            // Retornar las ofertas de trabajo paginadas
            return $this->jsonResponse(
                JobResource::collection($jobs),
                'Job offers retrieved successfully!',
                200,
                $minSalary,
                $maxSalary
            );
        } catch (Exception $e) {
            return $this->jsonErrorResponse('Error retrieving jobs: ' . $e->getMessage());
        }
    }

    private function buildJobQuery(Request $request): Builder
    {
        $query = Job::with(['company', 'jobCategory', 'applications', 'benefits', 'skills', 'jobTypes', 'educationLevels', 'languages']);

        // Aplicar filtros
        $query->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->query('search');
            return $query->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%')
                ->orWhere('posted_date', 'like', '%' . $searchTerm . '%')
                ->orWhere('deadline', 'like', '%' . $searchTerm . '%')
                ->orWhere('contact_email', 'like', '%' . $searchTerm . '%')
                ->orWhere('contact_phone', 'like', '%' . $searchTerm . '%');
        });

        $query->when($request->filled('company_name'), function ($query) use ($request) {
            $companyName = $request->query('company_name');
            return $query->whereHas('company', function ($q) use ($companyName) {
                $q->where('name', 'like', '%' . $companyName . '%');
            });
        });

        $query->when($request->filled('skill_name'), function ($query) use ($request) {
            $skillName = $request->query('skill_name');
            return $query->whereHas('skills', function ($q) use ($skillName) {
                $q->where('name', 'like', '%' . $skillName . '%');
            });
        });

        $query->when($request->filled('education_level_name'), function ($query) use ($request) {
            $educationLevelName = $request->query('education_level_name');
            return $query->whereHas('educationLevels', function ($q) use ($educationLevelName) {
                $q->where('name', 'like', '%' . $educationLevelName . '%');
            });
        });

        $query->when($request->filled('benefit_name'), function ($query) use ($request) {
            $benefitName = $request->query('benefit_name');
            return $query->whereHas('benefits', function ($q) use ($benefitName) {
                $q->where('name', 'like', '%' . $benefitName . '%');
            });
        });

        $query->when($request->filled('language_name'), function ($query) use ($request) {
            $languageName = $request->query('language_name');
            return $query->whereHas('languages', function ($q) use ($languageName) {
                $q->where('name', 'like', '%' . $languageName . '%');
            });
        });

        $query->when($request->filled('job_type_name'), function ($query) use ($request) {
            $jobTypeName = $request->query('job_type_name');
            return $query->whereHas('jobTypes', function ($q) use ($jobTypeName) {
                $q->where('name', 'like', '%' . $jobTypeName . '%');
            });
        });

        $query->when($request->filled('job_category_name'), function ($query) use ($request) {
            $jobCategoryName = $request->query('job_category_name');
            return $query->whereHas('jobCategory', function ($q) use ($jobCategoryName) {
                $q->where('name', 'like', '%' . $jobCategoryName . '%');
            });
        });

        $query->when($request->filled('country_name'), function ($query) use ($request) {
            $countryName = $request->query('country_name');
            return $query->whereHas('country', function ($q) use ($countryName) {
                $q->where('name', 'like', '%' . $countryName . '%');
            });
        });

        $query->when($request->filled('state_name'), function ($query) use ($request) {
            $stateName = $request->query('state_name');
            return $query->whereHas('state', function ($q) use ($stateName) {
                $q->where('name', 'like', '%' . $stateName . '%');
            });
        });

        $query->when($request->filled('city_name'), function ($query) use ($request) {
            $cityName = $request->query('city_name');
            return $query->whereHas('city', function ($q) use ($cityName) {
                $q->where('name', 'like', '%' . $cityName . '%');
            });
        });

        $query->when($request->filled('min_salary'), function ($query) use ($request) {
            $minSalary = $request->query('min_salary');
            return $query->where('salary', '>=', $minSalary);
        });

        $query->when($request->filled('max_salary'), function ($query) use ($request) {
            $maxSalary = $request->query('max_salary');
            return $query->where('salary', '<=', $maxSalary);
        });

        $query->when($request->filled('sort_by') && $request->filled('sort_order'), function ($query) use ($request) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            return $query->orderBy($sortBy, $sortOrder);
        }, function ($query) {
            $query->orderBy('created_at', 'desc');
        });

        return $query;
    }

    public function getJobTypeCounts(): array
    {
        $jobTypes = JobType::withCount('jobs')->get();

        // Modify the return statement to return the object directly
        return [
            'data' => $jobTypes->pluck('jobs_count', 'name')->toArray()
        ];
    }

    public function getEducationLevelCounts(): array
    {
        $educationLevels = EducationLevel::withCount('jobs')->get();

        // Modify the return statement to return the object directly
        return [
            'data' => $educationLevels->pluck('jobs_count', 'name')->toArray()
        ];
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

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($job, $request);

            // Asociar ubicación si los datos están presentes
            if ($request->filled('location')) {
                $locationService = new LocationService();
                $locationData = $request->input('location');
                $locationResult = $locationService->createAndAssociateLocationForJob($locationData, $job);
                if (isset($locationResult['errors'])) {
                    // Revertir la transacción en caso de error
                    DB::rollBack();
                    return response()->json(['errors' => $locationResult['errors']], 422);
                }
            }

            // Asociar beneficios
            if ($request->filled('benefits')) {
                $benefits = $request->input('benefits');
                $job->benefits()->syncWithoutDetaching($benefits);
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
        } catch (Exception $e) {
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
            return $this->jsonResponse(new JobResource($job), 'Job offer detail obtained successfully');
        } catch (ModelNotFoundException $e) {
            return $this->jsonErrorResponse('Job not found.', 404);
        } catch (Exception $e) {
            return $this->jsonErrorResponse('Error retrieving job details: ' . $e->getMessage());
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

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($job, $request);

            // Asociar beneficios
            if ($request->filled('benefits')) {
                $benefits = $request->input('benefits');
                $job->benefits()->sync($benefits);
            } else {
                // Si no se proporcionan beneficios, eliminar todas las asociaciones existentes
                $job->educationLevels()->detach(); // Desasociar todos los beneficios existentes de la oferta de trabajo
            }

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
            return $this->jsonResponse($jobResource, 'Job offer updated successfully');
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            // Manejar cualquier error y devolver una respuesta de error
            return $this->jsonErrorResponse($e->getMessage());
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

            return $this->jsonResponse(null, 'Job offer deleted successfully!');
        } catch (Exception $e) {
            return $this->jsonErrorResponse('Error deleting the job offer: ' . $e->getMessage());
        }
    }

    private function storeBase64Files(Job $job, Request $request): void
    {
        if ($request->has('image')) {
            $imageBase64 = $request->input('image');
            $imageName = Str::random(40) . '.jpg'; // Cambiar la extensión según el tipo de archivo permitido
            $imagePath = 'job_uploads/images/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($imageBase64));
            $job->image = $imagePath;
        }

        if ($request->has('video')) {
            $videoBase64 = $request->input('video');
            $videoName = Str::random(40) . '.mp4'; // Cambiar la extensión según el tipo de archivo permitido
            $videoPath = 'job_uploads/videos/' . $videoName;
            Storage::disk('public')->put($videoPath, base64_decode($videoBase64));
            $job->video = $videoPath;
        }
    }

    protected function jsonResponse(mixed $data = null, ?string $message = null, int $status = 200, ?float $minSalary = null, ?float $maxSalary = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        // Agregar salario mínimo y máximo si están presentes
        if ($minSalary !== null) {
            $response['min_salary'] = $minSalary;
        }

        if ($maxSalary !== null) {
            $response['max_salary'] = $maxSalary;
        }

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
