<?php

namespace App\Http\Modules\Invoices\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Invoices\Models\InvoiceLine;

class InvoiceLineRepository extends RepositoryBase
{
    protected  $InvoiceLineModel;

    public function __construct(InvoiceLine $InvoiceLineModel)
    {
        parent::__construct($InvoiceLineModel);
        $this->InvoiceLineModel = $InvoiceLineModel;
    }
}
