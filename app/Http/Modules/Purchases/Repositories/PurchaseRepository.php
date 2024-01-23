<?php

namespace App\Http\Modules\Purchases\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Purchases\Models\Purchase;
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
        return $this->PurchaseModel->select('id', 'code', 'provider_id', 'status', 'created_at')
            ->with([
                'Provider' => fn ($q) => $q->select('id', 'name', 'last_name')
                    ->selectRaw('CONCAT(name, " ", last_name) as full_name'),
                'PurchaseLines' => fn ($q) => $q->select('id', 'price', 'quantity', 'purchase_id', 'product_id')
                    ->with(['Product:id,sku,name,description', 'Entrance' => function ($q) {
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
