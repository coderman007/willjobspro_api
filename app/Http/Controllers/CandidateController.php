<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Exception;
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
                    $subquery->where('skills', 'like', '%' . $searchTerm . '%')
                        ->orWhere('certifications', 'like', '%' . $searchTerm . '%');
                });
            }

            // Filtros
            $filters = [
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


    /**
     * Get all applications associated with the authenticated candidate user.
     *
     * This method retrieves all job applications made by the authenticated candidate user.
     * It checks if the user has the 'candidate' role and returns an error if not authorized.
     * Then it fetches the applications for the current candidate user along with related job information.
     * Finally, it transforms the application data into a response format and returns it as JSON.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse The JSON response containing the application data.
     */
    public function getAllApplications(Request $request): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado tiene el rol 'candidate'
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

            // Devolver la respuesta incluyendo las URL de las imágenes
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
            DB::beginTransaction();

            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = Auth::user();

            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'You do not have permission to create a candidate'], 403);
            }

            $user->update($request->only(['country_id', 'state_id', 'city_id', 'zip_code_id']));

            $candidate = new Candidate;
            $candidate->user_id = $user->id;
            $candidate->fill($request->validated());

            // Almacenar los archivos codificados en Base64
            $this->storeBase64Files($candidate, $request);

            $candidate->save();

            $this->syncRelations($candidate, $request);

            DB::commit();

            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile created successfully!',
                'data' => $candidateResource,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    private function storeBase64Files(Candidate $candidate, Request $request): void
    {
        if ($request->has('cv_file_base64')) {
            $cvBase64 = $request->input('cv_file_base64');
            $cvName = Str::random(40) . '.pdf'; // Nombre de archivo predeterminado o según el tipo de archivo
            $cvPath = 'candidate_uploads/cvs/' . $cvName;
            Storage::disk('public')->put($cvPath, base64_decode($cvBase64));
            $candidate->cv_file = $cvPath;
        }

        if ($request->has('photo_file_base64')) {
            $photoBase64 = $request->input('photo_file_base64');
            $photoName = Str::random(40) . '.jpg'; // Nombre de archivo predeterminado o según el tipo de archivo
            $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
            Storage::disk('public')->put($photoPath, base64_decode($photoBase64));
            $candidate->photo_file = $photoPath;
        }

        if ($request->has('banner_file_base64')) {
            $bannerBase64 = $request->input('banner_file_base64');
            $bannerName = Str::random(40) . '.jpg'; // Nombre de archivo predeterminado o según el tipo de archivo
            $bannerPath = 'candidate_uploads/banners/' . $bannerName;
            Storage::disk('public')->put($bannerPath, base64_decode($bannerBase64));
            $candidate->banner_file = $bannerPath;
        }
    }


    private function syncRelations(Candidate $candidate, Request $request): void
    {
        // Habilidades
        $skills = $request->input('skills') ? explode(',', $request->input('skills')) : [];
        $candidate->skills()->syncWithoutDetaching($skills);

        // Sincronizar niveles de estudio
        $educationLevels = $request->input('education_levels') ? explode(',', $request->input('education_levels')) : [];
        $candidate->educationLevels()->syncWithoutDetaching($educationLevels);

        // Sincronizar Redes sociales
        $socialNetworks = $request->input('social_networks') ? explode(',', $request->input('social_networks')) : [];
        $candidate->socialNetworks()->syncWithoutDetaching($socialNetworks);


        // Sincronizar Idiomas
        if ($request->has('languages')) {
            $languages = $this->parseLanguages($request->input('languages'));
            foreach ($languages as $language) {
                $candidate->languages()->attach($language['id'], ['level' => $language['level']]);
            }
        }
    }

    private function parseLanguages(string $languages): array
    {
        $parsedLanguages = [];
        $languageStrings = explode(',', $languages);
        foreach ($languageStrings as $languageString) {
            list($id, $level) = explode(':', $languageString);
            $parsedLanguages[] = ['id' => $id, 'level' => $level];
        }
        return $parsedLanguages;
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
            if ($request->hasFile('cv_file')) {
                // Eliminar el archivo anterior, si existe
                Storage::disk('public')->delete($candidate->cv_file);

                // Almacenar el nuevo archivo
                $cvFile = $request->file('cv_file');
                $cvName = Str::random(40) . '.' . $cvFile->getClientOriginalExtension();
                $cvPath = 'candidate_uploads/cvs/' . $cvName;
                Storage::disk('public')->put($cvPath, file_get_contents($cvFile));

                // Actualizar la ruta del currículum vitae en la base de datos
                $candidate->cv_file = $cvPath;
                $candidate->save();
            }

            // Actualizar la foto de perfil si se proporciona
            if ($request->hasFile('photo_file')) {
                // Eliminar la foto de perfil anterior, si existe
                Storage::disk('public')->delete($candidate->photo_file);

                // Almacenar la nueva foto de perfil
                $photoFile = $request->file('photo_file');
                $photoName = Str::random(40) . '.' . $photoFile->getClientOriginalExtension();
                $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
                Storage::disk('public')->put($photoPath, file_get_contents($photoFile));

                // Actualizar la ruta de la foto de perfil en la base de datos
                $candidate->photo_file = $photoPath;
                $candidate->save();
            }

            // Actualizar el banner si se proporciona
            if ($request->hasFile('banner_file')) {
                // Eliminar el banner anterior, si existe
                Storage::disk('public')->delete($candidate->banner_file);

                // Almacenar el nuevo banner
                $bannerFile = $request->file('banner_file');
                $bannerName = Str::random(40) . '.' . $bannerFile->getClientOriginalExtension();
                $bannerPath = 'candidate_uploads/banners/' . $bannerName;
                Storage::disk('public')->put($bannerPath, file_get_contents($bannerFile));

                // Actualizar la ruta del banner en la base de datos
                $candidate->banner_file = $bannerPath;
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
            if ($candidate->photo_file) {
                Storage::disk('public')->delete($candidate->photo_file);
            }

            // Eliminar el banner si existe
            if ($candidate->banner_file) {
                Storage::disk('public')->delete($candidate->banner_file);
            }

            // Eliminar el currículum vitae si existe
            if ($candidate->cv_file) {
                Storage::disk('public')->delete($candidate->cv_file);
            }

            // Eliminar el candidato de la base de datos
            $candidate->delete();

            // Eliminar el usuario asociado si no tiene otros roles
            if ($authUser->roles()->count() === 1) {
                $authUser->delete();
            }

            return response()->json(['message' => 'Candidate profile deleted!'], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle exceptions and generate appropriate JSON response.
     *
     * @param Exception $exception The exception to handle.
     * @return JsonResponse
     */
    private function handleException(Exception $exception): JsonResponse
    {
        try {
            throw $exception;
        } catch (Exception $e) {
            // Definir mensajes de error en inglés para diferentes códigos de estado.
            $errorMessages = [
                400 => 'Bad Request: The request could not be processed due to a malformed client.',
                401 => 'Unauthorized: Access unauthorized.',
                403 => 'Forbidden: You do not have sufficient permissions to access this resource.',
                404 => 'Not Found: The requested resource could not be found.',
                500 => 'Internal Server Error: An unexpected server error occurred.',
                503 => 'Service Unavailable: The server is currently unable to handle the request due to maintenance or temporary overloading.',
            ];

            // Obtener el código de estado de la excepción (si está disponible) o usar 500 como predeterminado.
            $status = $exception->getCode() ?: 500;

            // Obtener el mensaje de error asociado con el código de estado proporcionado.
            $errorMessage = $errorMessages[$status] ?? 'Unknown Error: An unexpected error occurred.';

            // Combinar el mensaje de la excepción con el mensaje detallado del código de estado.
            $detailedMessage = $exception->getMessage() . ' ' . $errorMessage;

            // Registrar el error en el sistema de registro de Laravel.
            if ($status >= 500) {
                \Log::error('HTTP ' . $status . ': ' . $detailedMessage);
            } else {
                \Log::warning('HTTP ' . $status . ': ' . $detailedMessage);
            }

            // Devolver una respuesta JSON con el mensaje de error y el código de estado.
            return response()->json(['error' => $detailedMessage], $status);
        }
    }


}
