<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Handle an exception.
     *
     * @param \Exception $e
     * @param string $errorMessage
     * @param int $statusCode
     * @return JsonResponse
     */
    private function handleException(\Exception $e, string $errorMessage, int $statusCode): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
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
                $users->orderBy($sortField, 'asc');
            }

            // Paginate the users
            $paginatedUsers = $users->paginate($perPage);

            return response()->json(['data' => $paginatedUsers], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'An error occurred while getting users', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
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
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
                'details' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return $this->handleException($e, 'An error ocurred while getting the user.', 500);
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
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error while updating the user',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return $this->handleException($e, 'An unexpected error occurred while updating the user', 500);
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

            return response()->json(['message' => 'User deleted'], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'An error ocurred while deleting the user.', 500);
        }
    }
}
