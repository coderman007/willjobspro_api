<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $query = Company::query();

            // Búsqueda
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where('contact_person', 'like', '%' . $searchTerm . '%')
                    ->orWhere('industry', 'like', '%' . $searchTerm . '%');
            }

            // Filtros
            $filters = [
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

            $companies = $query->paginate($perPage);

            // Transformar la colección de compañías utilizando CompanyResource
            $companiesResource = CompanyResource::collection($companies);

            return response()->json(['data' => $companiesResource], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $companyId): JsonResponse
    {
        try {
            $company = Company::findOrFail($companyId);

            $companyResource = new CompanyResource($company);

            return response()->json([
                'message' => 'Company information retrieved successfully!',
                'data' => $companyResource,
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $company = new Company;
            $company->user_id = $user->id;
            $company->fill($request->validated());

            // Almacenar archivos
            $this->storeFiles($company, $request);

            $company->save();

            // Sincronizar relaciones
            $this->syncRelations($company, $request);

            DB::commit();

            $companyResource = new CompanyResource($company);
            return response()->json([
                'message' => 'Company profile created successfully!',
                'data' => $companyResource,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    // Métodos restantes (update, destroy) también deben ser adaptados al contexto de Company

    // Métodos privados de almacenamiento de archivos y sincronización de relaciones permanecen igual

    // Método handleException permanece igual

}
