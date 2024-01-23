<?php

namespace App\Http\Modules\Invoices\Repositories;


use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Invoices\Models\PartialPaymentsOfInvoice;

class PartialPaymentsOfInvoiceRepository extends RepositoryBase
{
    protected  $PartialPaymentsOfInvoiceModel;

    public function __construct(PartialPaymentsOfInvoice $PartialPaymentsOfInvoiceModel)
    {
        parent::__construct($PartialPaymentsOfInvoiceModel);
        $this->PartialPaymentsOfInvoiceModel = $PartialPaymentsOfInvoiceModel;
    }
}
