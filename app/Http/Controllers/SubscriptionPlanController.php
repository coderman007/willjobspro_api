<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Http\Requests\StoreSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller
{
    private function handleException(\Exception $e, $errorMessage, $statusCode): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }

    public function index(): JsonResponse
    {
        try {
            $subscriptionPlans = SubscriptionPlan::all();
            return response()->json(['data' => $subscriptionPlans], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener la lista de planes de suscripción.', 500);
        }
    }

    public function store(StoreSubscriptionPlanRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $subscriptionPlan = SubscriptionPlan::create($validatedData);
            return response()->json(['data' => $subscriptionPlan, 'message' => 'Plan de suscripción creado con éxito.'], 201);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al crear el plan de suscripción.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $subscriptionPlan = SubscriptionPlan::findOrFail($id);
            return response()->json(['data' => $subscriptionPlan], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Plan de suscripción no encontrado.', 404);
        }
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $subscriptionPlan->update($validatedData);

            return response()->json(['data' => $subscriptionPlan, 'message' => 'Plan de suscripción actualizado con éxito.'], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al actualizar el plan de suscripción.', 500);
        }
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            $subscriptionPlan->delete();
            return response()->json(['message' => 'Plan de suscripción eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al eliminar el plan de suscripción.', 500);
        }
    }
}
