<?php

namespace App\Http\Modules\Invoices\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Invoices\Models\Invoice;

class InvoiceRepository extends RepositoryBase
{
    protected  $InvoiceModel;

    public function __construct(Invoice $InvoiceModel)
    {
        parent::__construct($InvoiceModel);
        $this->InvoiceModel = $InvoiceModel;
    }

    /**
     * Get all Invoices.
     *
     * @param int $limit
     * @param string $search
     * @return object
     * @author Luifer Almendrales
     */
    public function getAllInvoices(int $limit, string $search): object
    {
        return $this->InvoiceModel
            ->select('id', 'code', 'client_id', 'state','created_at')
            ->where('code', 'like', '%' . $search . '%')
            ->with([
                'Client' => function ($query) {
                    $query->select('id', 'name', 'last_name')
                        ->selectRaw('CONCAT(name, " ", last_name) as full_name');
                },
                'InvoiceLines' => function ($query) {
                    $query->select('id', 'invoice_id', 'service_id', 'quantity', 'price', 'percentage_tax')
                        ->with(['service:id,name', 'InvoiceLineSupplies' => function ($query) {
                            $query->select('id', 'description', 'price', 'percentage_tax', 'quantity', 'invoice_line_id');
                        }])
                        ->withCount(['InvoiceLineSupplies']);
                }
            ])
            ->withCount(['InvoiceLines'])
            ->orderBy('id', 'desc')
            ->paginate($limit);
    }

    /**
     * Get Invoice by code.
     *
     * @param string $code
     * @return ?object
     */
    public function getInvoiceByCode(string $code): ?object
    {
        return $this->InvoiceModel->where('code', $code)->first();
    }
}
