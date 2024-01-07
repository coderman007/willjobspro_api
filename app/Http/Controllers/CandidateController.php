<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $candidates = Candidate::all();
            return response()->json(['data' => $candidates], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de candidatos.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $candidate = Candidate::create($validatedData);
            return response()->json(['data' => $candidate, 'message' => 'Candidato creado con éxito.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el candidato.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function show(Candidate $candidate): JsonResponse
    {
        try {
            return response()->json(['data' => $candidate], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el candidato.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCandidateRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $candidate->update($validatedData);
            return response()->json(['data' => $candidate, 'message' => 'Candidato actualizado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el candidato.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function destroy(Candidate $candidate): JsonResponse
    {
        try {
            $candidate->delete();
            return response()->json(['message' => 'Candidato eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el candidato.'], 500);
        }
    }
}
