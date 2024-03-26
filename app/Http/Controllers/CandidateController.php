<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CandidateController extends Controller
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
            $query = Candidate::query();

            // Búsqueda
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery->where('full_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('skills', 'like', '%' . $searchTerm . '%')
                        ->orWhere('certifications', 'like', '%' . $searchTerm . '%');
                });
            }

            // Filtros
            $filters = [
                'full_name',
                'gender',
                'education_level_id',
                'status',
                'date_of_birth',
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
            }

            // Modifica la consulta para cargar la relación 'user' con las ubicaciones
            $query->with(['user.country', 'user.state', 'user.city', 'user.zipCode']);

            $candidates = $query->paginate($perPage);

            $paginationData = [
                'total' => $candidates->total(),
            ];

            return response()->json(['data' => $candidates, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the candidate list!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllApplications(Request $request): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado tiene el rol 'candidate'
            /** @var \App\Models\User */
            $user = Auth::user();
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'Unauthorized access to applications'], 403);
            }

            // Obtener las aplicaciones del candidato actual
            $applications = $user->candidate->applications()->with(['job'])->get();

            // Transformar las aplicaciones a un formato de respuesta
            $applicationsData = [];
            foreach ($applications as $application) {
                $applicationsData[] = [
                    'id' => $application->id,
                    'cover_letter' => $application->cover_letter,
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                    'job_id' => $application->job_id,
                    'job_title' => $application->job->title, // Agregar el título de la oferta de trabajo
                    'job_salary' => $application->job->salary, // Agregar el salario de la oferta de trabajo
                    'company_id' => $application->job->company_id, // Agregar el ID de la compañía
                    'company_name' => $application->job->company->name, // Agregar el nombre de la compañía
                ];
            }

            // Devolver la información de las aplicaciones
            return response()->json(['data' => $applicationsData], 200);
        } catch (\Exception $e) {
            return $this->handleGenericError($e);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function show(Candidate $candidate): JsonResponse
    {
        try {

            // Verificar si el usuario autenticado tiene el rol 'candidate' o 'admin'
            $user = Auth::user();
            if (!($user->hasRole('candidate') && $user->id === $candidate->user_id) && !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized access to candidate profile'], 403);
            }

            // Obtener las habilidades del candidato
            $skills = $candidate->skills;

            // Transformar el candidato a un recurso CandidateResource
            $candidateResource = new CandidateResource($candidate);

            // Devolver la respuesta incluyendo las URLs de las imágenes
            return response()->json([
                'message' => 'Candidate Profile Successfully Obtained!',
                'data' => [
                    'info' => $candidateResource,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso en el que no se encuentra al candidato
            return response()->json([
                'error' => 'Candidate not found.',
                'details' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y devolver una respuesta de error
            return response()->json([
                'error' => 'An error occurred while getting the candidate profile.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            // Iniciamos una transacción de base de datos para garantizar la consistencia
            DB::beginTransaction();

            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = Auth::user();
            // Verificar si el usuario tiene el rol 'candidate'
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'You do not have permission to create a candidate'], 403);
            }

            $request->input('country_id') ? $countryId = $request->input('country_id') : $countryId = null;
            $request->input('state_id') ? $stateId = $request->input('state_id') : $stateId = null;
            $request->input('city_id') ? $cityId = $request->input('city_id') : $cityId = null;
            $request->input('zip_code_id') ? $zipCodeId = $request->input('zip_code_id') : $zipCodeId = null;

            // Actualizar la ubicación del usuario en la tabla 'users'
            $user->update([
                'country_id' => $countryId,
                'state_id' => $stateId,
                'city_id' => $cityId,
                'zip_code_id' => $zipCodeId,
            ]);

            // Crear un nuevo candidato asociado al usuario autenticado
            $candidate = new Candidate;
            $candidate->user_id = $user->id;
            $candidate->fill($request->validated()); // Fill fillable attributes

            // Almacenamiento del CV
            if ($request->hasFile('cv_path')) {
                $cvFile = $request->file('cv_path');
                $cvName = Str::random(40) . '.' . $cvFile->getClientOriginalExtension();
                $cvPath = 'candidate_uploads/cvs/' . $cvName;
                Storage::disk('public')->put($cvPath, file_get_contents($cvFile));
                $candidate->cv_path = $cvPath;
            }

            // Almacenamiento de la foto de perfil
            if ($request->hasFile('photo_path')) {
                $photoFile = $request->file('photo_path');
                $photoName = Str::random(40) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
                Storage::disk('public')->put($photoPath, file_get_contents($photoFile));
                $candidate->photo_path = $photoPath;
            }

            // Almacenamiento del banner
            if ($request->hasFile('banner_path')) {
                $bannerFile = $request->file('banner_path');
                $bannerName = Str::random(40) . '.' . $bannerFile->getClientOriginalExtension();
                $bannerPath = 'candidate_uploads/banners/' . $bannerName;
                Storage::disk('public')->put($bannerPath, file_get_contents($bannerFile));
                $candidate->banner_path = $bannerPath;
            }

            // Guardar el candidato en la base de datos
            $candidate->save();

            // Habilidades
            $skills = $request->input('skills') ? explode(',', $request->input('skills')) : [];

            // Niveles de estudio
            $educationLevels = $request->input('education_levels') ? explode(',', $request->input('education_levels')) : [];

            // Idiomas
            $languages = $request->input('languages') ? explode(',', $request->input('languages')) : [];


            // Sincronizar las habilidades del candidato
            $candidate->skills()->syncWithoutDetaching($skills);

            // Sincronizar los niveles de educación del candidato
            $candidate->educationLevels()->syncWithoutDetaching($educationLevels);

            // Sincronizar los idiomas del candidato
            $candidate->languages()->syncWithoutDetaching($languages);

            // Commit de la transacción
            DB::commit();

            // Devolver la respuesta con el recurso del candidato creado
            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile created successfully!',
                'data' => $candidateResource,
            ], 201);

        } catch (QueryException $e) {
            // Rollback de la transacción en caso de error inesperado
            DB::rollBack();
            return response()->json([
                'error' => 'An unexpected error occurred while creating the candidate profile!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified candidate in storage.
     *
     * @param UpdateCandidateRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado tiene permiso para actualizar el perfil del candidato
            if (!$candidate->user->is(Auth::user())) {
                return response()->json(['error' => 'Unauthorized to update this candidate profile'], 403);
            }

            // Iniciamos una transacción de base de datos para garantizar la consistencia
            DB::beginTransaction();

            // Actualizar los datos del candidato
            $candidate->update($request->validated());

            // Actualizar el CV si se proporciona un nuevo archivo
            if ($request->hasFile('cv_path')) {
                $cvFile = $request->file('cv_path');
                $cvName = Str::random(40) . '.' . $cvFile->getClientOriginalExtension();
                $cvPath = 'candidate_uploads/cvs/' . $cvName;
                Storage::disk('public')->put($cvPath, file_get_contents($cvFile));
                $candidate->cv_path = $cvPath;
            }

            // Actualizar la foto de perfil si se proporciona un nuevo archivo
            if ($request->hasFile('photo_path')) {
                $photoFile = $request->file('photo_path');
                $photoName = Str::random(40) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
                Storage::disk('public')->put($photoPath, file_get_contents($photoFile));
                $candidate->photo_path = $photoPath;
            }

            // Actualizar el banner si se proporciona un nuevo archivo
            if ($request->hasFile('banner_path')) {
                $bannerFile = $request->file('banner_path');
                $bannerName = Str::random(40) . '.' . $bannerFile->getClientOriginalExtension();
                $bannerPath = 'candidate_uploads/banners/' . $bannerName;
                Storage::disk('public')->put($bannerPath, file_get_contents($bannerFile));
                $candidate->banner_path = $bannerPath;
            }

            // Guardar los cambios en el candidato
            $candidate->save();

            // Sincronizar las habilidades del candidato
            $candidate->skills()->sync($request->input('skills', []));

            // Sincronizar los niveles de educación del candidato
            $candidate->educationLevels()->sync($request->input('education_levels', []));

            // Sincronizar los idiomas del candidato
            $candidate->languages()->sync($request->input('languages', []));

            // Commit de la transacción
            DB::commit();

            // Devolver la respuesta con el recurso del candidato actualizado
            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile updated successfully!',
                'data' => $candidateResource,
            ], 200);

        } catch (QueryException $e) {
            // Rollback de la transacción en caso de error en la base de datos
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while updating the candidate profile!',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Rollback de la transacción en caso de error inesperado
            DB::rollBack();
            return response()->json([
                'error' => 'An unexpected error occurred while updating the candidate profile!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function destroy(Candidate $candidate): JsonResponse
    {
        try {
            // Validar que el usuario autenticado tenga permisos para eliminar este candidato
            /** @var \App\Models\User */
            $user = Auth::user();
            if (!$user->hasRole('admin') && $user->id !== $candidate->user_id) {
                return response()->json(['error' => 'Unauthorized to delete this candidate'], 403);
            }

            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Eliminar al candidato y al usuario asociado
            $candidate->delete();
            $candidate->user()->delete();

            // Commit de la transacción
            DB::commit();

            return response()->json(['message' => 'Candidate deleted!'], 200);
        } catch (\Exception $e) {
            // Rollback de la transacción en caso de error
            DB::rollBack();

            return response()->json([
                'error' => 'An error occurred while deleting the candidate and associated user!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
