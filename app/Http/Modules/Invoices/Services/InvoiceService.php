<?php

namespace App\Http\Modules\Invoices\Services;

use App\Http\Modules\Inventories\Repositories\InventoryRepository;
use App\Http\Modules\Invoices\Models\Invoice;
use App\Http\Modules\Invoices\Models\InvoiceLine;
use App\Http\Modules\Invoices\Models\InvoiceLineSupply;
use App\Http\Modules\Invoices\Models\PartialPaymentsOfInvoice;
use App\Http\Modules\Invoices\Repositories\InvoiceLineRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceLineSupplyRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceRepository;
use App\Http\Modules\Invoices\Repositories\PartialPaymentsOfInvoiceRepository;
use App\Http\Modules\Invoices\Requests\CreateOrUpdateInvoiceRequest;
use App\Http\Modules\Invoices\Requests\CreatePaymentPartialInvoiceRequest;
use App\Http\Modules\Outputs\Models\Output;
use App\Http\Modules\Outputs\Models\OutputInvoiceLine;
use App\Http\Modules\Outputs\Repositories\OutputInvoiceLineRepository;
use App\Http\Modules\Outputs\Repositories\OutputRepository;
use App\Traits\FileStorage;
use App\Traits\GenerateCodeRandom;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceService
{
    use GenerateCodeRandom, FileStorage;
    protected $InvoiceRepository, $InvoiceLineRepository, $InventoryRepository, $OutputRepository, $OutputInvoiceLineRepository, $PartialPaymentsOfInvoiceRepository;

    public function __construct(
        InvoiceRepository $InvoiceRepository,
        InvoiceLineRepository $InvoiceLineRepository,
        InventoryRepository $InventoryRepository,
        OutputRepository $OutputRepository,
        OutputInvoiceLineRepository $OutputInvoiceLineRepository,
        PartialPaymentsOfInvoiceRepository $PartialPaymentsOfInvoiceRepository
    ) {
        $this->InvoiceRepository           = $InvoiceRepository;
        $this->InvoiceLineRepository       = $InvoiceLineRepository;
        $this->InventoryRepository          = $InventoryRepository;
        $this->OutputRepository            = $OutputRepository;
        $this->OutputInvoiceLineRepository = $OutputInvoiceLineRepository;
        $this->PartialPaymentsOfInvoiceRepository = $PartialPaymentsOfInvoiceRepository;
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

            if ($invoice->state == 'Pagada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'Cancelada') {
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
                'data' => ['base64' => base64_encode($invoice), 'code' => $data['code']]

            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Download evidence.
     *
     * @param int $id
     * @return array
     */
    public function downloadEvidence(int $id): array
    {
        try {
            $data = $this->PartialPaymentsOfInvoiceRepository->find($id);

            if (!$data)
                return [
                    'status' => false,
                    'message' => 'Pago parcial de factura no encontrado',
                    'data' => null
                ];
            if (!$data->evidence)
                return [
                    'status' => false,
                    'message' => 'No se encontró evidencia de pago',
                    'data' => null
                ];
            //sear file in storage
            $file = $this->getFile($data->evidence);

            return [
                'status' => true,
                'message' => 'Factura descargada con éxito',
                'data' => ['base64' => base64_encode($file), 'code' => $data->id]

            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Pay invoice.
     *
     * @return object
     */
    public function payInvoice(int $id): object
    {
        DB::beginTransaction();
        try {
            $invoice = $this->InvoiceRepository->getInvoiceById($id);
            if (!$invoice) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'Factura no encontrada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'Pagada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'Cancelada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura se encuentra cancelada',
                    'data' => null
                ];
            }
            $this->PartialPaymentsOfInvoiceRepository->save(new PartialPaymentsOfInvoice([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_for_pay,
                'user_id' => auth()->user()->id,
                'description' => 'Pago total de la factura'
            ]));
            $invoice->state = 'PAID';
            $invoice = $this->InvoiceRepository->save($invoice);



            DB::commit();

            return (object) [
                'status' => true,
                'message' => 'Factura pagada con éxito',
                'data' => $invoice
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return (object) [
                'status' => false,
                'message' => 'Error al pagar la factura',
                'data' => null
            ];
        }
    }

    /**
     * Partial payment of invoice.
     *
     * @param CreatePaymentPartialInvoiceRequest $request
     * @return object
     */
    public function partialPayment(CreatePaymentPartialInvoiceRequest $request): object
    {
        DB::beginTransaction();
        try {
            $invoice = $this->InvoiceRepository->getInvoiceById($request->invoice_id);
            if (!$invoice) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'Factura no encontrada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'Pagada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura ya se encuentra pagada',
                    'data' => null
                ];
            }

            if ($invoice->state == 'Cancelada') {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'La factura se encuentra cancelada',
                    'data' => null
                ];
            }

            if ($request->amount > $invoice->total_for_pay) {
                DB::rollBack();
                return (object) [
                    'status' => false,
                    'message' => 'El monto a pagar es mayor al monto pendiente de la factura ($' . number_format($invoice->total_for_pay, 0, '.', '.') . ')',
                    'data' => null
                ];
            }


            $partial = $this->PartialPaymentsOfInvoiceRepository->save(new PartialPaymentsOfInvoice([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'evidence' => ($request->has('evidence') && $request->file('evidence') != null) ? $this->uploadFile($request->file('evidence')) : null,
                'user_id' => auth()->user()->id,
                'description' => $request->description
            ]));

            if ($invoice->total_for_pay == $request->amount) {
                $invoice->state = 'PAID';
                $invoice = $this->InvoiceRepository->save($invoice);
            }

            DB::commit();

            return (object) [
                'status' => true,
                'message' => 'Pago parcial de factura realizado con éxito',
                'data' => $partial
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return (object) [
                'status' => false,
                'message' => 'Error al realizar el pago parcial de la factura ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
}
