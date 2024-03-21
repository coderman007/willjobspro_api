<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Helpers\CompanyOwnershipValidator;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    private function buildCompanyQuery(Request $request)
    {
        $query = Company::with('user');

        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($subquery) use ($searchTerm) {
                $subquery->where('name', 'like', "%$searchTerm%")
                    ->orWhere('industry', 'like', "%$searchTerm%")
                    ->orWhere('contact_person', 'like', "%$searchTerm%");
            });
        }

        $filters = ['name', 'industry', 'status', 'contact_person'];

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->query('sort_by');
            $sortOrder = $request->query('sort_order');
            $query->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('with_jobs')) {
            $query->with('jobs');
        }

        return $query;
    }


    /**
     * Store a newly created company instance in storage.
     *
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {

        $validatedData = $request->validated();
        /** @var \App\Models\User */
        $user = Auth::user();

        // Verificar si el usuario tiene el rol 'company'
        if (!$user->hasRole('company')) {
            return response()->json(['error' => 'User does not have the company role'], 403);
        }

        try {

            // Obtener los datos de ubicación del usuario
            $countryId = $request->input('country_id');
            $stateId = $request->input('state_id');
            $cityId = $request->input('city_id');
            $zipCodeId = $request->input('zip_code_id');

            // Actualizar la ubicación del usuario (solo si se proporcionaron datos)
            $updateData = [];

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

            $user->update($updateData);

            // Lógica para la generación de nombres simplificada
            $logoName = 'logo_file_' . $user->id . $this->generateFileName($request->logo_file);
            $bannerName = 'banner_file_' . $user->id . $this->generateFileName($request->banner_file);

            // Crear instancia en la tabla 'companies'
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'industry' => $validatedData['industry'],
                'phone_number' => $validatedData['phone_number'],
                'website' => $validatedData['website'],
                'description' => $validatedData['description'],
                'contact_person' => $validatedData['contact_person'],
                'logo_path' => 'companies/logos/' . $logoName,
                'banner_path' => 'companies/banners/' . $bannerName,
                'social_networks' => $validatedData['social_networks'],
                'status' => $validatedData['status'],
            ]);

            // Almacenamiento de archivos de la compañía en el directorio correspondiente
            $this->storeFile($logoName, $request->logo_file, 'company_uploads/logos');
            $this->storeFile($bannerName, $request->banner_file, 'company_uploads/banners');


            return $this->jsonResponse(new CompanyResource($company), 'Company created successfully!', 201);
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
     * Genera un nombre de archivo único.
     *
     * @param \Illuminate\Http\UploadedFile|null $file
     * @return string|null
     */
    private function generateFileName($file): ?string
    {
        return $file ? Str::random(10) . "." . $file->getClientOriginalExtension() : null;
    }

    /**
     * Almacena un archivo en el disco.
     *
     * @param string $fileName
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    private function storeFile($fileName, $file, $directory): void
    {
        Storage::disk('public')->put("$directory/$fileName", file_get_contents($file));
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
