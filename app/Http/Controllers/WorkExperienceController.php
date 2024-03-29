<?php

namespace App\Http\Controllers;

use App\Models\WorkExperience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Obtener el candidato asociado al usuario autenticado
        $candidate = auth()->user()->candidate;

        // Verificar si el usuario tiene un candidato asociado
        if (!$candidate) {
            return response()->json(['error' => 'User is not associated with a candidate'], 400);
        }

        // Obtener todas las experiencias laborales asociadas al candidato
        $workExperiences = $candidate->workExperiences;

        return response()->json(['data' => $workExperiences]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Obtener el candidato asociado al usuario autenticado
        $candidate = auth()->user()->candidate;

        // Verificar si el usuario tiene un candidato asociado
        if (!$candidate) {
            return response()->json(['error' => 'User is not associated with a candidate'], 400);
        }

        // Validar los datos de entrada
        $request->validate([
            'company' => 'required|string',
            'position' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Crear la nueva experiencia laboral asociada al candidato
        $workExperience = $candidate->workExperiences()->create($request->all());

        return response()->json(['message' => 'Work experience created successfully.', 'data' => $workExperience], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param WorkExperience $workExperience
     * @return JsonResponse
     */
    public function show(WorkExperience $workExperience): JsonResponse
    {
        // Verificar si el usuario está autenticado
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Verificar si el usuario tiene un candidato asociado
        $candidate = $user->candidate;
        if (!$candidate) {
            return response()->json(['error' => 'User is not associated with a candidate'], 400);
        }

        // Verificar si la experiencia laboral pertenece al candidato asociado al usuario autenticado
        if ($candidate->id !== $workExperience->candidate_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['data' => $workExperience]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param WorkExperience $workExperience
     * @return JsonResponse
     */
    public function update(Request $request, WorkExperience $workExperience): JsonResponse
    {
        // Verificar si la experiencia laboral pertenece al usuario autenticado
        if (auth()->user()->candidate->id !== $workExperience->candidate_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validar los datos de entrada
        $validatedData = $request->validate([
            'company' => 'required|string',
            'position' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        try {
            // Actualizar la experiencia laboral
            $workExperience->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Failed to update work experience due to database error'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update work experience'], 500);
        }

        return response()->json(['message' => 'Work experience updated successfully.', 'data' => $workExperience]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param WorkExperience $workExperience
     * @return JsonResponse
     */

    public function destroy(WorkExperience $workExperience): JsonResponse
    {
        // Verificar si la experiencia laboral pertenece al usuario autenticado
        if (!auth()->user()->candidate->workExperiences->contains($workExperience)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $workExperience->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete work experience'], 500);
        }

        return response()->json(['message' => 'Work experience deleted successfully.']);
    }


}
