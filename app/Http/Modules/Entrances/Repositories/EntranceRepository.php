<?php

namespace App\Http\Modules\Entrances\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Entrances\Models\Entrance;

class EntranceRepository extends RepositoryBase
{

    public function __construct(protected Entrance $EntranceModel)
    {
        parent::__construct($EntranceModel);
    }

    /**
     * Find Entrance by product id and batch id.
     *
     * @param int $productId
     * @param int $batchId
     * @return ?Entrance
     */
    public function findEntranceByProductIdAndBatchId(int $productId, int $batchId): ?Entrance
    {
        return $this->EntranceModel
            ->where('product_id', $productId)
            ->where('batch_id', $batchId)
            ->first();
    }
}
