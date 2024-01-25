<?php

namespace App\Http\Modules\Purchases\Repositories;


use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Purchases\Models\PartialPaymentsOfPurchase;

class PartialPaymentsOfIPurchaseRepository extends RepositoryBase
{
    protected  $PartialPaymentsOfPurchase;

    public function __construct(PartialPaymentsOfPurchase $PartialPaymentsOfPurchase)
    {
        parent::__construct($PartialPaymentsOfPurchase);
        $this->PartialPaymentsOfPurchase = $PartialPaymentsOfPurchase;
    }
}
