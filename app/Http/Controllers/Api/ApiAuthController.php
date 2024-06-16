<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ApiAuthController extends Controller
{
    /**
     * Registra un nuevo usuario y crea la instancia correspondiente (candidato o empresa).
     *
     * @param RegisterRequest $registerRequest
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
            ]);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Database error occurred while registering the user',
                'details' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            // Manejo de otras excepciones inesperadas
            return $this->handleException($e, 'An unexpected error occurred while registering the user');
        }
    }

    /**
     * Inicia sesión del usuario y devuelve el token de autenticación.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new AuthenticationException('Invalid credentials');
            }

            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'message' => 'User Logged Successfully!',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return $this->handleException($e, 'An unexpected error occurred while logging in');
        }
    }

    /**
     * Obtiene el perfil del usuario autenticado.
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new AuthenticationException('User not authenticated');
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
            ]);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return $this->handleException($e, 'An unexpected error occurred while fetching the user profile');
        }
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Actualización de contraseña.
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ], 204);
        } catch (Exception $e) {
            return $this->handleException($e, 'An unexpected error occurred while updating the password');
        }
    }

    /**
     * Cierra la sesión del usuario actual eliminando todos los tokens.
     *
     * @return JsonResponse
     */
    public function logOut(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Successfully Logged Out!',
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, 'Logout Error');
        }
    }

    /**
     * Maneja las excepciones no controladas y devuelve una respuesta JSON adecuada.
     *
     * @param Exception $e
     * @param string $errorMessage
     * @return JsonResponse
     */
    private function handleException(Exception $e, string $errorMessage): JsonResponse
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }
}
