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

            return response()->json(['data' => CompanyResource::collection($companies), 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while trying to retrieve company information',
                'details' => $e->getMessage(),
            ], 500);
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
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            // Verificar si el usuario tiene el rol 'company'
            if (!$user->hasRole('company')) {
                return response()->json(['error' => 'User does not have the company role'], 403);
            }

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
                'logo_path' => $validatedData['logo_path'],
                'social_networks' => $validatedData['social_networks'],
                'status' => $validatedData['status'],
            ]);

            return response()->json(['data' => new CompanyResource($company), 'message' => 'Company Created Successfully!'], 201);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Error en la base de datos al intentar crear la compañía.',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Otros errores
            return response()->json([
                'error' => 'Error al crear la compañía.',
                'details' => $e->getMessage(),
            ], 500);
        }
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

            return response()->json(['data' => $companyDetail], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while trying to retrieve company information',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        try {
            $validatedData = $request->validated();

            // Validar propiedad de la compañía
            if (!CompanyOwnershipValidator::validateOwnership($company->id)) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            // Actualizar los campos de la compañía
            $company->update([
                'name' => $validatedData['name'],
                'industry' => $validatedData['industry'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'website' => $validatedData['website'],
                'description' => $validatedData['description'],
                'contact_person' => $validatedData['contact_person'],
                'logo_path' => $validatedData['logo_path'],
                'social_networks' => $validatedData['social_networks'],
                'status' => $validatedData['status'],
            ]);

            // Devolver una respuesta exitosa
            return response()->json(['data' => new CompanyResource($company), 'message' => 'Company Updated Successfully!'], 200);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Error en la base de datos al intentar actualizar la compañía.',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Otros errores
            return response()->json([
                'error' => 'Error al actualizar la compañía.',
                'details' => $e->getMessage(),
            ], 500);
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
            return response()->json(['message' => 'Company has been deleted.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while trying to delete the company.',
                'details' => $e->getMessage()
            ], 500);
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

            return response()->json(['data' => $uniqueApplicants], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting company applicants!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
