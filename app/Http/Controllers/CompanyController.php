<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
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
            $query = $this->buildCompanyQuery($request);

            $companies = $query->paginate($perPage);

            return $this->jsonResponse(CompanyResource::collection($companies), 'Companies retrieved successfully!', 200)
                ->header('X-Total-Count', $companies->total())
                ->header('X-Per-Page', $companies->perPage())
                ->header('X-Current-Page', $companies->currentPage())
                ->header('X-Last-Page', $companies->lastPage());
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving companies: ' . $e->getMessage(), 500);
        }
    }

    private function buildCompanyQuery(Request $request): Builder
    {
        $query = Company::with('user');

        // Búsqueda por término de búsqueda global
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($subquery) use ($searchTerm) {
                $subquery->where('industry', 'like', "%$searchTerm%")
                    ->orWhere('contact_person', 'like', "%$searchTerm%");
            });
        }

        // Filtrado por campos específicos
        $filters = ['industry', 'status', 'contact_person'];

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%' . $request->query($filter) . '%');
            }
        }

        // Ordenamiento
        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            $query->orderBy($sortBy, $sortOrder);
        }

        // Carga condicional de relaciones
        if ($request->has('with_jobs')) {
            $query->with('jobs');
        }

        return $query;
    }

    /**
     * Display the specified resource.
     *
     * @param Company $company The company instance to be displayed.
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(Company $company): JsonResponse
    {
        try {
            // Get company details
            $companyDetail = new CompanyResource($company);

            return $this->jsonResponse($companyDetail, 'Company details retrieved successfully!', 200);
        } catch (ModelNotFoundException $e) {
            return $this->jsonErrorResponse('Company not found.' . $e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving company details: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        try {
            // Validar propiedad de la compañía
            $user = Auth::user();
            if (!$user->hasRole('company') || $user->id !== $company->user_id) {
                return response()->json(['error' => 'Unauthorized to delete this company'], 403);
            }

            $company->delete();
            $user->delete();

            return $this->jsonResponse(null, 'Company deleted!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error deleting company: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display all candidates who applied to jobs posted by the company.
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function getCompanyApplicants(Company $company): JsonResponse
    {
        try {
            // Obtener todos los trabajos publicados por la compañía
            $jobs = $company->jobs;

            // Obtener los candidatos que aplicaron a esos trabajos
            $applicants = [];
            foreach ($jobs as $job) {
                $applications = $job->applications()->with('candidate')->get();
                $applicants = array_merge($applicants, $applications->pluck('candidate')->toArray());
            }

            // Eliminar duplicados de la lista de candidatos
            $uniqueApplicants = collect($applicants)->unique('id')->values();

            return $this->jsonResponse($uniqueApplicants, 'Company applicants retrieved successfully!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving company applicants: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper method to create a JSON response.
     *
     * @param mixed $data The data to be included in the response.
     * @param string|null $message The message to be included in the response.
     * @param int $status The HTTP status code of the response.
     * @return JsonResponse
     */
    private function jsonResponse($data, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json(['message' => $message, 'data' => $data], $status);
    }

    /**
     * Helper method to create a JSON error response.
     *
     * @param string $message The error message.
     * @param int $status The HTTP status code of the response.
     * @return JsonResponse
     */
    private function jsonErrorResponse(string $message, int $status): JsonResponse
    {
        // Definir mensajes de error en inglés para diferentes códigos de estado.
        $errorMessages = [
            400 => 'Bad Request: The request could not be processed due to a malformed client.',
            401 => 'Unauthorized: Access unauthorized.',
            403 => 'Forbidden: You do not have sufficient permissions to access this resource.',
            404 => 'Not Found: The requested resource could not be found.',
            500 => 'Internal Server Error: An unexpected server error occurred.',
            503 => 'Service Unavailable: The server is currently unable to handle the request due to maintenance or temporary overloading.',
        ];

        // Verificar si el código de estado proporcionado tiene un mensaje de error asociado.
        $errorMessage = $errorMessages[$status] ?? 'Unknown Error: An unexpected error occurred.';

        // Combinar el mensaje proporcionado con el mensaje detallado del código de estado.
        $detailedMessage = $message . ' ' . $errorMessage;

        // Registrar el error en el sistema de registro de Laravel.
        if ($status >= 500) {
            // Podrías querer registrar errores del servidor para una mejor depuración.
            \Log::error('HTTP ' . $status . ': ' . $detailedMessage);
        } else {
            // Podrías querer registrar otros tipos de errores para seguimiento.
            \Log::warning('HTTP ' . $status . ': ' . $detailedMessage);
        }

        // Devolver una respuesta JSON con el mensaje de error y el código de estado.
        return response()->json(['error' => $detailedMessage], $status);
    }


    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            // Iniciar una transacción de base de datos para garantizar la consistencia
            DB::beginTransaction();

            // Verificar si el usuario está autenticado y tiene el rol de compañía
            if (!Auth::check() || !Auth::user()->hasRole('company')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener el usuario autenticado
            $user = Auth::user();

            $user->update($request->only(['country_id', 'state_id', 'city_id', 'zip_code_id']));

            // Crear una nueva instancia de Compañía
            $company = new Company;

            // Rellenar el modelo de compañía con los datos validados del formulario
            $company->fill($request->validated());

            // Asociar la compañía con el usuario autenticado
            $user->company()->save($company);

            $this->syncRelations($company, $request);

            // Almacenar los archivos (logo y banner) de la compañía
            $this->storeFiles($company, $request);

            // Confirmar la transacción
            DB::commit();

            // Devolver una respuesta JSON con el mensaje de éxito
            return response()->json([
                'message' => 'Company profile created successfully!',
                'data' => new CompanyResource($company),
            ], 201);

        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();

            // Manejar la excepción y devolver una respuesta JSON
            return $this->jsonErrorResponse('Error creating company profile: ' . $e->getMessage(), 500);
        }
    }

    private function syncRelations(Company $company, Request $request): void
    {
        // Sincronizar Redes sociales
        $socialNetworks = $request->input('social_networks') ? explode(',', $request->input('social_networks')) : [];
        $company->socialNetworks()->syncWithoutDetaching($socialNetworks);
    }

    private function storeFiles(Company $company, Request $request): void
    {
        if ($request->hasFile('logo_file')) {
            // Almacenamiento del logo
            $logoFile = $request->file('logo_file');
            $logoName = Str::random(40) . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = 'company_uploads/logos/' . $logoName;
            Storage::disk('public')->put($logoPath, file_get_contents($logoFile));
            $company->logo_file = $logoPath;
        }

        if ($request->hasFile('banner_file')) {
            // Almacenamiento del banner
            $bannerFile = $request->file('banner_file');
            $bannerName = Str::random(40) . '.' . $bannerFile->getClientOriginalExtension();
            $bannerPath = 'company_uploads/banners/' . $bannerName;
            Storage::disk('public')->put($bannerPath, file_get_contents($bannerFile));
            $company->banner_file = $bannerPath;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\Company $company
     * @return JsonResponse
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        try {
            // Iniciar una transacción de base de datos para garantizar la consistencia
            DB::beginTransaction();

            // Verificar si el usuario está autenticado y tiene el rol de compañía
            if (!Auth::check() || !Auth::user()->hasRole('company') || Auth::user()->id !== $company->user_id) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Obtener los datos de la solicitud
            $requestData = $request->all();

            // Actualizar los datos de la compañía con los datos de la solicitud
            $company->update($requestData);

            // Sincronizar las relaciones (por ejemplo, redes sociales)
            $this->syncRelations($company, $request);

            // Almacenar los archivos (logo y banner) de la compañía, si se han proporcionado
            $this->storeFiles($company, $request);

            // Confirmar la transacción
            DB::commit();

            // Devolver una respuesta JSON con el mensaje de éxito
            return response()->json([
                'message' => 'Company profile updated successfully!',
                'data' => new CompanyResource($company),
            ], 200);

        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();

            // Manejar la excepción y devolver una respuesta JSON
            return $this->jsonErrorResponse('Error updating company profile: ' . $e->getMessage(), 500);
        }
    }


}
