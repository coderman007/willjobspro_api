<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::all();

            return response()->json(['data' => $users], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la lista de usuarios.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }



    public function show($id): JsonResponse
    {
        try {
            // Obtener el usuario por su ID
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['error' => 'No pudimos encontrar el usuario'], 401);
            }

            $roles = $user->getRoleNames();

            return response()->json([
                'message' => 'User Profile Successfully Obtained!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'roles' => $roles,
                ]
            ], 200);

            // Retornar una respuesta JSON con el usuario encontrado
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso en que no se encuentra el usuario
            return response()->json([
                'error' => 'Usuario no encontrado.',
                'details' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y retornar una respuesta de error
            return response()->json([
                'error' => 'Error al obtener el usuario.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = auth()->user();

        try {
            $updateData = [];

            // Verificar si 'name' está presente en la solicitud antes de agregarlo a los datos de actualización
            if ($request->filled('name')) {
                $updateData['name'] = $request->input('name');
            }

            // Verificar si 'email' está presente en la solicitud antes de agregarlo a los datos de actualización
            if ($request->filled('email')) {
                $updateData['email'] = $request->input('email');
            }

            // Actualizar el usuario solo si hay datos para actualizar
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Si necesitas actualizar el rol, asegúrate de que sea una operación permitida
            if ($request->filled('role')) {
                $user->syncRoles([$request->input('role')]);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error while updating the user',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while updating the user',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            // Puedes retornar un mensaje de éxito u otros datos necesarios
            return response()->json(['message' => 'Usuario eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el usuario.'], 500);
        }
    }
}
