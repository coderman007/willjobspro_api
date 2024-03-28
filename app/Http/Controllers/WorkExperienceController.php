<?php

namespace App\Http\Controllers;

use App\Models\WorkExperience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the work experiences.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Verificar que el usuario autenticado tiene el rol de candidato
        if (!Auth::user()->hasRole('candidate')) {
            return response()->json(['message' => 'No tienes permisos para ver esta información.'], 403);
        }

        // Obtener todas las experiencias laborales del candidato autenticado
        $workExperiences = Auth::user()->candidate->workExperiences;

        // Retornar una respuesta JSON con las experiencias laborales
        return response()->json($workExperiences);
    }

    /**
     * Store a newly created work experience in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Verificar que el usuario autenticado tiene el rol de candidato
        if (!Auth::user()->hasRole('candidate')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar que el usuario autenticado tenga un candidato asociado
        if (!Auth::user()->candidate) {
            return response()->json(['message' => 'No tienes un perfil de candidato asociado.'], 403);
        }

        // Validar los datos del formulario
        $request->validate([
            'company' => 'required|string',
            'position' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Crear una nueva experiencia laboral asociada al candidato autenticado
        $workExperience = new WorkExperience($request->all());
        $workExperience->candidate()->associate(Auth::user()->candidate);
        $workExperience->save();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Work experience created successfully', 'data' => $workExperience], 201);
    }

    /**
     * Display the specified work experience.
     *
     * @param WorkExperience $workExperience
     * @return JsonResponse
     */
    public function show(WorkExperience $workExperience): JsonResponse
    {
        // Verificar que el usuario autenticado tiene el rol de candidato
        if (!Auth::user()->hasRole('candidate')) {
            return response()->json(['message' => 'No tienes permisos para ver esta información.'], 403);
        }

        // Verificar que el candidato autenticado sea dueño de la experiencia laboral
        if ($workExperience->candidate_id !== Auth::user()->candidate->id) {
            return response()->json(['message' => 'No tienes permisos para ver esta experiencia laboral.'], 403);
        }

        // Retornar la experiencia laboral específica
        return response()->json($workExperience);
    }

    /**
     * Update the specified work experience in storage.
     *
     * @param Request $request
     * @param WorkExperience $workExperience
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, WorkExperience $workExperience): JsonResponse
    {
        // Verificar que el usuario autenticado tiene el rol de candidato
        if (!Auth::user()->hasRole('candidate')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Verificar que el candidato autenticado sea dueño de la experiencia laboral
        if ($workExperience->candidate_id !== Auth::user()->candidate->id) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta experiencia laboral.'], 403);
        }

        // Validar los datos del formulario
        $request->validate([
            'company' => 'required|string',
            'position' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Actualizar la experiencia laboral
        $workExperience->update($request->all());

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Work experience updated successfully', 'data' => $workExperience]);
    }

    /**
     * Remove the specified work experience from storage.
     *
     * @param WorkExperience $workExperience
     * @return JsonResponse
     */
    public function destroy(WorkExperience $workExperience): JsonResponse
    {
        // Verificar que el usuario autenticado tiene el rol de candidato
        if (!Auth::user()->hasRole('candidate')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Verificar que el candidato autenticado sea dueño de la experiencia laboral
        if ($workExperience->candidate_id !== Auth::user()->candidate->id) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta experiencia laboral.'], 403);
        }

        // Eliminar la experiencia laboral
        $workExperience->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Work experience deleted successfully']);
    }
}
