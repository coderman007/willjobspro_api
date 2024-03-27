<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

            // Transformar la colección de candidatos utilizando CandidateResource
            $candidatesResource = CandidateResource::collection($candidates);

            return response()->json(['data' => $candidatesResource], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
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
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        try {
            // Obtener el usuario autenticado
            $authUser = Auth::user();

            // Verificar si el usuario está autenticado y si el ID coincide
            if (!$authUser || $authUser->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener el candidato asociado al ID de usuario
            $candidate = $authUser->candidate;

            // Verificar si el candidato existe
            if (!$candidate) {
                return response()->json(['error' => 'Candidate profile not found'], 404);
            }

            // Verificar si el usuario autenticado tiene el rol 'candidate' o 'admin'
            if (!$authUser->hasRole('candidate') && !$authUser->hasRole('admin')) {
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
        } catch (Exception $e) {
            return $this->handleException($e);
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

            // Llama al método handleException para manejar la excepción
            return $this->handleException($e);
        }
    }


    public function update(UpdateCandidateRequest $request, int $userId): JsonResponse
    {
        try {
            // Iniciar una transacción de base de datos para garantizar la consistencia
            DB::beginTransaction();

            // Obtener el usuario autenticado
            $authUser = Auth::user();

            // Verificar si el usuario está autenticado
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verificar si el ID de usuario pasado como parámetro coincide con el ID del usuario autenticado
            if ($authUser->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verificar si el usuario autenticado tiene el rol de candidato
            if (!$authUser->hasRole('candidate')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Obtener el candidato asociado al ID de usuario
            $candidate = $authUser->candidate;

            // Verificar si el candidato existe
            if (!$candidate) {
                return response()->json(['error' => 'Candidate profile not found'], 404);
            }

            // Actualizar la ubicación del usuario en la tabla 'users'
            $authUser->update([
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'zip_code_id' => $request->input('zip_code_id'),
            ]);

            // Actualizar los datos del candidato
            $candidate->update($request->validated());

            // Sincronizar habilidades
            $skills = $request->input('skills') ? explode(',', $request->input('skills')) : [];
            $candidate->skills()->sync($skills);

            // Sincronizar niveles de estudio
            $educationLevels = $request->input('education_levels') ? explode(',', $request->input('education_levels')) : [];
            $candidate->educationLevels()->sync($educationLevels);

            // Sincronizar idiomas
            $languages = $request->input('languages') ? explode(',', $request->input('languages')) : [];
            $candidate->languages()->sync($languages);

            // Actualizar el currículum vitae si se proporciona
            if ($request->hasFile('cv_path')) {
                // Eliminar el archivo anterior, si existe
                Storage::disk('public')->delete($candidate->cv_path);

                // Almacenar el nuevo archivo
                $cvFile = $request->file('cv_path');
                $cvName = Str::random(40) . '.' . $cvFile->getClientOriginalExtension();
                $cvPath = 'candidate_uploads/cvs/' . $cvName;
                Storage::disk('public')->put($cvPath, file_get_contents($cvFile));

                // Actualizar la ruta del currículum vitae en la base de datos
                $candidate->cv_path = $cvPath;
                $candidate->save();
            }

            // Actualizar la foto de perfil si se proporciona
            if ($request->hasFile('photo_path')) {
                // Eliminar la foto de perfil anterior, si existe
                Storage::disk('public')->delete($candidate->photo_path);

                // Almacenar la nueva foto de perfil
                $photoFile = $request->file('photo_path');
                $photoName = Str::random(40) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
                Storage::disk('public')->put($photoPath, file_get_contents($photoFile));

                // Actualizar la ruta de la foto de perfil en la base de datos
                $candidate->photo_path = $photoPath;
                $candidate->save();
            }

            // Actualizar el banner si se proporciona
            if ($request->hasFile('banner_path')) {
                // Eliminar el banner anterior, si existe
                Storage::disk('public')->delete($candidate->banner_path);

                // Almacenar el nuevo banner
                $bannerFile = $request->file('banner_path');
                $bannerName = Str::random(40) . '.' . $bannerFile->getClientOriginalExtension();
                $bannerPath = 'candidate_uploads/banners/' . $bannerName;
                Storage::disk('public')->put($bannerPath, file_get_contents($bannerFile));

                // Actualizar la ruta del banner en la base de datos
                $candidate->banner_path = $bannerPath;
                $candidate->save();
            }

            // Commit de la transacción
            DB::commit();

            // Devolver la respuesta con el recurso del candidato actualizado
            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile updated successfully!',
                'data' => $candidateResource,
            ], 200);

        } catch (QueryException $e) {
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
     * @param int $userId
     * @return JsonResponse
     */
    public function destroy(int $userId): JsonResponse
    {
        try {

            // Obtener el usuario autenticado
            $authUser = Auth::user();

            // Verificar si el usuario está autenticado
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verificar si el ID de usuario pasado como parámetro coincide con el ID del usuario autenticado
            if ($authUser->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verificar si el usuario autenticado tiene el rol de candidato
            if (!$authUser->hasRole('candidate')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Obtener el candidato asociado al ID de usuario
            $candidate = $authUser->candidate;

            // Verificar si el candidato existe
            if (!$candidate) {
                return response()->json(['error' => 'Candidate profile not found'], 404);
            }

            // Eliminar la foto de perfil si existe
            if ($candidate->photo_path) {
                Storage::disk('public')->delete($candidate->photo_path);
            }

            // Eliminar el banner si existe
            if ($candidate->banner_path) {
                Storage::disk('public')->delete($candidate->banner_path);
            }

            // Eliminar el currículum vitae si existe
            if ($candidate->cv_path) {
                Storage::disk('public')->delete($candidate->cv_path);
            }

            // Eliminar el candidato de la base de datos
            $candidate->delete();

            // Eliminar el usuario asociado si no tiene otros roles
            if ($authUser->roles()->count() === 1) {
                $authUser->delete();
            }

            return response()->json(['message' => 'Candidate profile deleted successfully'], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle exceptions.
     *
     * @param Exception $e
     * @return JsonResponse
     */
    private function handleException(Exception $e): JsonResponse
    {
        if ($e instanceof QueryException) {
            // Error de consulta de base de datos
            return response()->json([
                'error' => 'Database query error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        } elseif ($e instanceof ModelNotFoundException) {
            // Recurso no encontrado
            return response()->json([
                'error' => 'Resource not found.',
                'details' => $e->getMessage(),
            ], 404);
        } elseif ($e instanceof AuthenticationException) {
            // Error de autenticación
            return response()->json([
                'error' => 'Unauthenticated.',
                'details' => $e->getMessage(),
            ], 401);
        } else {
            // Otro tipo de excepción
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
