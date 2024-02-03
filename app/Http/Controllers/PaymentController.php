<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private function handleException(\Exception $e, $errorMessage, $statusCode): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }

    /**
     * Muestra todos los pagos del usuario autenticado.
     *
     * @return JsonResponse
     */
    public function getPayments(): JsonResponse
    {
        try {
            // Obtener el número de elementos por página desde la solicitud
            $perPage = request()->query('per_page', 10);

            // Obtener los pagos del usuario autenticado paginados
            $payments = auth()->user()->payments()->paginate($perPage);

            // Metadatos de paginación
            $paginationData = [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'next_page_url' => $payments->nextPageUrl(),
                'prev_page_url' => $payments->previousPageUrl(),
                'path' => $payments->path(),
                'data' => $payments->items(),
                'links' => $payments->render(),
            ];

            return response()->json(['data' => $payments, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener la lista de pagos del usuario autenticado.', 500);
        }
    }

    /**
     * Muestra un pago específico del usuario autenticado.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPayment(int $id): JsonResponse
    {
        $payment = auth()->user()->payments()->findOrFail($id);

        return response()->json(['payment' => $payment]);
    }

    /**
     * Registra un nuevo pago para el usuario autenticado.
     *
     * @param StorePaymentRequest $request
     * @return JsonResponse
     */
    public function makePayment(StorePaymentRequest $request): JsonResponse
    {
        $payment = auth()->user()->payments()->create($request->validated());

        return response()->json(['payment' => $payment], 201);
    }

    /**
     * Actualiza un pago específico del usuario autenticado.
     *
     * @param UpdatePaymentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePayment(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        $payment = auth()->user()->payments()->findOrFail($id);

        $payment->update($request->validated());

        return response()->json(['payment' => $payment]);
    }

    /**
     * Elimina un pago específico del usuario autenticado.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deletePayment(int $id): JsonResponse
    {
        $payment = auth()->user()->payments()->findOrFail($id);

        $payment->delete();

        return response()->json(['message' => 'Pago eliminado correctamente']);
    }
}
