<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function handleException(\Exception $e, $errorMessage, $statusCode): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get the number of items per page from the request
            $perPage = $request->query('per_page', 10);

            // Get all users with their roles
            $users = User::with('roles');

            // Filter by name
            if ($request->has('name')) {
                $users->where('name', 'like', '%' . $request->input('name') . '%');
            }

            // Filter by email
            if ($request->has('email')) {
                $users->where('email', 'like', '%' . $request->input('email') . '%');
            }

            // Filter by role
            if ($request->has('role')) {
                $role = Role::where('name', $request->input('role'))->first();

                if ($role) {
                    $users->whereHas('roles', function ($query) use ($role) {
                        $query->where('role_id', $role->id);
                    });
                }
            }

            // Sort results
            if ($request->has('sort')) {
                $sortField = $request->input('sort');
                $users->orderBy($sortField, 'asc');
            }

            // Get paginated users
            $paginatedUsers = $users->paginate($perPage);

            // Only include user data and pagination information
            $data = $paginatedUsers->items();

            // Pagination metadata
            $paginationData = [
                'total' => $paginatedUsers->total(),
                // 'per_page' => $paginatedUsers->perPage(),
                // 'current_page' => $paginatedUsers->currentPage(),
                // 'last_page' => $paginatedUsers->lastPage(),
                // 'from' => $paginatedUsers->firstItem(),
                // 'to' => $paginatedUsers->lastItem(),
                // 'next_page_url' => $paginatedUsers->nextPageUrl(),
                // 'prev_page_url' => $paginatedUsers->previousPageUrl(),
                // 'path' => $paginatedUsers->path(),
            ];

            return response()->json(['data' => $data, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'An error occurred while getting users', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            // Get user by ID
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
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

            // Return a JSON response with the found user
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the user is not found
            return response()->json([
                'error' => 'User not found.',
                'details' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            // Handle any other exception and return an error response
            return response()->json([
                'error' => 'An error ocurred while getting the user.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = auth()->user();

        try {
            $updateData = [];

            // Check if 'name' is present in the request before adding it to the update data
            if ($request->filled('name')) {
                $updateData['name'] = $request->input('name');
            }

            // Check if 'email' is present in the request before adding it to the update data
            if ($request->filled('email')) {
                $updateData['email'] = $request->input('email');
            }

            // Update the user only if there is data to update
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // If you need to update the role, ensure it is a permitted operation
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

            return response()->json(['message' => 'User deleted'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error ocurred while deleting the user.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
