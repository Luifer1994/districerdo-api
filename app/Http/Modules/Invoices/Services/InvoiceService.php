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
                'user_id' => auth()->user()->id,
            ]);

            $invoice = $this->InvoiceRepository->save(new Invoice($request->all()));

            foreach ($request->invoice_lines as $invoiceLineData) {
                $Inventory = $this->InventoryRepository->findInventoryByProductIdAndBatchId($invoiceLineData['product_id'], $invoiceLineData['batch_id']);
                if (!$Inventory) {
                    DB::rollBack();
                    return (object) [
                        'status' => false,
                        'message' => 'No se encontró el producto ' . $invoiceLineData['product_id'] . ' con el lote indicado',
                        'data' => null
                    ];
                }

                if ($Inventory->quantity < $invoiceLineData['quantity']) {
                    DB::rollBack();
                    return (object) [
                        'status' => false,
                        'message' => 'La cantidad a facturar del producto ' . $invoiceLineData['product_id'] . ' es mayor a la cantidad en existencia',
                        'data' => null
                    ];
                }

                $invoiceLine = new InvoiceLine($invoiceLineData);
                $invoiceLine->invoice_id = $invoice->id;
                $invoiceLine = $this->InvoiceLineRepository->save($invoiceLine);

                $newOutput = $this->OutputRepository->save(new Output([
                    'price' => $invoiceLineData['price'],
                    'quantity' => $invoiceLineData['quantity'],
                    'batch_id' => $invoiceLineData['batch_id'],
                    'product_id' => $invoiceLineData['product_id'],
                    'user_id' => auth()->user()->id,
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
}
