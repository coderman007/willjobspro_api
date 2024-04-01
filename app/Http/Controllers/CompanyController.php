<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\SocialNetwork;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Define el número de elementos por página, por defecto 20
            $perPage = $request->query('per_page', 20);

            // Inicia una consulta para obtener todas las compañías
            $query = Company::query();

            // Implementa la lógica para aplicar filtros, búsqueda y ordenación si es necesario

            // Búsqueda por nombre de compañía
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            // Filtros adicionales
            $filters = [
                'industry',
                'status',
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

            // Carga de relaciones si es necesario
            $query->with(['user.country', 'user.state', 'user.city', 'user.zipCode']);
            // Obtén las compañías paginadas
            $companies = $query->paginate($perPage);

            // Transforma la colección de compañías utilizando CompanyResource
            $companiesResource = CompanyResource::collection($companies);

            // Devuelve la respuesta JSON con las compañías paginadas
            return response()->json(['data' => $companiesResource], 200);
        } catch (Exception $e) {
            // Maneja cualquier excepción y devuelve una respuesta JSON con el mensaje de error
            return $this->handleException($e);
        }
    }


    public function show(int $companyId): JsonResponse
    {
        try {
            // Obtener la compañía asociada al ID de usuario
            $company = Company::where('user_id', $companyId)->first();

            // Verificar si la compañía existe
            if (!$company) {
                return response()->json(['error' => 'Company profile not found'], 404);
            }

            // Transforma la compañía a un recurso CompanyResource
            $companyResource = new CompanyResource($company);

            // Devuelve la respuesta JSON con la información de la compañía
            return response()->json([
                'message' => 'Company information successfully obtained!',
                'data' => [
                    'info' => $companyResource,
                ],
            ], 200);
        } catch (Exception $e) {
            // Maneja cualquier excepción y devuelve una respuesta JSON con el mensaje de error
            return $this->handleException($e);
        }
    }



    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            // Inicia una transacción de base de datos
            DB::beginTransaction();

            // Verifica si el usuario está autenticado
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtiene el usuario autenticado
            $user = Auth::user();

            // Verifica si el usuario tiene el rol de compañía
            if (!$user->hasRole('company')) {
                return response()->json(['error' => 'You do not have permission to create a company'], 403);
            }

            $user->update($request->only(['country_id', 'state_id', 'city_id', 'zip_code_id']));

            // Actualiza los campos de la compañía con los datos validados del formulario
            $company = new Company;
            $company->fill($request->validated());

            // Asocia el usuario autenticado como propietario de la compañía
            $company->user_id = $user->id;

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($company, $request);

            // Guarda la compañía en la base de datos
            $company->save();

            // Asocia las redes sociales a la compañía
            if ($request->filled('social_networks')) {
                foreach ($request->social_networks as $socialNetworkData) {
                    $socialNetwork = new SocialNetwork();
                    $socialNetwork->user_id = $user->id;
                    $socialNetwork->fill($socialNetworkData);
                    $socialNetwork->save();
                }
            }

            // Confirma la transacción de base de datos
            DB::commit();

            // Transforma la compañía a un recurso CompanyResource
            $companyResource = new CompanyResource($company);

            // Devuelve una respuesta JSON con el mensaje de éxito y la información de la compañía
            return response()->json([
                'message' => 'Company created successfully!',
                'data' => $companyResource,
            ], 201);

        } catch (Exception $e) {
            // En caso de error, hace un rollback de la transacción de base de datos
            DB::rollBack();

            // Maneja la excepción y devuelve una respuesta JSON con el mensaje de error
            return $this->handleException($e);
        }
    }


    public function update(UpdateCompanyRequest $request, int $userId): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Verificar la autorización y la autenticación del usuario
            $authUser = Auth::user();
            if (!$authUser || $authUser->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener la compañía asociada al ID de usuario
            $company = $authUser->company;

            // Verificar si la compañía existe
            if (!$company) {
                return response()->json(['error' => 'Company profile not found'], 404);
            }

            // Actualizar los campos del candidato
            $company->update($request->validated());

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($company, $request);

            // Actualiza la relación de redes sociales de la compañía (opcional)
            if ($request->filled('social_networks')) {
                $authUser->socialNetworks()->delete(); // Elimina las redes sociales existentes
                foreach ($request->social_networks as $socialNetworkData) {
                    $socialNetwork = new SocialNetwork();
                    $socialNetwork->user_id = $authUser->id;
                    $socialNetwork->fill($socialNetworkData);
                    $socialNetwork->save();
                }
            }

            DB::commit();

            // Devuelve una respuesta JSON con el mensaje de éxito y los datos actualizados de la compañía
            return response()->json([
                'message' => 'Company updated successfully!',
                'data' => new CompanyResource($company),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            // Maneja la excepción y devuelve una respuesta JSON con el mensaje de error
            return $this->handleException($e);
        }
    }


    public function destroy(int $userId): JsonResponse
    {
        try {
            // Verifica si el usuario está autenticado
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtiene el usuario autenticado
            $authUser = Auth::user();

            // Verifica si el usuario autenticado es el propietario de la compañía a eliminar
            if ($authUser->id !== $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtiene la compañía asociada al usuario
            $company = $authUser->company;

            // Verifica si la compañía existe
            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }

            // Elimina la compañía y sus relaciones
            $company->delete();

            // Devuelve una respuesta JSON con el mensaje de éxito
            return response()->json(['message' => 'Company deleted successfully!'], 200);

        } catch (Exception $e) {
            // Maneja la excepción y devuelve una respuesta JSON con el mensaje de error
            return $this->handleException($e);
        }
    }



    private function storeBase64Files(Company $company, Request $request): void
    {
        if ($request->has('logo_file_base64')) {
            $logoBase64 = $request->input('logo_file_base64');
            $logoName = Str::random(40) . '.png'; // Cambiar la extensión según el tipo de archivo permitido
            $logoPath = 'company_uploads/logos/' . $logoName;
            Storage::disk('public')->put($logoPath, base64_decode($logoBase64));
            $company->logo_file = $logoPath;
        }

        if ($request->has('banner_file_base64')) {
            $bannerBase64 = $request->input('banner_file_base64');
            $bannerName = Str::random(40) . '.jpg'; // Cambiar la extensión según el tipo de archivo permitido
            $bannerPath = 'company_uploads/banners/' . $bannerName;
            Storage::disk('public')->put($bannerPath, base64_decode($bannerBase64));
            $company->banner_file = $bannerPath;
        }
    }


    private function handleException(Exception $exception): JsonResponse
    {
        $statusCode = $exception->getCode() ?: 500;
        $message = $exception->getMessage() ?: 'Internal Server Error';

        return response()->json(['error' => $message], $statusCode);
    }

    public function getCompanyJobApplications(Request $request): JsonResponse
    {
        try {
            // Verificar si el usuario autenticado tiene el rol 'company'
            $user = Auth::user();
            if (!$user->hasRole('company')) {
                return response()->json(['error' => 'Unauthorized access to applications'], 403);
            }

            // Obtener la compañía autenticada y sus ofertas de trabajo
            $company = $user->company;
            $jobs = $company->jobs;

            // Verificar si la compañía ha publicado ofertas de trabajo
            if ($jobs->isEmpty()) {
                return response()->json(['message' => 'The company has not published any job offers yet'], 200);
            }

            // Inicializar array para almacenar las aplicaciones
            $applicationsData = [];

            // Variable para verificar si hay aplicaciones
            $hasApplications = false;

            // Recorrer las ofertas de trabajo de la compañía y obtener las aplicaciones asociadas
            foreach ($jobs as $job) {
                $applications = $job->applications()->with(['candidate'])->get();

                // Si hay aplicaciones, establecer la bandera en verdadero
                if ($applications->count() > 0) {
                    $hasApplications = true;
                }

                // Transformar las aplicaciones a un formato de respuesta
                foreach ($applications as $application) {
                    $applicationsData[] = [
                        'id' => $application->id,
                        'cover_letter' => $application->cover_letter,
                        'status' => $application->status,
                        'created_at' => $application->created_at,
                        'candidate_id' => $application->candidate_id,
                        'candidate_name' => $application->candidate->user->name, // Agregar el nombre del candidato
                        'candidate_email' => $application->candidate->user->email, // Agregar el correo electrónico del candidato
                        'job_id' => $application->job_id,
                        'job_title' => $job->title, // Agregar el título de la oferta de trabajo
                        'job_salary' => $job->salary, // Agregar el salario de la oferta de trabajo
                        'company_id' => $job->company_id, // Agregar el ID de la compañía
                        'company_name' => $company->user->name, // Agregar el nombre de la compañía
                    ];
                }
            }

            // Verificar si hay aplicaciones
            if (!$hasApplications) {
                return response()->json(['message' => 'No applications found for the company'], 200);
            }

            // Devolver la información de las aplicaciones
            return response()->json(['data' => $applicationsData], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


}

