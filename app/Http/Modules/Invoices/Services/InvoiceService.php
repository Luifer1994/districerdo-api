<?php

namespace App\Http\Modules\Invoices\Services;

use App\Http\Modules\Invoices\Models\Invoice;
use App\Http\Modules\Invoices\Models\InvoiceLine;
use App\Http\Modules\Invoices\Models\InvoiceLineSupply;
use App\Http\Modules\Invoices\Repositories\InvoiceLineRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceLineSupplyRepository;
use App\Http\Modules\Invoices\Repositories\InvoiceRepository;
use App\Http\Modules\Invoices\Requests\CreateOrUpdateInvoiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    protected $InvoiceRepository;
    protected $InvoiceLineRepository;

    public function __construct(InvoiceRepository $InvoiceRepository, InvoiceLineRepository $InvoiceLineRepository)
    {
        $this->InvoiceRepository           = $InvoiceRepository;
        $this->InvoiceLineRepository       = $InvoiceLineRepository;
    }

    /**
     * Get all Invoices with pagination.
     *
     * @param int $limit
     * @param string $search
     * @return object
     */
    function getAllInvoices(int $limit, string $search): object
    {
        $results = $this->InvoiceRepository->getAllInvoices($limit, $search);

        $results->getCollection()->map(function ($invoice) {
            $invoice->total = 0;
            $invoice->total_supplies = 0;
            $invoice->total_services = 0;

            foreach ($invoice->InvoiceLines as $invoiceLine) {
                $invoiceLine->subtotal  =  $invoiceLine->quantity * $invoiceLine->price;
                $invoiceLine->total_tax =  $invoiceLine->subtotal * ($invoiceLine->percentage_tax / 100);
                $invoiceLine->total     =  $invoiceLine->subtotal + $invoiceLine->total_tax;
                $invoice->total         += $invoiceLine->total;
                $invoice->total_services += $invoiceLine->total - $invoiceLine->total_tax;

                foreach ($invoiceLine->InvoiceLineSupplies as $supply) {
                    $supply->subtotal        =  $supply->quantity * $supply->price;
                    $supply->total_tax       =  $supply->subtotal * ($supply->percentage_tax / 100);
                    $supply->total           =  $supply->subtotal + $supply->total_tax;
                    $invoice->total          += $supply->total;
                    $invoiceLine->subtotal   += $supply->subtotal;
                    $invoiceLine->total_tax  += $supply->total_tax;
                    $invoiceLine->total      += $supply->total;
                    $invoice->total_supplies += $supply->total - $supply->total_tax;
                }
            }

            $invoice->subtotal = $invoice->InvoiceLines->sum('subtotal');
            $invoice->total_tax = $invoice->InvoiceLines->sum('total_tax');
            return $invoice;
        });

        return $results;
    }

    /**
     * Create new invoices.
     *
     * @param CreateOrUpdateInvoiceRequest $request
     * @return object
     */
    function CreateInvoice(CreateOrUpdateInvoiceRequest $request): object
    {
        try {
            DB::beginTransaction();

            $requestData = [
                'code' => $this->createUniqueCode(),
                'user_id' => auth()->user()->id,
                'client_id' => $request->client_id,
                'state' => $request->state,
            ];

            $invoice = $this->InvoiceRepository->save(new Invoice($requestData));

            foreach ($request->invoice_lines as $invoiceLineData) {
                $invoiceLine = new InvoiceLine($invoiceLineData);
                $invoiceLine->invoice_id = $invoice->id;
                $invoiceLine = $this->InvoiceLineRepository->save($invoiceLine);
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
        $code = Str::upper(Str::random(8));
        $codeExist = $this->InvoiceRepository->getInvoiceByCode($code);

        if ($codeExist)
            return $this->createUniqueCode();

        return $code;
    }
}
