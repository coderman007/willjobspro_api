<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    private function handleException(\Exception $e, $errorMessage, $statusCode): JsonResponse
    {
        return response()->json([
            'error' => $errorMessage,
            'details' => $e->getMessage()
        ], $statusCode);
    }

    /**
     * Muestra todas las facturas.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Obtener el número de elementos por página desde la solicitud
            $perPage = request()->query('per_page', 10);

            // Obtener las facturas paginadas
            $invoices = Invoice::paginate($perPage);

            // Metadatos de paginación
            $paginationData = [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'from' => $invoices->firstItem(),
                'to' => $invoices->lastItem(),
                'next_page_url' => $invoices->nextPageUrl(),
                'prev_page_url' => $invoices->previousPageUrl(),
                'path' => $invoices->path(),
                'data' => $invoices->items(),
                'links' => $invoices->render(),
            ];

            return response()->json(['data' => $invoices, 'pagination' => $paginationData], 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener la lista de facturas.', 500);
        }
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
