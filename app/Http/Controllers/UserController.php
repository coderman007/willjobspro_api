<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    public function index(): JsonResource
    {
        try {
            $users = User::all();

            return UserResource::collection($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de usuarios.'], 500);
        }
    }



    public function show($id): JsonResponse
    {
        try {
            // Obtener el usuario por su ID
            $user = User::findOrFail($id);

            // Retornar una respuesta JSON con el usuario encontrado
            return response()->json(['data' => new UserResource($user)], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso en que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y retornar una respuesta de error
            return response()->json(['error' => 'Error al obtener el usuario.'], 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $storeValidatedData = $request->validated();

            // Crear un nuevo usuario
            $user = User::create([
                'name' => $storeValidatedData['name'],
                'email' => $storeValidatedData['email'],
                'password' => bcrypt($storeValidatedData['password']),
                'role' => $storeValidatedData['role'], // Asumiendo que 'role' está presente en la solicitud
            ]);

            // Retornar una respuesta JSON con el usuario recién creado
            return response()->json(['data' => new UserResource($user), 'message' => 'Usuario creado con éxito.'], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar la excepción de la base de datos
            return response()->json(['error' => 'Error en la base de datos al crear un nuevo usuario.'], 500);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y retornar una respuesta de error
            return response()->json(['error' => 'Error al crear un nuevo usuario.'], 500);
        }
    }



    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            // Obtener el usuario que se va a actualizar
            $user = User::findOrFail($id);

            // Validar los datos recibidos en la solicitud
            $updateValidatedData = $request->validated();

            // Actualizar los campos necesarios
            $user->name = $updateValidatedData['name'];
            $user->email = $updateValidatedData['email'];

            // Si se proporciona un nuevo rol, actualizarlo
            if (isset($updateValidatedData['role'])) {
                $user->role = $updateValidatedData['role'];
            }

            // Si se proporciona una nueva contraseña, cifrarla y actualizarla
            if (isset($updateValidatedData['password'])) {
                $user->password = bcrypt($updateValidatedData['password']);
            }

            // Guardar los cambios en el usuario
            $user->save();

            // Retornar una respuesta JSON con el usuario actualizado
            return response()->json(['data' => new UserResource($user), 'message' => 'Usuario actualizado con éxito.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso en que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y retornar una respuesta de error
            return response()->json(['error' => 'Error al actualizar el usuario.'], 500);
        }
    }



    public function destroy($id): JsonResponse
    {
        try {
            // Obtener el usuario por su ID
            $user = User::findOrFail($id);

            // Eliminar el usuario
            $user->delete();

            // Retornar una respuesta JSON exitosa
            return response()->json(['message' => 'Usuario eliminado con éxito.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso en que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y retornar una respuesta de error
            return response()->json(['error' => 'Error al eliminar el usuario.'], 500);
        }
    }
}
