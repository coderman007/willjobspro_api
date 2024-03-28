<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Permitir que todos los usuarios autenticados vean la lista de idiomas
        $languages = Language::all();

        return response()->json(['languages' => $languages], 200);
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

        // Validar los datos de entrada y crear el idioma
        $request->validate([
            'name' => 'required|string|unique:languages,name',
        ]);

        $language = Language::create([
            'name' => $request->name,
        ]);

        return response()->json(['language' => $language], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        return response()->json(['language' => $language], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar los datos de entrada y actualizar el idioma
        $request->validate([
            'name' => 'string|unique:languages,name,' . $language->id,
        ]);

        $language->update([
            'name' => $request->name ?? $language->name,
        ]);


        return response()->json(['language' => $language], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        // Validar que el usuario tenga el rol de admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Eliminar el idioma
        $language->delete();

        return response()->json(null, 204);
    }
}
