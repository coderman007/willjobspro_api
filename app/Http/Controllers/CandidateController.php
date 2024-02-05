<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Models\Skill;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
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
            $query = Candidate::query();

            // Búsqueda
            if ($request->filled('search')) {
                $searchTerm = $request->query('search');
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery->where('full_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('skills', 'like', '%' . $searchTerm . '%')
                        ->orWhere('certifications', 'like', '%' . $searchTerm . '%');
                });
            }

            // Filtros
            $filters = [
                'full_name', 'gender', 'education', 'status', 'date_of_birth', 'skills', 'certifications',
            ];

            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->query($filter));
                }
            }

            // Ordenación
            if ($request->filled('sort_by') && $request->filled('sort_order')) {
                $sortBy = $request->query('sort_by');
                $sortOrder = $request->query('sort_order');
                $query->orderBy($sortBy, $sortOrder);
            }

            $candidates = $query->paginate($perPage);

            $paginationData = [
                'total' => $candidates->total(),
                'per_page' => $candidates->perPage(),
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
                'from' => $candidates->firstItem(),
                'to' => $candidates->lastItem(),
                'next_page_url' => $candidates->nextPageUrl(),
                'prev_page_url' => $candidates->previousPageUrl(),
                'path' => $candidates->path(),
            ];

            return response()->json(['data' => $candidates, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while getting the candidate list!'], 500);
        }
    }



    /**
     * Store a newly created candidate instance in storage.
     *
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            // Verificar si el usuario tiene el rol 'candidate'
            if (!$user->hasRole('candidate')) {
                return response()->json(['error' => 'User does not have the candidate role'], 403);
            }

            // Crear instancia en la tabla 'candidates'
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'full_name' => $validatedData['full_name'],
                'gender' => $validatedData['gender'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'work_experience' => $validatedData['work_experience'],
                'education' => $validatedData['education'],
                'certifications' => $validatedData['certifications'],
                'languages' => $validatedData['languages'],
                'references' => $validatedData['references'],
                'expected_salary' => $validatedData['expected_salary'],
                'cv_path' => $validatedData['cv_path'],
                'photo_path' => $validatedData['photo_path'],
                'candidate_social_networks' => $validatedData['candidate_social_networks'],
                'status' => $validatedData['status'],
            ]);

            return response()->json(['data' => $candidate, 'message' => 'Candidate Created Successfully!'], 201);
        } catch (QueryException $e) {

            // Manejo de errores de base de datos
            return response()->json([
                'error' => 'An error occurred in database while creating the candidate!',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add skills to the candidate.
     *
     * @param Request $request
     * @param Candidate $candidate
     * @param Skill $skill
     * @return JsonResponse
     */
    public function addSkill(Request $request, Candidate $candidate, Skill $skill)
    {
        try {
            // Validar la existencia de la habilidad
            if (!$candidate->skills->contains($skill->id)) {
                $candidate->addSkill($skill->id);
                return response()->json(['message' => 'Skill added to candidate successfully!'], 200);
            } else {
                return response()->json(['error' => 'Skill already added to candidate!'], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while adding skill to candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove skills from the candidate.
     *
     * @param Request $request
     * @param Candidate $candidate
     * @param Skill $skill
     * @return JsonResponse
     */
    public function removeSkill(Request $request, Candidate $candidate, Skill $skill)
    {
        try {
            // Validar la existencia de la habilidad
            if ($candidate->skills->contains($skill->id)) {
                $candidate->removeSkill($skill->id);
                return response()->json(['message' => 'Skill removed from candidate successfully!'], 200);
            } else {
                return response()->json(['error' => 'Skill not found in candidate\'s profile!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while removing skill from candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function show(Candidate $candidate): JsonResponse
    {
        try {
            return response()->json([
                'data' => $candidate,
                'role' => 'candidate',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCandidateRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $candidate->update($validatedData);
            return response()->json(['data' => $candidate, 'message' => 'Candidate updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function destroy(Candidate $candidate): JsonResponse
    {
        try {
            $candidate->delete();
            return response()->json(['message' => 'Candidate deleted!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the candidate!',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
