<?php

namespace App\Http\Modules\Outputs\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Outputs\Models\OutputInvoiceLine;

class OutputInvoiceLineRepository extends RepositoryBase
{
    public function __construct(protected OutputInvoiceLine $OutputInvoiceLineModel)
    {
        parent::__construct($OutputInvoiceLineModel);
    }
}
