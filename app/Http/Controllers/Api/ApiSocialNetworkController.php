<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiSocialNetworkController extends Controller
{
    /**
     * Display a listing of the social networks.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Obtener todas las redes sociales
        $socialNetworks = SocialNetwork::all();

        // Retornar una respuesta JSON con las redes sociales
        return response()->json($socialNetworks);
    }

    /**
     * Store a newly created social network in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validar que el usuario tenga el rol de administrador
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
        ]);

        // Crear una nueva red social
        $socialNetwork = SocialNetwork::create($request->all());

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Social network created successfully', 'data' => $socialNetwork], 201);
    }

    /**
     * Remove the specified social network from storage.
     *
     * @param SocialNetwork $socialNetwork
     * @return JsonResponse
     */
    public function destroy(SocialNetwork $socialNetwork): JsonResponse
    {
        // Validar que el usuario tenga el rol de administrador
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        // Eliminar la red social
        $socialNetwork->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Social network deleted successfully']);
    }
}
