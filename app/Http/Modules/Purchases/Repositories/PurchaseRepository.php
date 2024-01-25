<?php

namespace App\Http\Modules\Purchases\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Purchases\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseRepository extends RepositoryBase
{

    public function __construct(protected Purchase $PurchaseModel)
    {
        parent::__construct($PurchaseModel);
    }

    /**
     * Get all Purchases.
     *
     * @param  int $limit
     * @param  string $search
     * @return object
     * @author Luifer Almendrales
     */
    public function list(int $limit, string $search, string $state, string $dateStart, string $dateEnd): object
    {
        return $this->PurchaseModel->select('id', 'code', 'provider_id', 'status', 'created_at')
            ->with([
                'Provider' => fn ($q) => $q->select('id', 'name', 'last_name', 'document_number')
                    ->selectRaw('CONCAT(name, " ", last_name) as full_name'),
                'PurchaseLines' => fn ($q) => $q->select('id', 'price', 'quantity', 'purchase_id', 'product_id')
                    ->with(['Entrance' => function ($q) {
                        $q->select('id', 'purchase_line_id', 'quantity', 'batch_id', 'product_id')
                            ->with('Batch:id,code');
                    }])
            ])
            ->withCount('PurchaseLines as total_products')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(price * quantity), 0)')
                    ->from('purchase_lines')
                    ->whereColumn('purchase_id', 'purchases.id');
            }, 'total')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(amount), 0)')
                    ->from('partial_payments_of_purchases')
                    ->whereColumn('purchase_id', 'purchases.id');
            }, 'total_paid')
            ->selectSub(function ($query) {
                $query->from('purchases as inv')
                    ->selectRaw('COALESCE((SELECT SUM(il.price * il.quantity) FROM purchase_lines as il WHERE il.purchase_id = inv.id), 0) - COALESCE((SELECT SUM(pp.amount) FROM partial_payments_of_purchases as pp WHERE pp.purchase_id = inv.id), 0)')
                    ->where('inv.id', '=', DB::raw('purchases.id'));
            }, 'total_for_pay')
            ->when($state, function ($query, $state) {
                return $query->where('status', $state);
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
     * find a Purchase by id.
     *
     * @param  int $id
     * @return ?object
     */
    public function findById(int $id): ?object
    {
        return $this->PurchaseModel->select('id', 'code', 'provider_id', 'status', 'created_at', 'user_id')
            ->with([
                'Provider' => fn ($q) => $q->select('id', 'name', 'last_name', 'document_number', 'city_id', 'document_type_id', 'address', 'phone', 'email')
                    ->selectRaw('CONCAT(name, " ", last_name) as full_name')
                    ->with(['City' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->with(['DocumentType' => function ($query) {
                        $query->select('id', 'name', 'code');
                    }]),
                'PurchaseLines' => fn ($q) => $q->select(
                    'id',
                    'price',
                    'quantity',
                    'purchase_id',
                    'product_id',
                    // Añadir el cálculo del total de la línea directamente en el select
                    DB::raw('COALESCE(price * quantity, 0) as total_line')
                )
                    ->with(['Product:id,sku,name,description', 'Entrance' => function ($q) {
                        $q->select('id', 'purchase_line_id', 'quantity', 'batch_id', 'product_id')
                            ->with('Batch:id,code');
                    }]),
                'PartialPaymentsOfPurchase' => function ($query) {
                    $query->select('id', 'amount', 'purchase_id', 'created_at', 'evidence', 'description')
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
            ->withCount('PurchaseLines as total_products')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(price * quantity), 0)')
                    ->from('purchase_lines')
                    ->whereColumn('purchase_id', 'purchases.id');
            }, 'total')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(amount), 0)')
                    ->from('partial_payments_of_purchases')
                    ->whereColumn('purchase_id', 'purchases.id');
            }, 'total_paid')
            ->selectSub(function ($query) {
                $query->from('purchases as inv')
                    ->selectRaw('COALESCE((SELECT SUM(il.price * il.quantity) FROM purchase_lines as il WHERE il.purchase_id = inv.id), 0) - COALESCE((SELECT SUM(pp.amount) FROM partial_payments_of_purchases as pp WHERE pp.purchase_id = inv.id), 0)')
                    ->where('inv.id', '=', DB::raw('purchases.id'));
            }, 'total_for_pay')
            ->where('id', $id)
            ->first();
    }


    /**
     * Find a PurchaseLine by code.
     *
     * @param  string $code
     * @return ?object
     */
    public function findByCode(string $code): ?object
    {
        return $this->PurchaseModel->where('code', $code)->first();
    }

    /**
     * Total amount for month where state is paid.
     *
     */
    public function totalAmountForMonth(): float
    {
        $result = $this->PurchaseModel
            ->selectRaw('COALESCE(SUM(purchase_lines.price * purchase_lines.quantity), 0) as total')
            ->join('purchase_lines', 'purchases.id', '=', 'purchase_lines.purchase_id')
            /* ->where('purchases.status', 'PAID') */
            ->whereMonth('purchases.created_at', now()->format('m'))
            ->first();


        // Verificar si $result es null y devolver 0 en ese caso
        return $result ? (float) $result->total : 0;
    }
}
