<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Http\Requests\StoreSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $subscriptionPlans = SubscriptionPlan::all();
            return response()->json(['data' => $subscriptionPlans], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de planes de suscripción.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionPlanRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $subscriptionPlan = SubscriptionPlan::create($validatedData);
            return response()->json(['data' => $subscriptionPlan, 'message' => 'Plan de suscripción creado con éxito.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el plan de suscripción.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            return response()->json(['data' => $subscriptionPlan], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el plan de suscripción.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $subscriptionPlan->update($validatedData);
            return response()->json(['data' => $subscriptionPlan, 'message' => 'Plan de suscripción actualizado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el plan de suscripción.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            $subscriptionPlan->delete();
            return response()->json(['message' => 'Plan de suscripción eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el plan de suscripción.'], 500);
        }
    }
}
