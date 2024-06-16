<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ApiUserController extends Controller
{
    /**
     * Handle an exception.
     *
     * @param Exception $e
     * @param string $errorMessage
     * @return JsonResponse
     */
    private function handleException(Exception $e, string $errorMessage): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $users = User::with('roles');

            if ($request->has('name')) {
                $users->where('name', 'like', '%' . $request->input('name') . '%');
            }

            if ($request->has('email')) {
                $users->where('email', 'like', '%' . $request->input('email') . '%');
            }

            if ($request->has('role')) {
                $role = Role::where('name', $request->input('role'))->first();

                if ($role) {
                    $users->whereHas('roles', function ($query) use ($role) {
                        $query->where('role_id', $role->id);
                    });
                }
            }

            if ($request->has('sort')) {
                $sortField = $request->input('sort');
                $users->orderBy($sortField);
            }

            // Paginate the users
            $paginatedUsers = $users->paginate($perPage)->items();

            // Retornar las ofertas de trabajo paginadas junto con datos adicionales
            return $this->jsonResponse(UserResource::collection($paginatedUsers), 'users retrieved successfully!');
        } catch (Exception $e) {
            // Manejar cualquier error y retornar una respuesta de error
            return $this->jsonErrorResponse('Error retrieving users: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
                'details' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while getting the user.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();

        try {
            $updateData = [];

            if ($request->filled('name')) {
                $updateData['name'] = $request->input('name');
            }

            if ($request->filled('email')) {
                $updateData['email'] = $request->input('email');
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            if ($request->filled('role')) {
                $user->syncRoles([$request->input('role')]);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error while updating the user',
                'details' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return $this->handleException($e, 'An unexpected error occurred while updating the user');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            if ($user->id !== Auth::id()) {
                return response()->json([
                    'error' => 'Unauthorized action! You can\'t delete this user.',
                ], 403);
            }

            $user->delete();

            return response()->json(['message' => 'User deleted']);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while deleting the user.');
        }

    }

    protected function jsonResponse(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    protected function jsonErrorResponse(?string $message = null, int $status = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        return response()->json($response, $status);
    }

}

