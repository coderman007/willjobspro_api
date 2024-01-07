<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    /**
     * Muestra todas las facturas.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $invoices = Invoice::all();

        return response()->json(['invoices' => $invoices]);
    }

    /**
     * Muestra una factura específica.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);

        return response()->json(['invoice' => $invoice]);
    }

    /**
     * Crea una nueva factura.
     *
     * @param StoreInvoiceRequest $request
     * @return JsonResponse
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = Invoice::create($request->validated());

        return response()->json(['invoice' => $invoice], 201);
    }

    /**
     * Actualiza una factura específica.
     *
     * @param UpdateInvoiceRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->validated());

        return response()->json(['invoice' => $invoice]);
    }

    /**
     * Elimina una factura específica.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json(['message' => 'Factura eliminada correctamente']);
    }
}
