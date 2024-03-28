<?php

namespace App\Http\Controllers;

use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Permitir que todos los usuarios autenticados vean la lista de categorías de habilidades
        $categories = SkillCategory::all();
        return response()->json(['categories' => $categories], 200);
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

        // Validar los datos de entrada y crear la categoría de habilidad
        $request->validate([
            'name' => 'required|string|unique:skill_categories,name',
            'description' => 'nullable|string'
        ]);

        $category = SkillCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['category' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SkillCategory $skillCategory)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Permitir que todos los usuarios autenticados vean los detalles de la categoría de habilidad
        return response()->json(['category' => $skillCategory], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkillCategory $skillCategory)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar los datos de entrada y actualizar la categoría de habilidad
        $request->validate([
            'name' => 'required|string|unique:skill_categories,name,' . $skillCategory->id,
            'description' => 'nullable|string'
        ]);

        $skillCategory->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['category' => $skillCategory], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkillCategory $skillCategory)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Eliminar la categoría de habilidad
        $skillCategory->delete();

        return response()->json(null, 204);
    }
}
