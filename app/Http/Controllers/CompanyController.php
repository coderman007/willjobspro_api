<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $companies = Company::all();
            return response()->json(['data' => $companies], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de compañías.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $company = Company::create($validatedData);
            return response()->json(['data' => $company, 'message' => 'Compañía creada con éxito.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la compañía.'], 500);
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
            return response()->json(['error' => 'Error al obtener la compañía.'], 500);
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
            return response()->json(['error' => 'Error al actualizar la compañía.'], 500);
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
            return response()->json(['error' => 'Error al eliminar la compañía.'], 500);
        }
    }
}
