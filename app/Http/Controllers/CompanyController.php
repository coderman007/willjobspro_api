<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

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
                $subquery->where('name', 'like', "%$searchTerm%")
                    ->orWhere('industry', 'like', "%$searchTerm%")
                    ->orWhere('contact_person', 'like', "%$searchTerm%");
            });
        }

        // Filtrado por campos específicos
        $filters = ['name', 'industry', 'status', 'contact_person'];

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
     * Store a newly created company instance in storage.
     *
     * @param StoreCompanyRequest $request The request containing company data.
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            // Validar y obtener los datos enviados en la solicitud
            $validatedData = $request->validated();

            // Obtener el usuario autenticado
            /* @var App\Models\User */
            $user = Auth::user();

            // Verificar si el usuario tiene el rol 'company'
            if (!$user->hasRole('company')) {
                return response()->json(['error' => 'User does not have the company role'], 403);
            }

            // Actualizar la ubicación del usuario (si se proporcionaron datos)
            $this->updateUserLocation($request, $user);

            // Generar nombres únicos para los archivos de imagen
            $logoName = $this->generateUniqueFileName($request->logo_file);
            $bannerName = $this->generateUniqueFileName($request->banner_file);

            // Crear la instancia de compañía en la base de datos
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'industry' => $validatedData['industry'],
                'phone_number' => $validatedData['phone_number'],
                'website' => $validatedData['website'],
                'description' => $validatedData['description'],
                'contact_person' => $validatedData['contact_person'],
                'logo_path' => $this->storeImageAndGetPath($logoName, $request->logo_file, 'company_uploads/logos'),
                'banner_path' => $this->storeImageAndGetPath($bannerName, $request->banner_file, 'company_uploads/banners'),
                'social_networks' => $validatedData['social_networks'],
                'status' => $validatedData['status'],
            ]);

            // Respuesta exitosa con la ruta de las imágenes
            return $this->jsonResponse([
                'company' => new CompanyResource($company),
                'logo_url' => $company->logo_path,
                'banner_url' => $company->banner_path,
            ], 'Company created successfully!', 201);
        } catch (UnauthorizedException $e) {
            return $this->jsonErrorResponse('User does not have the company role: ' . $e->getMessage(), 403);
        } catch (ValidationException $e) {
            return $this->jsonErrorResponse('Validation error: ' . $e->getMessage(), 422);
        } catch (QueryException $e) {
            return $this->jsonErrorResponse('Database error: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error creating company: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the user's location information.
     *
     * @param Request $request The request containing location data.
     * @param User $user The user instance to be updated.
     * @return void
     */
    private function updateUserLocation(Request $request, User $user): void
    {
        $updateData = [];

        // Obtener los datos de ubicación del usuario desde la solicitud
        $countryId = $request->input('country_id');
        $stateId = $request->input('state_id');
        $cityId = $request->input('city_id');
        $zipCodeId = $request->input('zip_code_id');

        // Actualizar los datos de ubicación si se proporcionaron en la solicitud
        if ($countryId) {
            $updateData['country_id'] = $countryId;
        }

        if ($stateId) {
            $updateData['state_id'] = $stateId;
        }

        if ($cityId) {
            $updateData['city_id'] = $cityId;
        }

        if ($zipCodeId) {
            $updateData['zip_code_id'] = $zipCodeId;
        }

        // Actualizar la ubicación del usuario en la base de datos
        $user->update($updateData);
    }

    /**
     * Generate a unique file name for the uploaded image.
     *
     * @param \Illuminate\Http\UploadedFile|null $file The uploaded file.
     * @return string|null
     */
    private function generateUniqueFileName($file): ?string
    {
        return $file ? Str::random(10) . "." . $file->getClientOriginalExtension() : null;
    }

    /**
     * Store the uploaded image and return its path.
     *
     * @param string $fileName The name of the file.
     * @param UploadedFile $file The uploaded file.
     * @param string $directory The directory to store the file.
     * @return string
     */
    private function storeImageAndGetPath(string $fileName, UploadedFile $file, string $directory): string
    {
        // Almacenar el archivo en el directorio especificado
        Storage::disk('public')->put("$directory/$fileName", file_get_contents($file->getPathname()));

        // Devolver la ruta completa del archivo almacenado
        return Storage::disk('public')->url("$directory/$fileName");
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
     * Update the specified resource in storage.
     *
     * @param UpdateCompanyRequest $request The update company request.
     * @param Company $company The company instance to be updated.
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(UpdateCompanyRequest $request, $userId): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Validar que el usuario autenticado tenga permisos para actualizar este compañía
            // /** @var \App\Models\User */
            // $user = Auth::user();


            $user = User::findOrFail($userId);

            if (!$user->hasRole('company') || $user->id !== $user->company->user_id) {
                return response()->json(['error' => 'Unauthorized to update this company'], 403);
            }
            // if (!$user->hasRole('company') || $user->id !== $userId) {
            //     return response()->json(['error' => 'Unauthorized to update this company'], 403);
            // }

            // Actualizar la ubicación del usuario en la tabla 'users'
            $user->update([
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'zip_code_id' => $request->input('zip_code_id'),
            ]);

            // Actualizar los campos de la compañía
            $user->company->update($validatedData);

            return $this->jsonResponse(new CompanyResource($user->company), 'Company updated successfully!', 200);
        } catch (UnauthorizedException $e) {
            return $this->jsonErrorResponse('Unauthorized action: ' . $e->getMessage(), 403);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error updating company: ' . $e->getMessage(), 500);
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
            /** @var \App\Models\User */
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
     * Function to generate a consistent JSON response
     *
     * @param mixed $data The data to include in the response
     * @param string $message The response message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonResponse($data, $message = null, $status = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Function to generate a consistent JSON error response
     *
     * @param string $message The error message
     * @param int $status The HTTP status code
     * @return JsonResponse
     */
    protected function jsonErrorResponse($message = null, $status = 500): JsonResponse
    {
        $response = [
            'error' => $message,
        ];

        return response()->json($response, $status);
    }
}
