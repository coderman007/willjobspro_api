<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
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
            $languages = Language::paginate($perPage)->items();

            return response()->json([
                'message' => 'Languages successfully retrieved',
                'data' => $languages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while getting the languages list!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLanguageRequest $request
     * @return JsonResponse
     */
    public function store(StoreLanguageRequest $request): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $language = Language::create($validatedData);

            return response()->json([
                'message' => 'Language successfully created',
                'data' => $language,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the language!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Language $language
     * @return JsonResponse
     */
    public function show(Language $language): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            return response()->json([
                'message' => 'Language detail successfully retrieved',
                'data' => $language,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the language!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLanguageRequest $request
     * @param Language $language
     * @return JsonResponse
     */
    public function update(UpdateLanguageRequest $request, Language $language): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $validatedData = $request->validated();
            $language->update($validatedData);

            return response()->json([
                'message' => 'Language successfully updated',
                'data' => $language,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the language!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Language $language
     * @return JsonResponse
     */
    public function destroy(Language $language): JsonResponse
    {
        try {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            $language->delete();

            return response()->json([
                'message' => 'Language deleted',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the language!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
