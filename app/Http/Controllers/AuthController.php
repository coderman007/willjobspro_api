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

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $roleName = $request->input('role');
            $role = Role::findByName($roleName);
            $user->assignRole($role);

            $user->save();

            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'message' => 'User Created Successfully!',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], 201);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Database error while registering the user',
            ], 500);
        } catch (\Exception $e) {
            // Manejo de otras excepciones inesperadas
            return response()->json([
                'error' => 'An unexpected error occurred while registering the user',
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

            // Registro de actividad.
            activity()->log('Password updated for user ' . $user->id);

            return response()->json([
                'message' => 'Password updated successfully',
            ], 204);
        } catch (QueryException $e) {
            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'Database error while updating the password',
            ], 500);
        } catch (\Exception $e) {
            // Manejo de otras excepciones inesperadas
            return response()->json([
                'error' => 'An unexpected error occurred while updating the password',
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
            return response()->json(['error' => 'Logout Error'], 500);
        }
    }
}
