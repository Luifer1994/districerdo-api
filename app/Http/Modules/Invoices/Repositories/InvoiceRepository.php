<?php

namespace App\Http\Modules\Invoices\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Invoices\Models\Invoice;
use Illuminate\Support\Facades\DB;

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
                    $query->select('id', 'name', 'last_name', 'document_number', 'address', 'phone')
                        ->selectRaw('CONCAT(name, " ", last_name) as full_name');
                }
            ])
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(price * quantity), 0)')
                    ->from('invoice_lines')
                    ->whereColumn('invoice_id', 'invoices.id');
            }, 'total')

            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(amount), 0)')
                    ->from('partial_payments_of_invoices')
                    ->whereColumn('invoice_id', 'invoices.id');
            }, 'total_paid')
            ->selectSub(function ($query) {
                $query->from('invoices as inv')
                      ->selectRaw('COALESCE((SELECT SUM(il.price * il.quantity) FROM invoice_lines as il WHERE il.invoice_id = inv.id), 0) - COALESCE((SELECT SUM(pp.amount) FROM partial_payments_of_invoices as pp WHERE pp.invoice_id = inv.id), 0)')
                      ->where('inv.id', '=', DB::raw('invoices.id'));
            }, 'total_for_pay')

            ->withCount(['InvoiceLines'])
            ->when($state, function ($query, $state) {
                return $query->where('state', $state);
            })
            ->when(($dateStart && $dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $startDate = \DateTime::createFromFormat('d-m-Y', $dateStart)->format('Y-m-d');
                $endDate = \DateTime::createFromFormat('d-m-Y', $dateEnd)->format('Y-m-d');
                return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->when($search, function ($query, $search) {
                return $query->where('code', $search);
            })
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

    /**
     * Get Invoice by id.
     *
     * @param int $id
     * @return ?object
     */
    public function getInvoiceById(int $id): ?object
    {
        return $this->InvoiceModel
            ->select('id', 'code', 'client_id', 'state', 'created_at', 'observation', 'user_id')

            ->with([
                'Client' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'address', 'phone', 'document_number', 'city_id', 'document_type_id', 'email')
                        ->selectRaw('CONCAT(name, " ", last_name) as full_name')
                        ->with(['City' => function ($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['DocumentType' => function ($query) {
                            $query->select('id', 'name', 'code');
                        }]);
                }, 'PartialPaymentsOfInvoice' => function ($query) {
                    $query->select('id', 'amount', 'invoice_id', 'created_at', 'evidence', 'description')
                        ->with(['User' => function ($query) {
                            $query->select('id', 'name', 'last_name')
                                ->selectRaw('CONCAT(name, " ", last_name) as full_name');
                        }])
                        ->selectRaw('DATE_FORMAT(created_at, "%d-%m-%Y") as date')
                        ->orderBy('id', 'desc');
                },
                'User' => function ($query) {
                    $query->select('id', 'name', 'last_name')
                        ->selectRaw('CONCAT(name, " ", last_name) as full_name');
                }
            ])
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(price * quantity), 0)')
                    ->from('invoice_lines')
                    ->whereColumn('invoice_id', 'invoices.id');
            }, 'total')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(amount), 0)')
                    ->from('partial_payments_of_invoices')
                    ->whereColumn('invoice_id', 'invoices.id');
            }, 'total_paid')
            ->selectSub(function ($query) {
                $query->from('invoices as inv')
                      ->selectRaw('COALESCE((SELECT SUM(il.price * il.quantity) FROM invoice_lines as il WHERE il.invoice_id = inv.id), 0) - COALESCE((SELECT SUM(pp.amount) FROM partial_payments_of_invoices as pp WHERE pp.invoice_id = inv.id), 0)')
                      ->where('inv.id', '=', DB::raw('invoices.id'));
            }, 'total_for_pay')


            ->with(['InvoiceLines' => function ($query) {
                $query->select(
                    'id',
                    'price',
                    'quantity',
                    'invoice_id',
                    'product_id',
                    'batch_id',
                    // Añadir el cálculo del total de la línea directamente en el select
                    DB::raw('COALESCE(price * quantity, 0) as total_line')
                )
                ->with([
                    'Product' => function ($query) {
                        $query->select('id', 'name', 'sku', 'description');
                    },
                    'Batch' => function ($query) {
                        $query->select('id', 'code');
                    }
                ]);
            }])


            ->withCount(['InvoiceLines'])
            ->where('id', $id)
            ->first();
    }

    /**
     * Total amount for month where state is paid.
     *
     */
    public function totalAmountForMonth(): float
    {
        $result = $this->InvoiceModel
            ->selectRaw('COALESCE(SUM(invoice_lines.price * invoice_lines.quantity), 0) as total')
            ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoices.state', '!=', 'CANCELLED')
            ->whereMonth('invoices.created_at', now()->format('m'))
            ->first();

        // Verificar si $result es null y devolver 0 en ese caso
        return $result ? (float) $result->total : 0;
    }
}
