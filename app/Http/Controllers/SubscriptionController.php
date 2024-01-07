<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Muestra todas las subscripciones del usuario autenticado.
     *
     * @return JsonResponse
     */
    public function getSubscriptions(): JsonResponse
    {
        $subscriptions = auth()->user()->subscriptions;

        return response()->json(['subscriptions' => $subscriptions]);
    }

    /**
     * Muestra una subscripción específica del usuario autenticado.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getSubscription(int $id): JsonResponse
    {
        $subscription = auth()->user()->subscriptions()->findOrFail($id);

        return response()->json(['subscription' => $subscription]);
    }

    /**
     * Crea una nueva subscripción para el usuario autenticado.
     *
     * @param StoreSubscriptionRequest $request
     * @return JsonResponse
     */
    public function subscribe(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = auth()->user()->subscriptions()->create($request->validated());

        return response()->json(['subscription' => $subscription], 201);
    }

    /**
     * Actualiza una subscripción específica del usuario autenticado.
     *
     * @param UpdateSubscriptionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateSubscription(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = auth()->user()->subscriptions()->findOrFail($id);

        $subscription->update($request->validated());

        return response()->json(['subscription' => $subscription]);
    }

    /**
     * Cancela una subscripción específica del usuario autenticado.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancelSubscription(int $id): JsonResponse
    {
        $subscription = auth()->user()->subscriptions()->findOrFail($id);

        $subscription->delete();

        return response()->json(['message' => 'Subscripción cancelada correctamente']);
    }
}