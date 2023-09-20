<?php

namespace App\Http\Modules\Inventories\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Inventories\Models\Inventory;

class InventoryRepository extends RepositoryBase
{

    public function __construct(protected Inventory $InventoryModel)
    {
        parent::__construct($InventoryModel);
    }

    /**
     * Find Inventory by product id and batch id.
     *
     * @param int $productId
     * @param string $batchCode
     * @return ?Inventory
     */
    public function findInventoryByProductIdAndBatchCode(int $productId, string $batchCode): ?Inventory
    {
        return $this->InventoryModel
            ->where('product_id', $productId)
            ->with(['batch:id,code'])
            ->whereHas('batch', function ($query) use ($batchCode) {
                $query->where('code', $batchCode);
            })
            ->first();
    }
}
