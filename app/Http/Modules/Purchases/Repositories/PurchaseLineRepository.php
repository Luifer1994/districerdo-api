<?php

namespace App\Http\Modules\Purchases\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Purchases\Models\PurchaseLine;

class PurchaseLineRepository extends RepositoryBase
{

    public function __construct(protected PurchaseLine $PurchaseLineModel)
    {
        parent::__construct($PurchaseLineModel);
    }
}
