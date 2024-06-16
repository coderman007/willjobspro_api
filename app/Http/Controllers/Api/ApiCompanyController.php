<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\CompanyResource;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\SocialNetwork;
use App\Models\State;
use App\Models\ZipCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ApiCompanyController extends Controller
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
        // Inicia una transacción de base de datos
        DB::beginTransaction();

        try {
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

            // Verificar si el usuario autenticado ya tiene un perfil de compañía
            $user = Auth::user();
            if ($user->company) {
                return response()->json(['error' => 'You already have a company profile'], 422);
            }

            // Verificar si se proporciona una dirección en la solicitud
            if ($request->filled('address')) {
                // Guardar la dirección proporcionada en el usuario asociado
                $user->address = $request->input('address');
                $user->save();
            }

            // Gestionar ubicaciones
            if ($request->filled('location')) {
                // Obtener los datos de ubicación del formulario
                $locationData = $request->input('location');

                // Buscar o crear las ubicaciones correspondientes
                $country = Country::firstOrCreate([
                    'name' => $locationData['country'],
                    'dial_code' => $locationData['dial_code'],
                    'iso_alpha_2' => $locationData['iso_alpha_2']
                ]);
                $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
                $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
                $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

                // Asociar las ubicaciones con el usuario
                $user->country_id = $country->id;
                $user->state_id = $state->id;
                $user->city_id = $city->id;
                $user->zip_code_id = $zipCode->id;
                $user->save();
            }

            // Actualiza los campos de la compañía con los datos validados del formulario
            $company = new Company;
            $company->user_id = $user->id;
            $company->fill($request->validated());

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($company, $request);

            // Guarda la compañía en la base de datos
            $company->save();

            // Asocia las redes sociales al usuario
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

            // Devuelve una respuesta JSON con la información de la compañía
            return response()->json([
                'message' => 'Company profile created successfully!',
                'data' => $companyResource,
            ], 201);
        } catch (\Exception $e) {
            // En caso de error, hace un rollback de la transacción de base de datos
            DB::rollBack();

            // Devuelve una respuesta JSON con el código y el mensaje de error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function update(UpdateCompanyRequest $request, int $userId): JsonResponse
    {
        // Inicia una transacción de base de datos
        DB::beginTransaction();

        try {
            // Verificar la autorización y la autenticación del usuario
            $user = Auth::user();
            if (!$user || $user->id != $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener la compañía asociada al usuario autenticado
            $company = $user->company;

            // Verificar si la compañía existe
            if (!$company) {
                return response()->json(['error' => 'Company profile not found'], 404);
            }

            // Verificar si se proporciona una dirección en la solicitud
            if ($request->filled('address')) {
                // Eliminar la dirección anterior si existe
                if ($user->address) {
                    $user->address = null;
                    $user->save();
                }

                // Guardar la nueva dirección proporcionada en el usuario asociado
                $user->address = $request->input('address');
                $user->save();
            }

            // Gestionar ubicaciones
            if ($request->filled('location')) {
                // Obtener los datos de ubicación del formulario
                $locationData = $request->input('location');

                // Buscar o crear las ubicaciones correspondientes
                $country = Country::firstOrCreate([
                    'name' => $locationData['country'],
                    'dial_code' => $locationData['dial_code'],
                    'iso_alpha_2' => $locationData['iso_alpha_2']
                ]);
                $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
                $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
                $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

                // Asociar las ubicaciones con el usuario
                $user->country_id = $country->id;
                $user->state_id = $state->id;
                $user->city_id = $city->id;
                $user->zip_code_id = $zipCode->id;
                $user->save();
            }

            // Actualiza los campos de la compañía con los datos validados del formulario
            $company->fill($request->validated());

            // Almacena los archivos codificados en Base64
            $this->storeBase64Files($company, $request);

            // Guarda los cambios en la base de datos
            $company->save();

            // Confirma la transacción de base de datos
            DB::commit();

            // Transforma la compañía a un recurso CompanyResource
            $companyResource = new CompanyResource($company);

            // Devuelve una respuesta JSON con la información de la compañía actualizada
            return response()->json([
                'message' => 'Company profile updated successfully!',
                'data' => $companyResource,
            ], 200);
        } catch (\Exception $e) {
            // En caso de error, hace un rollback de la transacción de base de datos
            DB::rollBack();

            // Devuelve una respuesta JSON con el código y el mensaje de error
            return response()->json(['error' => $e->getMessage()], 500);
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
            $user = Auth::user();

            // Verifica si el usuario autenticado es el propietario de la compañía a eliminar
            if ($user->id !== $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtiene la compañía asociada al usuario
            $company = $user->company;

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
        if ($request->has('logo')) {
            $logoBase64 = $request->input('logo');
            $logoName = Str::random(40) . '.png'; // Cambiar la extensión según el tipo de archivo permitido
            $logoPath = 'company_uploads/logos/' . $logoName;
            Storage::disk('public')->put($logoPath, base64_decode($logoBase64));
            $company->logo = $logoPath;
        }

        if ($request->has('banner')) {
            $bannerBase64 = $request->input('banner');
            $bannerName = Str::random(40) . '.jpg'; // Cambiar la extensión según el tipo de archivo permitido
            $bannerPath = 'company_uploads/banners/' . $bannerName;
            Storage::disk('public')->put($bannerPath, base64_decode($bannerBase64));
            $company->banner = $bannerPath;
        }

        if ($request->has('video')) {
            $videoBase64 = $request->input('video');
            $videoName = Str::random(40) . '.mp4'; // Cambiar la extensión según el tipo de archivo permitido
            $videoPath = 'company_uploads/videos/' . $videoName;
            Storage::disk('public')->put($videoPath, base64_decode($videoBase64));
            $company->video = $videoPath;
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

                // Transformar las aplicaciones utilizando ApplicationResource
                $applicationsResource = ApplicationResource::collection($applications);

                // Agregar las aplicaciones transformadas al array de datos
                $applicationsData = array_merge($applicationsData, $applicationsResource->toArray($request));
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