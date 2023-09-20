<?php

namespace App\Http\Modules\Invoices\Services;

use App\Http\Modules\Inventories\Repositories\InventoryRepository;
use App\Http\Modules\Invoices\Models\Invoice;
use App\Http\Modules\Invoices\Models\InvoiceLine;
use App\Http\Modules\Invoices\Models\InvoiceLineSupply;
use App\Http\Modules\Invoices\Repositories\InvoiceLineRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceLineSupplyRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceRepository;
use App\Http\Modules\Invoices\Requests\CreateOrUpdateInvoiceRequest;
use App\Http\Modules\Outputs\Models\Output;
use App\Http\Modules\Outputs\Models\OutputInvoiceLine;
use App\Http\Modules\Outputs\Repositories\OutputInvoiceLineRepository;
use App\Http\Modules\Outputs\Repositories\OutputRepository;
use App\Traits\GenerateCodeRandom;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    use GenerateCodeRandom;
    protected $InvoiceRepository, $InvoiceLineRepository, $InventoryRepository, $OutputRepository, $OutputInvoiceLineRepository;

    public function __construct(InvoiceRepository $InvoiceRepository, InvoiceLineRepository $InvoiceLineRepository, InventoryRepository $InventoryRepository, OutputRepository $OutputRepository, OutputInvoiceLineRepository $OutputInvoiceLineRepository)
    {
        $this->InvoiceRepository           = $InvoiceRepository;
        $this->InvoiceLineRepository       = $InvoiceLineRepository;
        $this->InventoryRepository          = $InventoryRepository;
        $this->OutputRepository            = $OutputRepository;
        $this->OutputInvoiceLineRepository = $OutputInvoiceLineRepository;
    }

    /**
     * Create new invoices.
     *
     * @param CreateOrUpdateInvoiceRequest $request
     * @return object
     */
    function CreateInvoice(CreateOrUpdateInvoiceRequest $request): object
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'code' => $this->createUniqueCode(),
                'user_id' => 1,
            ]);

            $invoice = $this->InvoiceRepository->save(new Invoice($request->all()));

            foreach ($request->invoice_lines as $invoiceLineData) {
                $Inventory = $this->InventoryRepository->findInventoryByProductIdAndBatchCode($invoiceLineData['product_id'], $invoiceLineData['batch']);
                if (!$Inventory) {
                    DB::rollBack();
                    return (object) [
                        'status' => false,
                        'message' => 'No se encontró el producto ' . $invoiceLineData['product_id'] . ' con el lote ' . $invoiceLineData['batch'],
                        'data' => null
                    ];
                }

                if ($Inventory->quantity < $invoiceLineData['quantity']) {
                    DB::rollBack();
                    return (object) [
                        'status' => false,
                        'message' => 'La cantidad a facturar del producto ' . $invoiceLineData['product_id'] . ' es mayor a la cantidad en existencia del lote ' . $invoiceLineData['batch'] . ' (Cantidad en existencia: ' . $Inventory->quantity . ')',
                        'data' => null
                    ];
                }

                $invoiceLine = new InvoiceLine($invoiceLineData);
                $invoiceLine->invoice_id = $invoice->id;
                $invoiceLine->batch_id = $Inventory['batch']['id'];
                $invoiceLine = $this->InvoiceLineRepository->save($invoiceLine);

                $newOutput = $this->OutputRepository->save(new Output([
                    'price' => $invoiceLineData['price'],
                    'quantity' => $invoiceLineData['quantity'],
                    'batch_id' => $Inventory['batch']['id'],
                    'product_id' => $invoiceLineData['product_id'],
                    'user_id' => 1,
                ]));

                $this->OutputInvoiceLineRepository->save(new OutputInvoiceLine([
                    'output_id' => $newOutput->id,
                    'invoice_line_id' => $invoiceLine->id
                ]));

                $Inventory->quantity -= $invoiceLineData['quantity'];
                $this->InventoryRepository->save($Inventory);
            }

            DB::commit();

            return (object) [
                'status' => true,
                'message' => 'Factura creada con éxito',
                'data' => $invoice
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return (object) [
                'status' => false,
                'message' => $th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile(),
                'data' => null
            ];
        }
    }

    /**
     * Cancel invoice.
     *
     * @param int $id
     * @return object
     */
    function cancelInvoice(int $id): object
    {
        DB::beginTransaction();
        try {
            $invoice = $this->InvoiceRepository->find($id);
            if (!$invoice) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'Factura no encontrada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'PAID') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'CANCELLED') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura se encuentra cancelada',
                    'data' => null
                ];
            }

            $invoice->state = 'CANCELLED';
            $invoice = $this->InvoiceRepository->save($invoice);

            foreach ($invoice->InvoiceLines as $invoiceLine) {
                $Inventory = $this->InventoryRepository->findInventoryByProductIdAndBatchCode($invoiceLine->product_id, $invoiceLine->batch->code);
                if (!$Inventory) {
                    DB::rollBack();
                    return (object) [
                        'status' => false,
                        'message' => 'No se encontró el producto ' . $invoiceLine->product_id . ' con el lote ' . $invoiceLine->batch->code,
                        'data' => null
                    ];
                }

                $Inventory->quantity += $invoiceLine->quantity;
                $this->InventoryRepository->save($Inventory);
            }

            DB::commit();

            return (object) [
                'status' => true,
                'message' => 'Factura cancelada con éxito',
                'data' => $invoice
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return (object) [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create unique code.
     *
     * @return string
     */
    public function createUniqueCode(): string
    {
        $code = $this->generateCode(8);
        $codeExist = $this->InvoiceRepository->getInvoiceByCode($code);

        if ($codeExist)
            return $this->createUniqueCode();

        return $code;
    }

    /**
     * Download invoice.
     *
     * @param int $id
     * @return array
     */
    public function downloadInvoice(int $id): array
    {
        try {
            $data = $this->InvoiceRepository->getInvoiceById($id);

            if (!$data)
                return [
                    'status' => false,
                    'message' => 'Factura no encontrada',
                    'data' => null
                ];
            $data = $data->toArray();
            $pdf = Pdf::loadView('pdf.invoice', compact('data'));
            $invoice = $pdf->download('invoice.pdf');

            return [
                'status' => true,
                'message' => 'Factura descargada con éxito',
                'data' => base64_encode($invoice)
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => null
            ];
        }
    }
}
