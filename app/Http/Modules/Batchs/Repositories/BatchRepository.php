<?php

namespace App\Http\Modules\Batchs\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Batchs\Models\Batch;

class BatchRepository extends RepositoryBase
{

    public function __construct(protected Batch $BatchModel)
    {
        parent::__construct($BatchModel);
    }

    /**
     * Find a Batch by code.
     *
     * @param  string $code
     * @return ?object
     */
    public function findByCode(string $code): ?object
    {
        return $this->BatchModel->where('code', $code)->first();
    }
}
