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
        // Manejar excepciones y devolver una respuesta JSON clara en caso de errores
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }

    public function index(): JsonResponse
    {
        try {
            // Obtener los números de elementos por página de los request
            $perPage = request()->query('per_page', 10);

            // Obtener los planes de subscription paginados
            $subscriptionPlans = SubscriptionPlan::paginate($perPage);

            // Metadatos de paginación 
            $paginationData = [
                'total' => $subscriptionPlans->total(),
                'per_page' => $subscriptionPlans->perPage(),
                'current_page' => $subscriptionPlans->currentPage(),
                'last_page' => $subscriptionPlans->lastPage(),
                'from' => $subscriptionPlans->firstItem(),
                'to' => $subscriptionPlans->lastItem(),
                'next_page_url' => $subscriptionPlans->nextPageUrl(),
                'prev_page_url' => $subscriptionPlans->previousPageUrl(),
                'path' => $subscriptionPlans->path(),
            ];

            // Devolver la respuesta JSON con datos y metadatos de paginación
            return response()->json(['data' => $subscriptionPlans, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver una respuesta JSON clara en caso de errores al obtener la lista de planes de suscripción
            return $this->handleException($e, 'Error retrieving the subscription plans list.', 500);
        }
    }

    public function store(StoreSubscriptionPlanRequest $request): JsonResponse
    {
        try {
            // Validar los datos del request
            $validatedData = $request->validated();

            // Verificar si el usuario tiene el rol 'admin'
            if (!$request->user()->hasRole('admin')) {
                return response()->json(['error' => 'User does not have the admin role'], 403);
            }

            // Crear un nuevo plan de suscripción con los datos validados
            $subscriptionPlan = SubscriptionPlan::create($validatedData);

            // Devolver la respuesta JSON con el nuevo plan de suscripción y un mensaje de éxito
            return response()->json(['data' => $subscriptionPlan, 'message' => 'Subscription plan created successfully.'], 201);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver una respuesta JSON clara en caso de errores al crear el plan de suscripción
            return $this->handleException($e, 'Error creating the subscription plan.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            // Buscar el plan de suscripción por su identificador
            $subscriptionPlan = SubscriptionPlan::findOrFail($id);

            // Devolver una respuesta JSON con los datos del plan de suscripción encontrado
            return response()->json(['data' => $subscriptionPlan], 200);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver una respuesta JSON clara en caso de que el plan de suscripción no sea encontrado
            return $this->handleException($e, 'Subscription plan not found.', 404);
        }
    }


    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            // Verificar si el usuario tiene el rol 'admin'
            if (!$request->user()->hasRole('admin')) {
                // Devolver una respuesta JSON de error si el usuario no tiene el rol 'admin'
                return response()->json(['error' => 'User does not have the admin role'], 403);
            }

            // Validar los datos del request
            $validatedData = $request->validated();

            // Actualizar el plan de suscripción con los datos validados
            $subscriptionPlan->update($validatedData);

            // Devolver una respuesta JSON con el plan de suscripción actualizado y un mensaje de éxito
            return response()->json(['data' => $subscriptionPlan, 'message' => 'Subscription plan updated successfully.'], 200);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver una respuesta JSON clara en caso de errores al actualizar el plan de suscripción
            return $this->handleException($e, 'Error updating the subscription plan.', 500);
        }
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            // Verificar si el usuario tiene el rol 'admin'
            if (!auth()->user()->hasRole('admin')) {
                // Devolver una respuesta JSON de error si el usuario no tiene el rol 'admin'
                return response()->json(['error' => 'User does not have the admin role'], 403);
            }

            // Eliminar el plan de suscripción
            $subscriptionPlan->delete();

            // Devolver una respuesta JSON con un mensaje de éxito
            return response()->json(['message' => 'Subscription plan deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver una respuesta JSON clara en caso de errores al eliminar el plan de suscripción
            return $this->handleException($e, 'Error deleting the subscription plan.', 500);
        }
    }
}
