<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Helpers\CompanyOwnershipValidator;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $query = Company::query()->with('user', 'jobs');

            // Search
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('industry', 'like', '%' . $searchTerm . '%')
                        ->orWhere('contact_person', 'like', '%' . $searchTerm . '%');
                });
            }

            // Filters
            $filters = [
                'name', 'industry', 'status', 'contact_person'
            ];

            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->query($filter));
                }
            }

            // Sorting
            if ($request->filled('sort_by') && $request->filled('sort_order')) {
                $sortBy = $request->query('sort_by');
                $sortOrder = $request->query('sort_order');
                $query->orderBy($sortBy, $sortOrder);
            }

            $companies = $query->paginate($perPage);

            $paginationData = [
                'total' => $companies->total(),
                'per_page' => $companies->perPage(),
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                // 'from' => $companies->firstItem(),
                // 'to' => $companies->lastItem(),
                // 'next_page_url' => $companies->nextPageUrl(),
                // 'prev_page_url' => $companies->previousPageUrl(),
                // 'path' => $companies->path(),
                // 'data' => $companies->items(),
            ];

            return $this->jsonResponse(CompanyResource::collection($companies), 'Companies retrieved successfully!', 200)
                ->header('X-Total-Count', $companies->total())
                ->header('X-Per-Page', $companies->perPage())
                ->header('X-Current-Page', $companies->currentPage())
                ->header('X-Last-Page', $companies->lastPage());
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving companies: ' . $e->getMessage(), 500);
        }
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
        $user = auth()->user();

        // Verificar si el usuario tiene el rol 'company'
        if (!$user->hasRole('company')) {
            return response()->json(['error' => 'User does not have the company role'], 403);
        }

        try {
            // Lógica para la generación de nombres simplificada
            $logoName = $this->generateFileName($request->logo_file);
            $bannerName = $this->generateFileName($request->banner_file);

            // Crear instancia en la tabla 'companies'
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'industry' => $validatedData['industry'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'website' => $validatedData['website'],
                'description' => $validatedData['description'],
                'contact_person' => $validatedData['contact_person'],
                'logo_path' => $logoName,
                'banner_path' => $bannerName,
                'social_networks' => $validatedData['social_networks'],
                'status' => $validatedData['status'],
            ]);

            // Guardar logo y banner en el directorio 'Storage'
            $this->storeFile($logoName, $request->logo_file, 'logos');
            $this->storeFile($bannerName, $request->banner_file, 'banners');


            return $this->jsonResponse(new CompanyResource($company), 'Company created successfully!', 201);
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
        return $file ? Str::random(32) . "." . $file->getClientOriginalExtension() : null;
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
     * @param Company $company
     * @return JsonResponse
     */
    public function show(Company $company): JsonResponse
    {
        try {
            // Get company details
            $companyDetail = new CompanyResource($company);

            return $this->jsonResponse($companyDetail, 'Company details retrieved successfully!', 200);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('Error retrieving company details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Validar propiedad de la compañía
            if (!CompanyOwnershipValidator::validateOwnership($company->id)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Actualizar los campos de la compañía
            $company->update($validatedData);

            return $this->jsonResponse(new CompanyResource($company), 'Company updated successfully!', 200);
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
            if (!CompanyOwnershipValidator::validateOwnership($company->id)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            $company->delete();

            return $this->jsonResponse(null, 'Company deleted successfully!', 200);
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
    protected function jsonResponse($data = null, $message = null, $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
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
            'success' => false,
            'error' => $message,
        ];

        return response()->json($response, $status);
    }
}
