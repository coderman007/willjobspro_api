<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargar las habilidades con sus categorías de trabajo asociadas
        $skills = Skill::with('skillCategory')->get();

        return response()->json(['skills' => $skills], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar los datos de entrada y crear la habilidad
        $request->validate([
            'skill_category_id' => 'required|exists:skill_categories,id',
            'name' => 'required|string|unique:skills,name,NULL,id,skill_category_id,' . $request->input('skill_category_id'),
            'description' => 'nullable|string'
        ]);

        $skill = Skill::create([
            'skill_category_id' => $request->input('skill_category_id'),
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['skill' => $skill], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }
        
        return response()->json(['skill' => $skill], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar los datos de entrada y actualizar la habilidad
        $request->validate([
            'skill_category_id' => 'required|exists:skill_categories,id',
            'name' => 'required|string|unique:skills,name,' . $skill->id . ',id,skill_category_id,' . $request->input('skill_category_id'),
            'description' => 'nullable|string'
        ]);

        $skill->update([
            'skill_category_id' => $request->input('skill_category_id'),
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['skill' => $skill], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Eliminar la habilidad
        $skill->delete();

        return response()->json(null, 204);
    }
}
