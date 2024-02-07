<?php

namespace App\Http\Controllers;

use App\Models\Company;
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

            // Extracting emails
            $email = $companies->pluck('user.email')->toArray();
            $job = $companies->pluck('companies.jobs')->toArray();

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
                'details' => 'Internal Server Error',
            ], 500);
        } catch (\Exception $e) {
            // Otros errores
            return response()->json([
                'error' => 'Error al crear la compañía.',
                'details' => 'Internal Server Error',
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
            return response()->json(['data' => $company], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the company!',
                'details' => $e->getMessage()
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
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $company->update($validatedData);
            return response()->json(['data' => $company, 'message' => 'Compañía actualizada con éxito.'], 200);
        } catch (\Exception $e) {
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
            $company->delete();
            return response()->json(['message' => 'Compañía eliminada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar la compañía.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
