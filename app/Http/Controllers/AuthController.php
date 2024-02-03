<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario y crea la instancia correspondiente (candidato o empresa).
     *
     * @param RegisterRequest $registerRequest
     * @param StoreCandidateRequest $candidateRequest
     * @param StoreCompanyRequest $companyRequest
     * @return JsonResponse
     */
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        try {
            // Validar la solicitud
            $registerValidatedData = $registerRequest->validated();

            // Crear un nuevo usuario
            $user = User::create([
                'name' => $registerValidatedData['name'],
                'email' => $registerValidatedData['email'],
                'password' => Hash::make($registerValidatedData['password']),
            ]);

            // Asignar el rol al usuario
            $roleName = $registerValidatedData['role'];
            $role = Role::findByName($roleName);
            $user->assignRole($role);

            // Generar el token de Sanctum después de la creación del candidato o la empresa
            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'message' => 'User Created Successfully!',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], 200);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Database error occurred while registering the user',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Manejo de otras excepciones inesperadas
            return response()->json([
                'error' => 'An unexpected error occurred while registering the user',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }
        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'message' => 'User Logged Successfully!',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
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
        } catch (\Exception $e) {
            // Manejo de otras excepciones inesperadas
            return response()->json([
                'error' => 'An unexpected error occurred while fetching the user profile',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        try {
            // Actualización de contraseña. 
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ], 204);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Database error while updating the password',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Manejo de otras excepciones inesperadas
            return response()->json([
                'error' => 'An unexpected error occurred while updating the password',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function logOut(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Successfully Logged Out!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logout Error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
