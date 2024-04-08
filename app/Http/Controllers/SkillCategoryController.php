<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillCategoryRequest;
use App\Http\Requests\UpdateSkillCategoryRequest;
use App\Models\Language;
use App\Models\SkillCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillCategoryController extends Controller
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
            $skillCategories = SkillCategory::paginate($perPage)->items();

            return response()->json([
                'message' => 'Skill categories successfully retrieved',
                'data' => $skillCategories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the skill categories list!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSkillCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreSkillCategoryRequest $request): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $skillCategory = SkillCategory::create($validatedData);

            return response()->json([
                'message' => 'Skill category successfully created',
                'data' => $skillCategory
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the skill category!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param SkillCategory $skillCategory
     * @return JsonResponse
     */
    public function show(SkillCategory $skillCategory): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Skill category detail successfully retrieved',
                'data' => $skillCategory,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the skill category!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSkillCategoryRequest $request
     * @param SkillCategory $skillCategory
     * @return JsonResponse
     */
    public function update(UpdateSkillCategoryRequest $request, SkillCategory $skillCategory): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $skillCategory->update($validatedData);

            return response()->json([
                'message' => 'Skill category successfully updated',
                'data' => $skillCategory
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the skill category!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SkillCategory $skillCategory
     * @return JsonResponse
     */
    public function destroy(SkillCategory $skillCategory): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $skillCategory->delete();

            return response()->json([
                'message' => 'Skill category deleted',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the skill category!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
