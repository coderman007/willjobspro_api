<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the user's subscriptions.
     *
     * @return JsonResponse
     */
    public function getSubscriptions(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Obtener el número de elementos por página desde la solicitud
            $perPage = request()->query('per_page', 10);

            // Verificar el rol del usuario
            if ($user->hasRole('admin')) {
                // Permitir a los usuarios administrativos obtener todas las suscripciones en el sistema.
                $subscriptions = Subscription::paginate($perPage);
            } elseif ($user->hasRole('candidate')) {
                // Limitar a los usuarios con el rol 'candidate' a obtener solo sus propias suscripciones.
                $subscriptions = $user->subscriptions()->paginate($perPage);
            } else {
                // Devolver una respuesta JSON de error si el usuario no tiene el rol 'admin' o el rol 'candidate'
                return response()->json(['error' => 'Access denied.'], 403);
            }

            // Metadatos de paginación
            $paginationData = [
                'total' => $subscriptions->total(),
                // 'per_page' => $subscriptions->perPage(),
                // 'current_page' => $subscriptions->currentPage(),
                // 'last_page' => $subscriptions->lastPage(),
                // 'from' => $subscriptions->firstItem(),
                // 'to' => $subscriptions->lastItem(),
                // 'next_page_url' => $subscriptions->nextPageUrl(),
                // 'prev_page_url' => $subscriptions->previousPageUrl(),
                // 'path' => $subscriptions->path(),
            ];

            return response()->json(['subscriptions' => $subscriptions, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while getting user subscriptions.'], 500);
        }
    }

    /**
     * Muestra una suscripción específica del usuario autenticado o de cualquier candidato (para administradores).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getSubscription(int $id): JsonResponse
    {
        try {
            $user = auth()->user();
            $subscription = Subscription::findOrFail($id);

            // Verificar el acceso al recurso
            if ($user->hasRole('admin') || $user->id === $subscription->candidate->user_id) {
                // Permitir al administrador o al candidato acceder a la suscripción
                return response()->json(['subscription' => $subscription], 200);
            } else {
                // Devolver una respuesta JSON de error si el usuario no tiene acceso
                return response()->json(['error' => 'Access denied.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Subscription not found.',
                'details' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualiza una suscripción específica del usuario autenticado o de cualquier candidato (para administradores).
     *
     * @param UpdateSubscriptionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateSubscription(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        try {
            // Obtener la suscripción por su ID
            $subscription = Subscription::findOrFail($id);

            // Verificar el acceso al recurso
            if (!auth()->user()->hasRole('admin') && $subscription->candidate_id !== auth()->user()->id) {
                return response()->json(['error' => 'Unauthorized. You do not have permission to update this subscription.'], 403);
            }

            // Validar los datos proporcionados en la solicitud
            $validatedData = $request->validated();

            // Actualizar la suscripción con los datos validados
            $subscription->update($validatedData);

            return response()->json(['subscription' => $subscription, 'message' => 'Subscription updated successfully.'], 200);
        } catch (\Exception $e) {
            // Manejar errores y devolver una respuesta JSON detallada
            return response()->json(['error' => 'An error occurred while updating the subscription.', 'details' => $e->getMessage()], 500);
        }
    }

    // /**
    //  * Crea una nueva subscripción para el usuario autenticado.
    //  *
    //  * @param StoreSubscriptionRequest $request
    //  * @return JsonResponse
    //  */
    // public function subscribe(StoreSubscriptionRequest $request): JsonResponse
    // {

    //     $subscription = auth()->user()->subscriptions()->create($request->validated());

    //     return response()->json(['subscription' => $subscription], 201);
    // }

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
