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
    public function getAllInvoices(int $limit, string $search, string $state, string $dateStart, string $dateEnd): object
    {
        return $this->InvoiceModel
            ->select('id', 'code', 'client_id', 'state', 'created_at')

            ->with([
                'Client' => function ($query) {
                    $query->select('id', 'name', 'last_name')
                        ->selectRaw('CONCAT(name, " ", last_name) as full_name');
                }
            ])
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(price * quantity), 0)')
                    ->from('invoice_lines')
                    ->whereColumn('invoice_id', 'invoices.id');
            }, 'total')
            ->withCount(['InvoiceLines'])
            ->when($state, function ($query, $state) {
                return $query->where('status', $state);
            })
            ->when(($dateStart && $dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $startDate = \DateTime::createFromFormat('d-m-Y', $dateStart)->format('Y-m-d');
                $endDate = \DateTime::createFromFormat('d-m-Y', $dateEnd)->format('Y-m-d');
                return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->where('code', 'like', '%' . $search . '%')
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
