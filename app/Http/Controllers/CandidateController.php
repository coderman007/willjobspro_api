<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use Illuminate\Database\QueryException;
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
     * Store a newly created candidate instance in storage.
     *
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            // Verificar si el usuario tiene el rol 'candidate'
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'User does not have the candidate role'], 403);
            }

            // Crear instancia en la tabla 'candidates'
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'full_name' => $validatedData['full_name'],
                'gender' => $validatedData['gender'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'work_experience' => $validatedData['work_experience'],
                'education' => $validatedData['education'],
                'skills' => $validatedData['skills'],
                'certifications' => $validatedData['certifications'],
                'languages' => $validatedData['languages'],
                'references' => $validatedData['references'],
                'expected_salary' => $validatedData['expected_salary'],
                'cv_path' => $validatedData['cv_path'],
                'photo_path' => $validatedData['photo_path'],
                'status' => $validatedData['status'],
            ]);

            return response()->json(['data' => $candidate, 'message' => 'Candidate Created Successfully!'], 201);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Ha ocurrido un error en la base de datos al intentar crear el candidato.',
                'details' => $e->getMessage(), // Agrega esta línea para obtener detalles específicos
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el candidato.',
                'details' => $e->getMessage(), // Agrega esta línea para obtener detalles específicos
            ], 500);
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
            return response()->json([
                'data' => $candidate,
                'role' => 'candidate',
            ], 200);
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
