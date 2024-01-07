<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Muestra todos los pagos del usuario autenticado.
     *
     * @return JsonResponse
     */
    public function getPayments(): JsonResponse
    {
        $payments = auth()->user()->payments;

        return response()->json(['payments' => $payments]);
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
