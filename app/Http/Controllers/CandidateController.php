<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Http\Resources\JobResource;
use App\Models\Candidate;
use App\Models\EducationHistory;
use App\Models\EducationLevel;
use App\Models\Job;
use App\Models\Language;
use App\Models\SocialNetwork;
use App\Models\WorkExperience;
use App\Services\LocationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CandidateController extends Controller
{
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

            return response()->json(['data' => $candidatesResource]);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function getAllApplications(): JsonResponse
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
                    'candidate_id' => $application->candidate_id,
                    'job_id' => $application->job_id,
                    'job_title' => $application->job->title,
                    'job_salary' => $application->job->salary,
                    'company_id' => $application->job->company_id,
                    'company_name' => $application->job->company->user->name,
                ];
            }
            // Devolver la información de las aplicaciones
            return response()->json(['data' => $applicationsData]);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function getUnappliedJobs(): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado tiene el rol 'candidate'
            $user = Auth::user();
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Obtener el candidato actual
            $candidate = $user->candidate;

            // Obtener todas las aplicaciones del candidato
            $appliedJobIds = $candidate->applications()->pluck('job_id')->toArray();

            // Obtener todas las ofertas de trabajo que el candidato no ha aplicado
            $unappliedJobs = Job::whereNotIn('id', $appliedJobIds)->get();

            // Transformar las ofertas de trabajo utilizando el recurso JobResource
            $unappliedJobsData = JobResource::collection($unappliedJobs);

            // Devolver la información de las ofertas de trabajo no aplicadas
            return response()->json(['data' => $unappliedJobsData]);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
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
//            $skills = $candidate->skills;

            // Transformar el candidato a un recurso CandidateResource
            $candidateResource = new CandidateResource($candidate);

            // Devolver la respuesta incluyendo las URL de las imágenes
            return response()->json([
                'message' => 'Candidate Profile Successfully Obtained!',
                'data' => [
                    'info' => $candidateResource,
                ],
            ]);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized. You can\'t do this!'], 401);
            }

            $user = Auth::user();

            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'You do not have permission to create a candidate'], 403);
            }

            // Verificar si el usuario autenticado ya tiene un perfil de candidato
            $user = Auth::user();
            if ($user->candidate) {
                return response()->json(['error' => 'You already have a candidate profile'], 422);
            }

            $candidate = new Candidate;
            $candidate->user_id = $user->id;
            $candidate->fill($request->validated());

            // Almacenar los archivos codificados en Base64
            $this->storeBase64Files($candidate, $request);

            $candidate->save();

            // Crear o actualizar ubicaciones utilizando LocationService
            $locationService = new LocationService();
            $locationResult = $locationService->createAndAssociateLocation($request->input('location'), $user);

            // Verificar si ocurrieron errores durante la creación de ubicaciones
            if ($locationResult !== true) {
                // Si hay errores, revertir cambios en la base de datos y devolver los errores
                DB::rollBack();
                return response()->json(['errors' => $locationResult['errors']], 422);
            }

            // Crear las habilidades asociadas al candidato (opcional)
            if ($request->filled('skills')) {
                $skills = $request->input('skills');
                $candidate->skills()->syncWithoutDetaching($skills);
            }

            // Crear la experiencia laboral asociada al candidato (opcional)
            if ($request->filled('work_experiences')) {
                foreach ($request->work_experiences as $workData) {
                    $workExperience = new WorkExperience();
                    $workExperience->candidate_id = $candidate->id;
                    $workExperience->fill($workData);
                    $workExperience->save();
                }
            }

            // Verificar si se proporciona información sobre el historial académico
            if ($request->filled('education_history')) {
                foreach ($request->education_history as $educationData) {
                    // Verificar si se proporciona el nivel de educación para este historial académico
                    if (!isset($educationData['education_level_id'])) {
                        // Si no se proporciona el nivel de educación, retornar un error
                        return response()->json(['error' => 'Education level ID is required for each education record'], 422);
                    }

                    // Verificar si el nivel de educación proporcionado es válido
                    if (!EducationLevel::where('id', $educationData['education_level_id'])->exists()) {
                        // Si el nivel de educación no es válido, retornar un error
                        return response()->json(['error' => 'Invalid education level ID provided'], 422);
                    }

                    // Crear el historial académico asociado al candidato y al nivel de educación
                    $education = new EducationHistory();
                    $education->candidate_id = $candidate->id;
                    $education->education_level_id = $educationData['education_level_id'];
                    $education->fill($educationData);
                    $education->save();
                }
            }

            // Sincronizar Idiomas
            $languages = $request->input('languages') ?? [];
            foreach ($languages as $languageData) {
                // Verificar si el idioma está presente en la base de datos
                $language = Language::find($languageData['id']);
                if ($language) {
                    // Asociar el idioma con el nivel correspondiente al candidato
                    $candidate->languages()->attach($language->id, ['level' => $languageData['level']]);
                }
            }

            // Asociar redes sociales al usuario
            if ($request->filled('social_networks')) {
                foreach ($request->social_networks as $socialNetworkData) {
                    $socialNetwork = new SocialNetwork();
                    $socialNetwork->user_id = $user->id;
                    $socialNetwork->fill($socialNetworkData);
                    $socialNetwork->save();
                }
            }

            DB::commit();

            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile created successfully!',
                'data' => $candidateResource,
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleException($e);
        }
    }
    private function storeBase64Files(Candidate $candidate, Request $request): void
    {
        if ($request->has('cv')) {
            $cvBase64 = $request->input('cv');
            $cvName = Str::random(40) . '.pdf'; // Nombre de archivo predeterminado o según el tipo de archivo
            $cvPath = 'candidate_uploads/cvs/' . $cvName;
            Storage::disk('public')->put($cvPath, base64_decode($cvBase64));
            $candidate->cv = $cvPath;
        }

        if ($request->has('photo')) {
            $photoBase64 = $request->input('photo');
            $photoName = Str::random(40) . '.jpg'; // Nombre de archivo predeterminado o según el tipo de archivo
            $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
            Storage::disk('public')->put($photoPath, base64_decode($photoBase64));
            $candidate->photo = $photoPath;
        }

        if ($request->has('banner')) {
            $bannerBase64 = $request->input('banner');
            $bannerName = Str::random(40) . '.jpg'; // Nombre de archivo predeterminado o según el tipo de archivo
            $bannerPath = 'candidate_uploads/banners/' . $bannerName;
            Storage::disk('public')->put($bannerPath, base64_decode($bannerBase64));
            $candidate->banner = $bannerPath;
        }
    }
    public function update(UpdateCandidateRequest $request, int $userId): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Verificar la autorización y la autenticación del usuario
            $authUser = Auth::user();
            if (!$authUser || $authUser->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener el candidato asociado al ID de usuario
            $candidate = $authUser->candidate;

            // Verificar si el candidato existe
            if (!$candidate) {
                return response()->json(['error' => 'Candidate profile not found'], 404);
            }

            // Almacenar los archivos codificados en Base64 (si se proporcionan)
            if ($request->has('cv')) {
                $cvBase64 = $request->input('cv');
                $cvName = Str::random(40) . '.pdf';
                $cvPath = 'candidate_uploads/cvs/' . $cvName;
                Storage::disk('public')->put($cvPath, base64_decode($cvBase64));
                Storage::disk('public')->delete($candidate->cv); // Eliminar el archivo existente
                $candidate->cv = $cvPath;
            }

            if ($request->has('photo')) {
                $photoBase64 = $request->input('photo');
                $photoName = Str::random(40) . '.jpg';
                $photoPath = 'candidate_uploads/profile_photos/' . $photoName;
                Storage::disk('public')->put($photoPath, base64_decode($photoBase64));
                Storage::disk('public')->delete($candidate->photo); // Eliminar el archivo existente
                $candidate->photo = $photoPath;
            }

            if ($request->has('banner')) {
                $bannerBase64 = $request->input('banner');
                $bannerName = Str::random(40) . '.jpg';
                $bannerPath = 'candidate_uploads/banners/' . $bannerName;
                Storage::disk('public')->put($bannerPath, base64_decode($bannerBase64));
                Storage::disk('public')->delete($candidate->banner); // Eliminar el archivo existente
                $candidate->banner = $bannerPath;
            }

            // Actualizar los campos del candidato excepto los archivos
            $candidate->update($request->except(['cv', 'photo', 'banner']));

            // Crear o actualizar ubicaciones utilizando LocationService
            $locationService = new LocationService();
            $locationResult = $locationService->updateAndAssociateLocation($request->input('location'), $authUser);

            // Verificar si ocurrieron errores durante la creación de ubicaciones
            if ($locationResult !== true) {
                // Si hay errores, revertir cambios en la base de datos y devolver los errores
                DB::rollBack();
                return response()->json(['errors' => $locationResult['errors']], 422);
            }

            // Actualizar la relación de habilidades del candidato (opcional)
            if ($request->filled('skills')) {
                $skills = $request->input('skills');
                $candidate->skills()->sync($skills);
            }

            // Actualizar la relación de experiencias laborales del candidato (opcional)
            if ($request->filled('work_experiences')) {
                $candidate->workExperiences()->delete(); // Eliminar todas las experiencias laborales existentes
                foreach ($request->work_experiences as $workData) {
                    $workExperience = new WorkExperience();
                    $workExperience->candidate_id = $candidate->id;
                    $workExperience->fill($workData);
                    $workExperience->save();
                }
            }

            // Actualizar la relación de historial académico del candidato (opcional)
            if ($request->filled('education_history')) {
                $candidate->educationHistory()->delete(); // Eliminar todos los registros existentes
                foreach ($request->education_history as $educationData) {
                    // Verificar si se proporciona el nivel educativo
                    if (!isset($educationData['education_level_id'])) {
                        return response()->json(['error' => 'Education level ID is required for updating education history'], 422);
                    }

                    $education = new EducationHistory();
                    $education->candidate_id = $candidate->id;
                    $education->fill($educationData);
                    $education->save();
                }
            }

            // Actualizar las redes sociales asociadas al usuario (opcional)
            if ($request->filled('social_networks')) {
                $authUser->socialNetworks()->delete(); // Eliminar las redes sociales existentes
                foreach ($request->social_networks as $socialNetworkData) {
                    $socialNetwork = new SocialNetwork();
                    $socialNetwork->user_id = $authUser->id;
                    $socialNetwork->fill($socialNetworkData);
                    $socialNetwork->save();
                }
            }

            DB::commit();

            // Devolver la respuesta con éxito
            $candidateResource = new CandidateResource($candidate);
            return response()->json([
                'message' => 'Candidate profile updated successfully!',
                'data' => $candidateResource,
            ]);

        } catch (Exception $e) {
            // Si ocurre alguna excepción, revertir los cambios y devolver el error
            DB::rollBack();
            return $this->handleException($e);
        }
    }

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

            // Eliminar el candidato y su perfil asociado
            $candidate->delete();

            // Return success response
            return response()->json(['message' => 'Candidate profile deleted!']);

        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
    private function handleException(Exception $exception): JsonResponse
    {
        $statusCode = $exception->getCode() ?: 500;
        $message = $exception->getMessage() ?: 'Internal Server Error';

        return response()->json(['error' => $message], $statusCode);
    }

}
