<?php

namespace App\Http\Controllers;

use App\Models\EducationHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationHistoryController extends Controller
{
    /**
     * Display a listing of the education history.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Obtener todos los registros de historial académico
        $educationHistories = EducationHistory::all();

        // Retornar una vista o una respuesta JSON, según sea necesario
        return response()->json($educationHistories);
    }

    /**
     * Store a newly created education history in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validar los datos del formulario
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'institution' => 'required|string',
            'degree_title' => 'required|string',
            'field_of_study' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Crear un nuevo registro de historial académico
        $educationHistory = EducationHistory::create($request->all());

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Education history created successfully', 'data' => $educationHistory], 201);
    }

    /**
     * Display the specified education history.
     *
     * @param EducationHistory $educationHistory
     * @return JsonResponse
     */
    public function show(EducationHistory $educationHistory): JsonResponse
    {
        // Retornar el historial académico específico
        return response()->json($educationHistory);
    }

    /**
     * Update the specified education history in storage.
     *
     * @param Request $request
     * @param EducationHistory $educationHistory
     * @return JsonResponse
     */
    public function update(Request $request, EducationHistory $educationHistory): JsonResponse
    {
        // Validar los datos del formulario
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'institution' => 'required|string',
            'degree_title' => 'required|string',
            'field_of_study' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Actualizar el registro de historial académico
        $educationHistory->update($request->all());

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Education history updated successfully', 'data' => $educationHistory]);
    }

    /**
     * Remove the specified education history from storage.
     *
     * @param EducationHistory $educationHistory
     * @return JsonResponse
     */
    public function destroy(EducationHistory $educationHistory): JsonResponse
    {
        // Eliminar el registro de historial académico
        $educationHistory->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Education history deleted successfully']);
    }
}
