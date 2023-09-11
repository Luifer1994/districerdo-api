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
     * @param int $batchId
     * @return ?Inventory
     */
    public function findInventoryByProductIdAndBatchId(int $productId, int $batchId): ?Inventory
    {
        return $this->InventoryModel
            ->where('product_id', $productId)
            ->where('batch_id', $batchId)
            ->first();
    }
}
